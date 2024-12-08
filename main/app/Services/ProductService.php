<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Producer;
use App\Models\Product;
use App\Models\Image;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProductService
{
	protected Product $product;

	/**
	 * @param Product $product
	 * @return void
	 */
	public function setProduct(Product $product)
	{
		$this->product = $product;
	}

	/**
	 * @return Product
	 * @throws \Throwable
	 */
	public function storeProduct()
	{
		try {
			\DB::beginTransaction();

			$producer = Producer::findOrFail(request()->input('producer_id'));

			$team = $producer->team;

			$this->checkProduct();
			$this->checkPermission($team);

			$data = json_decode(request()->input('product'), true);

			$composition = [];

			if ($data['composition']) {
				$composition = array_values(
					collect($data['composition'])
						->filter(fn($ingredient) => !\Arr::exists($ingredient, "to_delete"))
						->map(function($ingredient) {
							unset ($ingredient['is_new']);
							return $ingredient;
						})
						->toArray()
				);
			}

			$this->product->fill([
				'producer_id' => $producer->id,
				'title' => $data['title'],
				'price' => $data['price'],
				'amount' => !$data['amount'] ? 0 : $data['amount'],
				'composition' => $composition
			]);

			$committedImageThumbnailIndex = request()->input('committed_image_thumbnail_index');

			if ($committedImageThumbnailIndex === null) {
				$this->product->fill([
					'thumbnail_id' => $data['thumbnail_id'] ?? null
				]);
			}

			$this->product->save();

			$tagIds = collect($data['tags'])->pluck('id');
			$this->product->tags()->attach($tagIds);

			// todo - validate that incoming file is a picture
			$committedImages = request()->file('images');

			if ($committedImages) {
				$basePath = 'team_' . $team->id . '/product_images';

				foreach ($committedImages as $index => $image) {
					$path = Storage::disk('public')->putFile(
						$basePath,
						$image
					);

					$image = Image::create([
						'imageable_id' => $this->product->id,
						'imageable_type' => Product::class,
						'path' => $path
					]);

					if (
						$committedImageThumbnailIndex !== null &&
						(int)$committedImageThumbnailIndex === $index
					) {
						$this->product->update([
							'thumbnail_id' => $image->id
						]);
					}
				}
			}

			\DB::commit();
		} catch (\Throwable $e) {
			\DB::rollBack();
			throw new \Exception($e->getMessage());
		}

		return $this->product;
	}

	/**
	 * @return Product
	 * @throws \Throwable
	 */
	public function updateProduct()
	{
		// todo - check for maximum amount of pictures & tags
		try {
			\DB::beginTransaction();

			$this->checkProduct();
			$this->checkPermission($this->product->producer->team);

			$data = json_decode(request()->input('product'), true);

			$this->product->fill([
				'producer_id' => $this->product->producer->team->detailed_id,
				'title' => $data['title'],
				'price' => $data['price'],
				'amount' => !$data['amount'] ? 0 : $data['amount'],
				'thumbnail_id' => $data['thumbnail_id'],
			]);

			$tagIds = collect($data['tags'])->pluck('id');
			$this->product->tags()->sync($tagIds);

			if (\Arr::exists($data, 'composition')) {
				$composition = array_values(
					collect($data['composition'])
						->filter(fn($ingredient) => !\Arr::exists($ingredient, 'to_delete'))
						->map(function($ingredient) {
							unset ($ingredient['is_new']);
							return $ingredient;
						})
						->toArray()
				);

				$this->product->fill([
					'composition' => $composition
				]);
			}

			// todo - validate that incoming file is a picture
			$removeImages = collect([]);

			if ($data['images']) {
				$removeImages = collect($data['images'])
					->filter(fn($image) => array_key_exists('to_delete', $image));
			}

			if ($removeImages->isNotEmpty()) {
				$removeImagePaths = $removeImages->map(fn($image) => $image['path']);
				Storage::disk('public')->delete($removeImagePaths->toArray());

				$removeImageIds = $removeImages->map(fn($image) => $image['id']);
				Image::destroy($removeImageIds->toArray());

				if ($this->product->thumbnail_id && $removeImageIds->contains($this->product->thumbnail_id)) {
					$this->product->fill([
						'thumbnail_id' => null
					]);
				}
			}

			$committedImages = request()->file('images');
			$committedImageThumbnailIndex = request()->input('committed_image_thumbnail_index');

			if ($committedImages) {
				$basePath = 'team_' . $this->product->producer->team->id . '/product_images';

				foreach ($committedImages as $index => $image) {
					$path = Storage::disk('public')->putFile(
						$basePath,
						$image
					);

					$image = Image::create([
						'imageable_id' => $this->product->id,
						'imageable_type' => Product::class,
						'path' => $path
					]);

					if (
						$committedImageThumbnailIndex !== null &&
						(int)$committedImageThumbnailIndex === $index
					) {
						$this->product->fill([
							'thumbnail_id' => $image->id
						]);
					}
				}
			}

			$this->product->save();

			\DB::commit();
		} catch (\Throwable $e) {
			\DB::rollBack();
			throw new \Exception($e->getMessage());
		}

		return $this->product;
	}

	/**
	 * @return bool|null
	 * @throws \Throwable
	 */
	public function deleteProduct()
	{
		try {
			$images = $this->product->images;

			if ($images->isNotEmpty()) {
				Storage::disk('public')->delete(
					$images->pluck('path')->toArray()
				);
			}

			$this->product->tags()->detach();
			$this->product->images()->delete();

			$isProductDeleted = $this->product->delete();

			\DB::commit();
		} catch (\Throwable $e) {
			\DB::rollBack();
			throw new \Exception($e->getMessage());
		}

		return $isProductDeleted;
	}

	/**
	 * @return Product
	 */
	public function getProduct()
	{
		return $this->product;
	}

	/**
	 * @return void
	 * @throws \Exception
	 */
	private function checkPermission(Team $team)
	{
		/** @var User $user */
		$user = auth()->user();

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_PRODUCT['name'], $team) &&
			!$user->owns($team)
		)
			throw new \Exception('Доступ закрыт');
	}

	/**
	 * @return void
	 * @throws \Exception
	 */
	private function checkProduct()
	{
		if (!$this->product->exists())
			throw new \Exception('Продукт не задан');
	}
}

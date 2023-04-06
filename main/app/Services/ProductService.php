<?php

namespace App\Services;

use App\Models\Permission;
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

			$team = Team::find(request()->input('team_id'));

			$this->checkPermission($team);

			$changes = json_decode(request()->input('changes'),true);

			$data = json_decode(request()->input('product'), true);

			$this->product->fill([
				'producer_id' => $team->detailed_id
			]);

			if ($changes['common']) {
				$this->product->fill([
					'title' => $data['title'],
					'price' => $data['price'],
					'amount' => $data['amount']
				]);
			}

			if ($changes['composition']) {
				$composition = array_values(
					collect($data['composition'])
						->filter(fn($ingredient) => !\Arr::exists($ingredient, "to_delete"))
						->toArray()
				);

				$this->product->fill([
					'composition' => $composition
				]);
			}

			if ($changes['images']) {
				$basePath = 'team_' . $team->id . '/product_images';

				$committedImages = request()->file('images');

				if ($committedImages) {
					foreach ($committedImages as $image) {

						$path = Storage::disk('public')->putFile(
							$basePath,
							$image
						);

						Image::create([
							'imageable_id' => $this->product->id,
							'imageable_type' => Product::class,
							'path' => $path
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

	// todo - request validation
	/**
	 * @return void
	 * @throws \Throwable
	 */
	public function syncProductCommonSettings()
	{
		$this->checkProduct();
		$this->checkPermission($this->product->producer->team);

		try {
			\DB::beginTransaction();
			$data = json_decode(request()->input('product'), true);

			$this->product->update([
				'title' => $data['title'],
				'price' => $data['price'],
				'amount' => $data['amount']
			]);
			\DB::commit();
		} catch (\Throwable) {
			\DB::rollBack();
			throw new \Exception('Не удалось обновить общие настройки продукта');
		}
	}


	/**
	 * @return void
	 * @throws \Throwable
	 */
	public function syncProductComposition()
	{
		$this->checkProduct();
		$this->checkPermission($this->product->producer->team);

		try {
			\DB::beginTransaction();
			$data = json_decode(request()->input('product'), true);

			$composition = array_values(
				collect($data['composition'])
					->filter(fn($ingredient) => !\Arr::exists($ingredient, "to_delete"))
					->toArray()
			);

			$this->product->update([
				'composition' => $composition
			]);

			\DB::commit();
		} catch (\Throwable) {
			\DB::rollBack();
			throw new \Exception('Не удалось обновить состав продукта');
		}
	}

	/**
	 * @return void
	 * @throws \Throwable
	 */
	public function syncProductImages()
	{
		$this->checkProduct();
		$this->checkPermission($this->product->producer->team);

		try {
			$basePath = 'team_' . $this->product->producer->team->id . '/product_images';

			$data = json_decode(request()->input('product'), true);

			$removeImages = collect([]);

			if ($data['images']) {
				$removeImages = collect($data['images'])
					->filter(fn($image) => array_key_exists('to_delete', $image));
			}

			if ($removeImages->isNotEmpty()) {
				Storage::disk('public')->delete(
					$removeImages->map(fn($image) => $image['path'])
						->toArray()
				);

				Image::destroy(
					$removeImages->map(fn($image) => $image['id'])
						->toArray()
				);
			}

			$committedImages = request()->file('images');

			if ($committedImages) {
				foreach ($committedImages as $image) {

					$path = Storage::disk('public')->putFile(
						$basePath,
						$image
					);

					Image::create([
						'imageable_id' => $this->product->id,
						'imageable_type' => Product::class,
						'path' => $path
					]);
				}
			}

			\DB::commit();
		} catch (\Throwable) {
			\DB::rollBack();
			throw new \Exception('Не удалось обновить изображения продукта');
		}
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
		$user = auth('sanctum')->user();

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

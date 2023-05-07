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

			$this->checkProduct();

			$team = Team::find(request()->input('team_id'));

			$this->checkPermission($team);

			$data = json_decode(request()->input('product'), true);

			$this->product->fill([
				'producer_id' => $team->detailed_id,
				'title' => $data['title'],
				'price' => $data['price'],
				'amount' => !$data['amount'] ? 0 : $data['amount']
			]);

			if ($data['composition']) {
				$composition = array_values(
					collect($data['composition'])
						->filter(fn($ingredient) => !\Arr::exists($ingredient, "to_delete"))
						->toArray()
				);

				$this->product->fill([
					'composition' => $composition
				]);
			}

			$this->product->save();

			// todo - validate that incoming file is a picture
			if ($data['images']) {
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
		try {
			\DB::beginTransaction();

			$this->checkProduct();
			$this->checkPermission($this->product->zxc->team);

			$data = json_decode(request()->input('product'), true);

			$this->product->fill([
				'producer_id' => $this->product->producer->team->detailed_id,
				'title' => $data['title'],
				'price' => $data['price'],
				'amount' => !$data['amount'] ? 0 : $data['amount']
			]);

			$composition = [];

			if ($data['composition']) {
				$composition = array_values(
					collect($data['composition'])
						->filter(fn($ingredient) => !\Arr::exists($ingredient, "to_delete"))
						->toArray()
				);
			}

			$this->product->fill([
				'composition' => $composition
			]);

			$this->product->save();

			// todo - validate that incoming file is a picture
			if ($data['images']) {
				$basePath = 'team_' . $this->product->producer->team->id . '/product_images';

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

			\DB::commit();
		} catch (\Throwable $e) {
			\DB::rollBack();
			throw new \Exception($e->getMessage());
		}

		return $this->product;
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

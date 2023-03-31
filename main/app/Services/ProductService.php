<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductImage;
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
	 * @return void
	 * @throws \Throwable
	 */
	public function syncProductImages()
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		if (!$this->product->exists())
			throw new \Exception('Продукт не задан');

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_PRODUCT['name'], $this->product->producer->team) &&
			!$user->owns($this->product->producer->team)
		)
			throw new \Exception('Доступ закрыт');

		try {
			$basePath = 'team_' . $this->product->producer->team->id . '/product_images';

			$product = json_decode(request()->input('product'), true);

			$removeImages = collect([]);

			if ($product['images']) {
				$removeImages = collect($product['images'])
					->filter(fn($image) => array_key_exists('to_delete', $image));
			}

			if ($removeImages->isNotEmpty()) {
				Storage::disk('public')->delete(
					$removeImages->map(fn($image) => $image['path'])
						->toArray()
				);

				ProductImage::destroy(
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

					ProductImage::create([
						'product_id' => $this->product->id,
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


	// todo - request validation

	/**
	 * @return void
	 * @throws \Throwable
	 */
	public function syncProductCommonSettings()
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		if (!$this->product->exists())
			throw new \Exception('Продукт не задан');

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_PRODUCT['name'], $this->product->producer->team) &&
			!$user->owns($this->product->producer->team)
		)
			throw new \Exception('Доступ закрыт');

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
		/** @var User $user */
		$user = auth('sanctum')->user();

		if (!$this->product->exists())
			throw new \Exception('Продукт не задан');

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_PRODUCT['name'], $this->product->producer->team) &&
			!$user->owns($this->product->producer->team)
		)
			throw new \Exception('Доступ закрыт');

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
	 * @return Product
	 */
	public function getProduct()
	{
		return $this->product;
	}
}

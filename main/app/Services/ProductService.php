<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Http\Request;
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

	// todo - image deletion
	/**
	 * @return void
	 * @throws \Exception
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

		$basePath = 'team_' . $this->product->producer->team->id . '/product_images';

		$committedImages = request()->input('product.committed_images');

		if ($committedImages) {
			foreach ($committedImages as $arImage) {

				$path = Storage::disk('public')->putFile(
					$basePath,
					base64_decode($arImage['src'])
				);

				ProductImage::create([
					'product_id' => $this->product->id,
					'path' => $path
				]);
			}
		}

	}


	// todo - request validation
	/**
	 * @return void
	 * @throws \Exception
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

		$this->product->update([
			'title' => request()->input('product.title'),
			'price' => request()->input('product.price'),
			'amount' => request()->input('product.amount')
		]);
	}

	/**
	 * @return void
	 * @throws \Exception
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

		$composition = array_values(
			collect(request()->input('composition'))
				->filter(fn($ingredient) => !\Arr::exists($ingredient, "to_delete"))
				->toArray()
		);

		$this->product->update([
			'composition' => $composition
		]);
	}
}

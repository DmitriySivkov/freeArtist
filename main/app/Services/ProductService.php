<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Producer;
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

	/**
	 * @param Product $product
	 * @param Request $request
	 * @return ProductImage|\Illuminate\Database\Eloquent\Model
	 */
	public function syncProductImages(Product $product, Request $request)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_PRODUCT['name'], $product->producer->team) &&
			!$user->owns($product->producer->team)
		)
			throw new \LogicException('Доступ закрыт');

		$basePath = 'team_' . $product->producer->team->id . '/product_images';
		if ($request->hasFile('image')) {
			$path = Storage::disk('public')->putFile(
				$basePath,
				$request->file('image')
			);
		} else {
			@list($type, $fileData) = explode(';', $request->getContent());
			@list(, $fileData) = explode(',', $fileData);

			$extension = explode('/', $type);
			$path = $basePath . '/' . \Str::random(15) . '.' . end($extension);

			Storage::disk('public')->put(
				$path,
				base64_decode($fileData)
			);
		}

		return ProductImage::create([
			'product_id' => $product->id,
			'path' => $path
		]);
	}

	/**
	 * @param Product $product
	 * @param Request $request
	 * @return void
	 */
	public function syncProductCommonSettings(Product $product, Request $request)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_PRODUCT['name'], $product->producer->team) &&
			!$user->owns($product->producer->team)
		)
			throw new \LogicException('Доступ закрыт');

		$product->update([
			'title' => $request->input('settings.title'),
			'price' => $request->input('settings.price'),
			'amount' => $request->input('settings.amount')
		]);
	}

	/**
	 * @param Product $product
	 * @param Request $request
	 * @return array
	 */
	public function syncProductComposition(Product $product, Request $request)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_PRODUCT['name'], $product->producer->team) &&
			!$user->owns($product->producer->team)
		)
			throw new \LogicException('Доступ закрыт');

		$composition = array_values(
			collect($request->get('composition'))
				->filter(function($ingredient) {
					return !array_key_exists("to_delete", $ingredient);
				})
				->map(function($ingredient) {
					return [
						"name" => $ingredient["name"],
						"description" => $ingredient["description"]
					];
				})
				->toArray()
		);

		$product->update([
			'composition' => $composition
		]);

		return $composition;
	}
}

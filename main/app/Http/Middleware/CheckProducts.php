<?php

namespace App\Http\Middleware;

use App\Exceptions\OrderInvalidItemsException;
use App\Services\Orders\UserOrderService;
use Closure;
use Illuminate\Http\Request;

class CheckProducts
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
	 * @return mixed
	 * @throws OrderInvalidItemsException
	 */
    public function handle(Request $request, Closure $next)
    {
		$service = new UserOrderService(); // could not resolve from binding due to app flow order

		$invalidProducts = $service->findInvalidProducts($request->input('products'));

		if ($invalidProducts->isNotEmpty()) {
			throw new OrderInvalidItemsException(
				$invalidProducts->map(fn(\App\Models\Product $product) => [
					'id' 			=> $product->id,
					'title' 		=> $product->title,
					'amount' 		=> $product->is_active ? $product->amount : 0,
					'price'			=> $product->price,
					'producer'		=> $product->producer->team->display_name,
					'producer_id'	=> $product->producer->id
				])
					->values()
					->toArray(),
				'Изменились стоимость или доступное количество продуктов');
		}

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FilterEmptyCartProducts
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
	 * @return mixed
	 */
    public function handle(Request $request, Closure $next)
    {
		$input = $request->all();

		$input['products'] = collect($input['products'])
			->filter(fn($product) => $product['amount'] !== 0)
			->values()
			->toArray();

		if (count($input['products']) === 0) {
			throw new \LogicException('Попытка заказа пустого списка продуктов');
		}

		$request->replace($input);

        return $next($request);
    }
}

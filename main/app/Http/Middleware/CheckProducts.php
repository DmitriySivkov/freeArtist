<?php

namespace App\Http\Middleware;

use App\Services\Orders\UserOrderService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProducts
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
	 * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
	 */
    public function handle(Request $request, Closure $next)
    {
		$service = new UserOrderService(); // could not resolve from binding as per app flow order

		$invalidProducts = $service->findInvalidProducts($request->input('products'));

		if ($invalidProducts->isNotEmpty()) {
			return response([
				'message' => 'Ой, кажется кто-то вас опередил',
				'invalid_items' => $invalidProducts->map(fn(\App\Models\Product $product) => [
					'id' => $product->id,
					'title' => $product->title,
					'amount' => $product->is_active ? $product->amount : 0
				])->values(),
			], Response::HTTP_UNPROCESSABLE_ENTITY);
		}

        return $next($request);
    }
}

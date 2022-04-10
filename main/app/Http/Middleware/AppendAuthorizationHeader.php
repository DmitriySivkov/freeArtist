<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AppendAuthorizationHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
		if (!$request->hasHeader('Authorization') || $request->header('Authorization') !== 'Bearer ' . $request->cookie('token'))
			$request->headers->set('Authorization', 'Bearer ' . $request->cookie('token'));
        return $next($request);
    }
}

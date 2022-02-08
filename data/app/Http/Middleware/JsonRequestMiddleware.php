<?php

namespace App\Http\Middleware;

use Closure;

class JsonRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('post') && !$request->isJson()) {
            return response('Invalid data type. JSON expected', 400);
        }

        return $next($request);
    }
}

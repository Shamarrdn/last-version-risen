<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MethodOverrideMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('_method')) {
            $method = strtoupper($request->input('_method'));
            if (in_array($method, ['PUT', 'PATCH', 'DELETE'])) {
                $request->setMethod($method);
            }
        }

        return $next($request);
    }
}

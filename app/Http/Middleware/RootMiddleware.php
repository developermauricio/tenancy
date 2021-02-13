<?php

namespace App\Http\Middleware;

use Closure;

class RootMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $website = app(\Hyn\Tenancy\Environment::class)->website();
        if ($website) {
            abort(401, "Acceso denegado a esta zona");
        }
        return $next($request);
    }
}

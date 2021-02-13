<?php

namespace App\Http\Middleware;

use Closure;

class TenancyCheckMiddleware
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
        /**
         * TROZOS DE LA URL POR PUNTOS (SUBDOMINIO)
         */
        $url_array = explode('.', parse_url(request()->url(), PHP_URL_HOST));
        $site = sprintf("%s.%s", $url_array[0], $url_array[1]);

        /**
         * SI ES UN SUBDOMINIO PERO NO EXISTE EN NUESTRA BASE DE DATOS NO DEJAMOS ACCEDER
         */
        if ($site !== env("APP_DOMAIN")) {
            // WEBSITE ACTUAL
            $website = app(\Hyn\Tenancy\Environment::class)->website();
            if(!$website) {
                return redirect('//' . env('APP_DOMAIN'));
            } else {
                Config::set('database.default', 'tenant');
            }
        }
        return $next($request);
    }
}

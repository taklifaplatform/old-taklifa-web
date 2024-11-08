<?php

namespace Webkul\Marketplace\Http\Middleware;

use Closure;

class Marketplace
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        abort_if(! core()->getConfigData('marketplace.settings.general.status'), 404);

        return $next($request);
    }
}

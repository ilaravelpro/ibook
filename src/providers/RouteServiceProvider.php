<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/02/05 Fri 06:39 AM IRST
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

namespace iLaravel\iBook\Providers;

use Illuminate\Routing\Router;

class RouteServiceProvider extends \Illuminate\Foundation\Support\Providers\RouteServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    public function register()
    {
        parent::register();
    }
    public function map(Router $router)
    {
        $this->apiRoutes($router);
    }

    public function apiRoutes(Router $router)
    {
        $router->group([
            'namespace' => '\iLaravel\iBook\iApp\Http\Controllers\API',
            'prefix' => 'api',
            'middleware' => 'api'
        ], function ($router) {
            require_once(ibook_path('routes/api.php'));
        });
    }
}

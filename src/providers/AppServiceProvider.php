<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/02/05 Fri 06:39 AM IRST
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

namespace iLaravel\iBook\Providers;

class AppServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if($this->app->runningInConsole())
        {
            $this->loadMigrationsFrom(ibook_path('migrations'));
        }
        $this->mergeConfigFrom(ibook_path('config/book.php'), 'ilaravel.main.ibook');
    }

    public function register()
    {
        parent::register();
    }
}

<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url): void
    {
        if (app()->isProduction()) {
            $url->forceScheme('https');
        } else {
            // DB::listen(function ($query) {
            //     $sql = $query->sql;
            //     for ($i = 0; $i < count($query->bindings); $i++) {
            //         $sql = preg_replace("/\?/", $query->bindings[$i], $sql, 1);
            //     }
            //     Log::info($sql);
            // });
        }
    }
}

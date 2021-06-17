<?php

namespace App\Providers;

use App\Models\Repositories\CommonRepositoryServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(CommonRepositoryServiceProvider::class);
    }

    public function boot()
    {
        Relation::morphMap([
            'employee' => 'App\Models\Employee',
            'user' => 'App\Models\User',
        ]);
    }
}

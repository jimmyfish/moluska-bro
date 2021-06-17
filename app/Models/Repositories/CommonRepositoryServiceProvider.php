<?php

namespace App\Models\Repositories;

use App\Models\Repositories\Configuration\ConfigurationRepository;
use App\Models\Repositories\Configuration\ConfigurationRepositoryInterface;
use App\Models\Repositories\Employee\EmployeeRepository;
use App\Models\Repositories\Employee\EmployeeRepositoryInterface;
use App\Models\Repositories\EmployeeRequest\EmployeeRequestRepository;
use App\Models\Repositories\EmployeeRequest\EmployeeRequestRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class CommonRepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            EmployeeRequestRepositoryInterface::class,
            EmployeeRequestRepository::class
        );

        $this->app->bind(
            EmployeeRepositoryInterface::class,
            EmployeeRepository::class
        );
        
        $this->app->bind(
            ConfigurationRepositoryInterface::class,
            ConfigurationRepository::class
        );
    }
}
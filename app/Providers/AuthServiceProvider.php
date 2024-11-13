<?php

namespace App\Providers;

use App\Models\Business;
use App\Policies\BusinessPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Business::class => BusinessPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}

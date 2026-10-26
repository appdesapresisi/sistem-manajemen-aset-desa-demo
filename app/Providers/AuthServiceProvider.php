<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Bidang;
use App\Models\Inventaris;
use App\Models\JenisInventaris;
use App\Models\Pejabat;
use App\Models\Ruangan;
use App\Models\Dropdown;
use App\Models\User;
use App\Models\ExportLog;
use App\Policies\BidangPolicy;
use App\Policies\InventarisPolicy;
use App\Policies\JenisInventarisPolicy;
use App\Policies\PejabatPolicy;
use App\Policies\RuanganPolicy;
use App\Policies\UserPolicy;
use App\Policies\ExportLogPolicy;
use App\Policies\DropdownPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Bidang::class         => BidangPolicy::class,
        Inventaris::class     => InventarisPolicy::class,
        JenisInventaris::class => JenisInventarisPolicy::class,
        Pejabat::class        => PejabatPolicy::class,
        Ruangan::class        => RuanganPolicy::class,
        User::class           => UserPolicy::class,
        ExportLog::class      => ExportLogPolicy::class,
        Dropdown::class       => DropdownPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user && method_exists($user, 'isSuperDeveloper') && $user->isSuperDeveloper()) {
                return true;
            }
        });
    }
}

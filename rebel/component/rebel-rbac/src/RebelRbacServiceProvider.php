<?php

namespace Rebel\Component\Rbac;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Rebel\Component\Rbac\Contracts\Permission as PermissionContract;
use Rebel\Component\Rbac\Contracts\Role as RoleContract;

class RebelRbacServiceProvider extends ServiceProvider
{

    public function boot(GateContract $gate)
    {
        $this->publishes([
            __DIR__ . '/../config/rebel-rbac.php' => config_path('rebel-rbac.php'),
        ], 'config');

        // copying the migration file.
        if (!class_exists('CreateRbacTable')) {
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__ . '/../database/migrations/CreateRbacTable.php' => database_path('migrations/' . $timestamp . '_create_rbac_table.php'),
            ], 'migrations');
        }

        $gate = (new Bootstraper($gate, $this->app['cache.store']))->registerAbilities();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/rebel-rbac.php', 'rebel-rbac');
        $this->registerModelBinding();
        $this->registerBladeDirective();
    }

    protected function registerModelBinding()
    {
        $config = $this->app['config']->get('rebel-rbac');

        $this->app->bind(PermissionContract::class, $config['permissions']['model']);
        $this->app->bind(RoleContract::class, $config['roles']['model']);
    }

    protected function registerBladeDirective()
    {
        $this->app->afterResolving('blade.compiler', function(BladeCompiler $compiler) {
            $compiler->directive('canDo', function($role) {
                return "<?php if(auth()->check() && auth()->user()->hasRole{$role}): ?>";
            });
            $compiler->directive('endCanDo', function() {
                return "<?php endif; ?>";
            });
        });
    }

}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;

class PerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar configurações de performance
        $this->app->singleton('performance.config', function () {
            return [
                'cache_ttl' => config('cache.ttl', 300), // 5 minutos
                'query_log_enabled' => config('app.debug', false),
                'view_cache_enabled' => !config('app.debug', false),
            ];
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configurar cache de views em produção
        if (!config('app.debug')) {
            $this->enableViewCaching();
        }
        
        // Monitorar queries lentas em desenvolvimento
        if (config('app.debug')) {
            $this->enableQueryLogging();
        }
        
        // Compartilhar dados globais com views
        $this->shareGlobalViewData();
        
        // Configurar cache tags se disponível
        $this->configureCacheTags();
    }
    
    /**
     * Habilitar cache de views
     */
    private function enableViewCaching(): void
    {
        View::composer('*', function ($view) {
            $cacheKey = 'view_' . md5($view->getName() . serialize($view->getData()));
            
            if (!Cache::has($cacheKey)) {
                Cache::put($cacheKey, true, now()->addHours(1));
            }
        });
    }
    
    /**
     * Habilitar log de queries
     */
    private function enableQueryLogging(): void
    {
        DB::listen(function (QueryExecuted $query) {
            // Log queries que demoram mais de 100ms
            if ($query->time > 100) {
                Log::warning('Slow Query Detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                    'connection' => $query->connectionName,
                ]);
            }
        });
    }
    
    /**
     * Compartilhar dados globais com views
     */
    private function shareGlobalViewData(): void
    {
        View::share([
            'app_name' => config('app.name'),
            'app_version' => '1.0.0',
            'cache_enabled' => !config('app.debug'),
        ]);
    }
    
    /**
     * Configurar cache tags
     */
    private function configureCacheTags(): void
    {
        // Definir tags de cache para diferentes tipos de dados
        $this->app->bind('cache.tags', function () {
            return [
                'products' => 'products',
                'categories' => 'categories', 
                'brands' => 'brands',
                'filters' => 'filters',
            ];
        });
    }
}
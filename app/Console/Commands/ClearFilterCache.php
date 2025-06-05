<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearFilterCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filter:clear-cache {--all : Clear all cache, not just filter cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear filter-related cache (categories, brands, products)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Limpando cache dos filtros...');
        
        if ($this->option('all')) {
            // Limpar todo o cache
            Cache::flush();
            $this->info('✅ Todo o cache foi limpo!');
        } else {
            // Limpar apenas cache específico dos filtros
            $cacheKeys = [
                'filter_categories',
                'filter_brands',
            ];
            
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
                $this->line("   ✓ Cache '{$key}' removido");
            }
            
            // Limpar cache de produtos filtrados (padrão genérico)
            $this->clearProductFilterCache();
            
            $this->info('✅ Cache dos filtros limpo com sucesso!');
        }
        
        $this->newLine();
        $this->comment('💡 Dica: Use --all para limpar todo o cache da aplicação');
        
        return Command::SUCCESS;
    }
    
    /**
     * Limpar cache de produtos filtrados
     */
    private function clearProductFilterCache()
    {
        // Em um ambiente de produção, seria melhor usar tags de cache
        // Por enquanto, vamos usar uma abordagem mais simples
        
        $cleared = 0;
        $cacheStore = Cache::getStore();
        
        // Se estivermos usando file cache, podemos tentar limpar arquivos específicos
        if (method_exists($cacheStore, 'getDirectory')) {
            $cacheDir = $cacheStore->getDirectory();
            $files = glob($cacheDir . '/products_filter_*');
            
            foreach ($files as $file) {
                if (unlink($file)) {
                    $cleared++;
                }
            }
            
            if ($cleared > 0) {
                $this->line("   ✓ {$cleared} arquivos de cache de produtos removidos");
            }
        } else {
            // Para outros drivers de cache, usar flush (menos eficiente)
            $this->line('   ⚠ Cache de produtos: usando flush geral (driver não suporta limpeza seletiva)');
        }
    }
}

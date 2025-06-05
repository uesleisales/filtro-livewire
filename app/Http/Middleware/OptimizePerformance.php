<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class OptimizePerformance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): BaseResponse
    {
        $response = $next($request);
        
        // Aplicar otimizações apenas para respostas HTML
        if ($this->shouldOptimize($request, $response)) {
            $this->addCacheHeaders($response);
            $this->addSecurityHeaders($response);
            $this->optimizeContent($response);
        }
        
        return $response;
    }
    
    /**
     * Verificar se deve aplicar otimizações
     */
    private function shouldOptimize(Request $request, BaseResponse $response): bool
    {
        return $response instanceof Response &&
               $response->getStatusCode() === 200 &&
               str_contains($response->headers->get('Content-Type', ''), 'text/html');
    }
    
    /**
     * Adicionar headers de cache
     */
    private function addCacheHeaders(BaseResponse $response): void
    {
        // Cache para recursos estáticos por 1 hora
        $response->headers->set('Cache-Control', 'public, max-age=3600');
        $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
        
        // ETag para validação de cache
        $etag = md5($response->getContent());
        $response->headers->set('ETag', '"' . $etag . '"');
    }
    
    /**
     * Adicionar headers de segurança
     */
    private function addSecurityHeaders(BaseResponse $response): void
    {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
    
    /**
     * Otimizar conteúdo da resposta
     */
    private function optimizeContent(Response $response): void
    {
        $content = $response->getContent();
        
        if ($content) {
            // Minificar HTML (remover espaços desnecessários)
            $content = $this->minifyHtml($content);
            
            // Comprimir com gzip se suportado
            if (function_exists('gzencode') && 
                str_contains($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip')) {
                $compressed = gzencode($content, 6);
                if ($compressed !== false) {
                    $response->headers->set('Content-Encoding', 'gzip');
                    $response->headers->set('Content-Length', strlen($compressed));
                    $content = $compressed;
                }
            }
            
            $response->setContent($content);
        }
    }
    
    /**
     * Minificar HTML
     */
    private function minifyHtml(string $html): string
    {
        // Remover comentários HTML (exceto condicionais do IE)
        $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);
        
        // Remover espaços em branco desnecessários
        $html = preg_replace('/\s+/', ' ', $html);
        
        // Remover espaços ao redor de tags
        $html = preg_replace('/\s*(<[^>]+>)\s*/', '$1', $html);
        
        return trim($html);
    }
}
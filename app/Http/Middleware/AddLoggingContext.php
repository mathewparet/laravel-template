<?php
namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class AddLoggingContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->defineLoggingContext($request);

        $start = microtime(true);
        
        $response = $next($request);

        $this->logRequestIfEnabled($start);

        $response->headers->set('Request-Id', $request->requestId());

        return $response;
    }

    private function defineLoggingContext(Request $request)
    {
        Log::shareContext([
            'user-id' => $request->user()?->id,
            'request-id' => $requestId = Str::uuid()->toString(),
            'session-id' => $request->sessionId(),
            'endpoint' => $request->method() . ' ' . Str::start($request->path(), '/'),
        ]);

        $request->headers->set('Request-Id', $requestId);
    }

    private function logRequestIfEnabled(string|float $start)
    {
        $logUntil = config('logging.before') ? Carbon::parse(config('logging.before'), "Australia/Sydney") : now()->subMinute();

        if($logUntil->isFuture()) {
            Log::debug('Endpoint called', [
                'duration' => round((microtime(true) - $start) * 1000)
            ]);
        }
    }
}

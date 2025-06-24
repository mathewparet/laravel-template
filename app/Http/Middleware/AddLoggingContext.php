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

        $start = $this->captureExecutionStartTime();
        
        $response = $next($request);

        $this->logRequest($start);

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

    private function captureExecutionStartTime(): float
    {
        return microtime(true);
    }

    private function calculateExecutionTime(string|float $start): float
    {
        return (microtime(true) - $start) * 1000;
    }

    private function isLongExecuting(string|float $duration): bool
    {
        $alert_durations_beyond = config('logging.slow_endpoint_ms');

        return $alert_durations_beyond && $duration > $alert_durations_beyond;
    }

    private function getLogUntilTime(): Carbon
    {
        return Carbon::parse(config('logging.before'));
    }

    private function logRequest(string|float $start)
    {
        $logUntil = $this->getLogUntilTime();

        $executionTime = $this->calculateExecutionTime($start);

        if($logUntil->isFuture() || $this->isLongExecuting($executionTime)) {
            Log::info('Endpoint called', [
                'duration' => $executionTime
            ]);
        }
    }
}

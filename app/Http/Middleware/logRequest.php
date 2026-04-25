<?php

namespace App\Http\Middleware;

use App\Models\RequestLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class logRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $response = $next($request);
        $duration = microtime(true) - $startTime;
    
        RequestLog::create([
            'user_id' => $request->user()?->id,
            'method' => $request->method(),
            'endpoint' => $request->path(),
            'ip' => $request->ip(),
            'response_time_ms' => round($duration * 1000, 2),
            'status_code' => $response->getStatusCode(),
        ]);
    
        return $response;
    }
}
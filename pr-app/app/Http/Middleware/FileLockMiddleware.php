<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class FileLockMiddleware
{
    public function handle($request, Closure $next)
    {
        $lockKey = 'file-write-lock';

        // For write operations, acquire an exclusive lock and hold it for 2 seconds
        if ($request->isMethod('post')) {
            Cache::lock($lockKey, 10)->block(10, function () use ($next, $request) {
                // Simulate a longer write operation by holding the lock for 2 seconds
                return $next($request);
            });
        }

        // For read operations, check if a write lock is active without blocking
        if ($request->isMethod('get') && Cache::has($lockKey)) {
            return response()->json(['message' => 'File is being written to. Please try again.'], 429);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AutoJsonResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Jika request menginginkan JSON (SPA/Axios) dan response adalah View
        $isJsonRequest = $request->wantsJson() || $request->expectsJson() || $request->is('api/*');

        if ($isJsonRequest && isset($response->original) && $response->original instanceof \Illuminate\View\View) {
            // Konversi compact() data yang dilempar ke View menjadi JSON
            return response()->json($response->original->getData(), method_exists($response, 'status') ? $response->status() : 200);
        }

        return $response;
    }
}

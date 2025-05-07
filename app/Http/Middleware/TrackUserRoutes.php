<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check() && !$request->ajax() && !$request->isMethod('get')) {
            $currentRoute = [
                'url' => $request->url(),
                'name' => $request->route()->getName(),
                'accessed_at' => now()->toIso8601String(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            $recentRoutes = session()->get('recent_routes', []);

            $existingIndex = array_search($currentRoute['url'], array_column($recentRoutes, 'url'));

            if ($existingIndex !== false) {
                unset($recentRoutes[$existingIndex]);
            }

            array_unshift($recentRoutes, $currentRoute);

            $recentRoutes = array_slice($recentRoutes, 0, 5);

            session()->put('recent_routes', $recentRoutes);
        }

        return $response;
    }
}

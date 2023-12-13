<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $userRole): Response
    {
        if (!auth()->user()) {
            return redirect('/');
        }

        if (auth()->user()->type == $userRole) {

            $token = $request->cookies->get('token');

            $response = $next($request);

            $response->headers->set('Authorization', "Bearer $token");
            $response->headers->set('Accept', '*/*');

            return $response;
        }

        return redirect('/');
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (auth()->user()->role != Role::ADMIN) {
        //     abort(403);
        // }

        // if (auth()->user()->role!= Role::ADMIN &&!in_array($request->route()->getName(), ['orders.orderItems', 'orders.index'])) {
        //     abort(403);
        // }

        return $next($request);
    }
}

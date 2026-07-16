<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictHorizonAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('app/horizon*') && ! ($request->user()?->can('ViewHorizon') ?? false)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Support\Access;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleAccess
{
    public function handle(Request $request, Closure $next, string $module, string $action = 'view'): Response
    {
        abort_unless(Access::can($request->user(), $module, $action), 403);

        return $next($request);
    }
}

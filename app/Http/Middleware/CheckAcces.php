<?php

namespace App\Http\Middleware;

use Closure;

class CheckAcces
{
    public function handle($request, Closure $next, $access)
    {
        $akses = auth()->user()->menu->pluck('slug')->contains($access);
        if ($akses === false && ! auth()->user()->is_super()) {
            abort(401);
        }

        return $next($request);
    }
}

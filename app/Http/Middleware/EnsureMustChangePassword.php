<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMustChangePassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->must_change_password && ! $request->routeIs('admin.change-password.*', 'admin.logout')) {
            return redirect()->route('admin.change-password.show')
                ->with('warning', 'Password masih menggunakan password sementara. Segera ubah password.');
        }

        return $next($request);
    }
}

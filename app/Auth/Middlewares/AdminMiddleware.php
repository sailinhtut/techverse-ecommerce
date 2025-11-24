<?php

namespace App\Auth\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('shop.get');
        }

        $roleName = $user->role?->name;

        if (!$user->role?->is_company_member) {
            return redirect()->route('shop.get')->with('error', 'You do not have access to this area.');
        }

        if ($permission && !$user->hasPermissions([$permission])) {
            return redirect()->route('admin.dashboard.setting.get')->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/dashboard');
        }

        $user = Auth::user();
        
        // تحقق من أن المستخدم لديه دور admin أو superadmin
        if (!$user->hasRole(['admin', 'superadmin'])) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}

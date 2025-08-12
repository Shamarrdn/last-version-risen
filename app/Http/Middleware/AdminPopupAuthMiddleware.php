<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPopupAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $adminUsername = config('admin.popup_username', 'admin');
        $adminPassword = config('admin.popup_password', 'admin123');

        // Check if the Basic Authentication header is present
        if (
            !$request->hasHeader('PHP_AUTH_USER') || !$request->hasHeader('PHP_AUTH_PW') ||
            $request->header('PHP_AUTH_USER') !== $adminUsername ||
            $request->header('PHP_AUTH_PW') !== $adminPassword
        ) {

            $headers = [
                'WWW-Authenticate' => 'Basic realm="منطقة إدارة المتجر - تسجيل الدخول مطلوب"'
            ];

            return response('تسجيل الدخول مطلوب للوصول إلى لوحة التحكم', 401, $headers);
        }

        return $next($request);
    }
}

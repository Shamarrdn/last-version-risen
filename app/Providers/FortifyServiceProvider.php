<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::loginView(function () {
            if (request()->has('redirect')) {
                session(['url.intended' => request()->query('redirect')]);
            }
            return view('auth.login');
        });

        // تكوين RateLimiter الخاص بـ Fortify
        RateLimiter::for('login', function (Request $request) {
            $emailKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());
            $ipKey = 'ip_only|' . $request->ip();

            if (RateLimiter::tooManyAttempts($ipKey, 3)) {
                $seconds = RateLimiter::availableIn($ipKey);
                $message = 'لقد تجاوزت الحد المسموح من المحاولات. الرجاء المحاولة بعد ' . ceil($seconds / 60) . ' دقيقة.';

                return response()->view('errors.429', ['exception' => new \Exception($message)], 429);
            }

            RateLimiter::hit($ipKey, 60 * 2);

            return Limit::perMinute(5)
                ->by($emailKey)
                ->response(function () {
                    $seconds = RateLimiter::availableIn('login.' . request()->ip());
                    return back()->withErrors([
                        'email' => 'لقد تجاوزت الحد المسموح من المحاولات. الرجاء المحاولة بعد ' . ceil($seconds / 60) . ' دقيقة.'
                    ]);
                });
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::authenticateUsing(function ($request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                $redirect = session('url.intended');

                if ($redirect) {
                    config(['fortify.redirects.login' => $redirect]);
                    session()->forget('url.intended');
                } else {
                    // توجيه المستخدم بناءً على دوره
        if ($user->hasRole('superadmin')) {
            config(['fortify.home' => '/superadmin/dashboard']);
        } elseif ($user->hasRole('admin')) {
            config(['fortify.home' => '/admin/dashboard']);
        } else {
            config(['fortify.home' => '/dashboard']);
        }
                }

                return $user;
            }
        });
    }
}

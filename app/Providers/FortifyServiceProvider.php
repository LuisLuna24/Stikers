<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->instance(LoginResponse::class, new class implements LoginResponse
        {
            public function toResponse($request): RedirectResponse
            {
                return self::redirectByRole($request->user());
            }

            private static function redirectByRole(?User $user): RedirectResponse
            {
                abort_unless($user, 401);

                if ($user->hasAnyRole(['admin', 'vendedor', 'super-admin'])) {
                    return redirect()->route('admin.dashboard');
                }

                if ($user->hasRole('cliente')) {
                    return redirect()->route('customer.dashboard');
                }

                return Route::has('dashboard')
                    ? redirect()->route('dashboard')
                    : redirect()->route('home');
            }
        });

        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse
        {
            public function toResponse($request): RedirectResponse
            {
                $user = $request->user();

                abort_unless($user, 401);

                if ($user->hasAnyRole(['admin', 'vendedor', 'super-admin'])) {
                    return redirect()->route('admin.dashboard');
                }

                if ($user->hasRole('cliente')) {
                    return redirect()->route('customer.dashboard');
                }

                return Route::has('dashboard')
                    ? redirect()->route('dashboard')
                    : redirect()->route('home');
            }
        });

        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse
        {
            public function toResponse($request): RedirectResponse
            {
                return redirect()->route('home');
            }
        });
    }

    public function boot(): void
    {
        Fortify::authenticateUsing(function (Request $request): ?User {
            $user = User::where('email', $request->string('email')->toString())->first();

            if ($user && $user->status && Hash::check($request->string('password')->toString(), $user->password)) {
                return $user;
            }

            return null;
        });

        Fortify::verifyEmailView(fn() => view('pages.auth.verify-email'));
        Fortify::loginView(fn() => view('pages.auth.login'));
        Fortify::registerView(fn() => view('pages.auth.register'));
        Fortify::requestPasswordResetLinkView(fn() => view('pages.auth.forgot-password'));
        Fortify::resetPasswordView(fn($request) => view('pages.auth.reset-password', ['request' => $request]));
        Fortify::confirmPasswordView(fn() => view('pages.auth.confirm-password'));
        Fortify::twoFactorChallengeView(fn() => view('pages.auth.two-factor-challenge'));

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(
                Str::lower($request->string(Fortify::username())) . '|' . $request->ip(),
            );

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}

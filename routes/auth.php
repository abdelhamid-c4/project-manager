<?php

use App\Models\User;
use App\Models\UserTrustedDevice;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Symfony\Component\HttpFoundation\Response;

$loginSecurityChallenge = static function (): array {
    $siteKey = config('services.recaptcha.site_key');
    $secretKey = config('services.recaptcha.secret_key');
    $configured = filled($siteKey) && filled($secretKey);

    return [
        'recaptchaEnabled' => (bool) config('services.recaptcha.enabled', true),
        'recaptchaSiteKey' => $siteKey,
        'recaptchaConfigured' => $configured,
        'recaptchaLocalFallback' => ! $configured && app()->isLocal(),
    ];
};

$verifyLoginSecurityChallenge = static function (Request $request): void {
    if (! config('services.recaptcha.enabled', true)) {
        return;
    }

    $siteKey = config('services.recaptcha.site_key');
    $secretKey = config('services.recaptcha.secret_key');

    if (! filled($siteKey) || ! filled($secretKey)) {
        if (app()->isLocal()) {
            $request->validate([
                'local_security_check' => ['accepted'],
            ], [
                'local_security_check.accepted' => 'Please confirm the workspace security check before logging in.',
            ]);

            return;
        }

        throw ValidationException::withMessages([
            'g-recaptcha-response' => 'Security verification is not configured. Please contact the administrator.',
        ]);
    }

    $validated = $request->validate([
        'g-recaptcha-response' => ['required', 'string'],
    ], [
        'g-recaptcha-response.required' => 'Please complete the security verification before logging in.',
    ]);

    $response = Http::asForm()
        ->timeout(5)
        ->post(config('services.recaptcha.verify_url'), [
            'secret' => $secretKey,
            'response' => $validated['g-recaptcha-response'],
            'remoteip' => $request->ip(),
        ]);

    if (! $response->ok() || ! $response->json('success')) {
        throw ValidationException::withMessages([
            'g-recaptcha-response' => 'Security verification failed. Please try again.',
        ]);
    }
};

$continueLogin = static function (Request $request, User $user, bool $remember): Response {
    if (! $user->email_verified_at) {
        $user->forceFill(['email_verified_at' => now()])->save();
    }

    Auth::login($user, $remember);
    $request->session()->regenerate();

    return redirect()->intended(route('dashboard'));
};

$trustedDeviceCookieName = static fn (): string => (string) config('services.login_trusted_devices.cookie_name', 'pm_trusted_device');
$trustedDeviceLifetimeDays = static fn (): int => max(1, (int) config('services.login_trusted_devices.lifetime_days', 90));
$trustedDeviceLifetimeMinutes = static fn (): int => $trustedDeviceLifetimeDays() * 24 * 60;

$findTrustedDevice = static function (Request $request, User $user) use ($trustedDeviceCookieName, $trustedDeviceLifetimeDays): ?UserTrustedDevice {
    $token = $request->cookie($trustedDeviceCookieName());

    if (! is_string($token) || strlen($token) < 40) {
        return null;
    }

    $trustedDevice = UserTrustedDevice::query()
        ->where('user_id', $user->id)
        ->where('token_hash', hash('sha256', $token))
        ->where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })
        ->first();

    if (! $trustedDevice) {
        return null;
    }

    $trustedDevice->forceFill([
        'last_used_ip' => $request->ip(),
        'last_used_at' => now(),
        'expires_at' => now()->addDays($trustedDeviceLifetimeDays()),
    ])->save();

    return $trustedDevice;
};

$rememberTrustedDevice = static function (Response $response, Request $request, User $user) use ($trustedDeviceCookieName, $trustedDeviceLifetimeDays, $trustedDeviceLifetimeMinutes): Response {
    $token = Str::random(80);

    UserTrustedDevice::create([
        'user_id' => $user->id,
        'token_hash' => hash('sha256', $token),
        'device_name' => Str::limit($request->userAgent() ?: 'Unknown device', 255, ''),
        'last_used_ip' => $request->ip(),
        'last_used_at' => now(),
        'expires_at' => now()->addDays($trustedDeviceLifetimeDays()),
    ]);

    return $response->withCookie(cookie(
        $trustedDeviceCookieName(),
        $token,
        $trustedDeviceLifetimeMinutes(),
        null,
        null,
        config('session.secure') ?? $request->isSecure(),
        true,
        false,
        'lax'
    ));
};

$forgetLoginEmailCode = static function (Request $request): void {
    $request->session()->forget([
        'login_email_code_hash',
        'login_email_code_user_id',
        'login_email_code_remember',
        'login_email_code_expires_at',
        'login_email_code_attempts',
    ]);
};

$sendLoginEmailCode = static function (Request $request, User $user, bool $remember): void {
    $code = (string) random_int(100000, 999999);

    $request->session()->put([
        'login_email_code_hash' => hash_hmac('sha256', $code, config('app.key')),
        'login_email_code_user_id' => $user->id,
        'login_email_code_remember' => $remember,
        'login_email_code_expires_at' => now()->addMinutes(10)->timestamp,
        'login_email_code_attempts' => 0,
    ]);

    Mail::raw(
        "Bonjour {$user->name},\n\nVotre code de connexion Project Manager est : {$code}\n\nCe code expire dans 10 minutes.\n\nSi vous n'avez pas demandé ce code, ignorez cet email.",
        function ($message) use ($user) {
            $message
                ->to($user->email, $user->name)
                ->subject('Votre code de connexion Project Manager');
        }
    );
};

Route::middleware('guest')->group(function () use ($loginSecurityChallenge, $verifyLoginSecurityChallenge, $continueLogin, $findTrustedDevice, $rememberTrustedDevice, $sendLoginEmailCode, $forgetLoginEmailCode) {
    Route::get('/login', function () use ($loginSecurityChallenge) {
        return view('auth.login', $loginSecurityChallenge());
    })->name('login');

    Route::post('/login', function (Request $request) use ($verifyLoginSecurityChallenge, $continueLogin, $findTrustedDevice, $sendLoginEmailCode) {
        $request->session()->forget(['login.id', 'login.remember']);
        $verifyLoginSecurityChallenge($request);

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        // Add delay to prevent timing attacks
        usleep(500000); // 0.5 seconds

        if (Auth::validate($credentials)) {
            $user = User::where('email', $validated['email'])->firstOrFail();

            if ($findTrustedDevice($request, $user)) {
                return $continueLogin($request, $user, $request->boolean('remember'));
            }

            $sendLoginEmailCode($request, $user, $request->boolean('remember'));

            return redirect()
                ->route('login.email-code')
                ->with('status', 'A verification code has been sent to your email address.');
        }

        return back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->onlyInput('email');
    })->middleware('throttle:5,1')->name('login.store');

    Route::get('/login/email-code', function (Request $request) {
        $userId = $request->session()->get('login_email_code_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('login');
        }

        return view('auth.email-code', [
            'email' => $user->email,
            'expiresAt' => $request->session()->get('login_email_code_expires_at'),
        ]);
    })->name('login.email-code');

    Route::post('/login/email-code', function (Request $request) use ($continueLogin, $rememberTrustedDevice, $forgetLoginEmailCode) {
        $validated = $request->validate([
            'email_code' => ['required', 'digits:6'],
        ], [
            'email_code.required' => 'Please enter the code sent to your email.',
            'email_code.digits' => 'The email verification code must contain 6 digits.',
        ]);

        $userId = $request->session()->get('login_email_code_user_id');
        $codeHash = $request->session()->get('login_email_code_hash');
        $expiresAt = (int) $request->session()->get('login_email_code_expires_at', 0);
        $attempts = (int) $request->session()->get('login_email_code_attempts', 0);

        if (! $userId || ! $codeHash) {
            return redirect()->route('login');
        }

        if ($expiresAt < now()->timestamp) {
            $forgetLoginEmailCode($request);

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'The verification code has expired. Please sign in again.']);
        }

        $submittedCodeHash = hash_hmac('sha256', $validated['email_code'], config('app.key'));

        if (! hash_equals($codeHash, $submittedCodeHash)) {
            $attempts++;
            $request->session()->put('login_email_code_attempts', $attempts);

            if ($attempts >= 5) {
                $forgetLoginEmailCode($request);

                return redirect()
                    ->route('login')
                    ->withErrors(['email' => 'Too many incorrect verification codes. Please sign in again.']);
            }

            return back()->withErrors(['email_code' => 'The verification code is incorrect.']);
        }

        $user = User::find($userId);

        if (! $user) {
            $forgetLoginEmailCode($request);

            return redirect()->route('login');
        }

        $remember = (bool) $request->session()->get('login_email_code_remember', false);
        $forgetLoginEmailCode($request);

        return $rememberTrustedDevice(
            $continueLogin($request, $user, $remember),
            $request,
            $user
        );
    })->middleware('throttle:10,1')->name('login.email-code.verify');

    Route::post('/login/email-code/resend', function (Request $request) use ($sendLoginEmailCode) {
        $userId = $request->session()->get('login_email_code_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('login');
        }

        $sendLoginEmailCode($request, $user, (bool) $request->session()->get('login_email_code_remember', false));

        return back()->with('status', 'A new verification code has been sent to your email address.');
    })->middleware('throttle:3,1')->name('login.email-code.resend');

    // Forgot password
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request) {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    })->middleware('throttle:5,1')->name('password.email');

    // Reset password
    Route::get('/reset-password/{token}', function (Request $request, string $token) {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    })->name('password.reset');

    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(12)->mixedCase()->numbers()->symbols()->uncompromised()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                     ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    })->middleware('throttle:5,1')->name('password.update');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::min(12)->mixedCase()->numbers()->symbols()->uncompromised()],
            'service' => ['required', 'string', 'in:development,design,marketing,support,sales,other'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'team_member',
            'service' => $validated['service'],
        ]);

        return redirect()->route('login')
            ->with('status', 'Account created successfully. Please sign in.');
    })->middleware('throttle:5,1')->name('register.store');
});

Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

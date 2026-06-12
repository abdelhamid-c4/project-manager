<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserTrustedDevice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class LoginCaptchaTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_displays_recaptcha_security_widget(): void
    {
        $this->enableRecaptcha();

        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Security verification');
        $response->assertSee('g-recaptcha', false);
        $response->assertSee('data-sitekey="test-site-key"', false);
        $response->assertSessionMissing('login_captcha_answer');
    }

    public function test_login_rejects_invalid_recaptcha_response(): void
    {
        $this->enableRecaptcha();
        $this->fakeFailedRecaptchaVerification();
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'password',
                'g-recaptcha-response' => 'invalid-token',
            ]);

        $response->assertSessionHasErrors('g-recaptcha-response');
        $this->assertGuest();
    }

    public function test_valid_credentials_send_user_to_email_code_step(): void
    {
        $this->enableRecaptcha();
        $this->fakeSuccessfulRecaptchaVerification();
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
            'g-recaptcha-response' => 'valid-token',
        ]);

        $response->assertRedirect(route('login.email-code'));
        $response->assertSessionHas('login_email_code_hash');
        $response->assertSessionHas('login_email_code_user_id', $user->id);
        $this->assertGuest();
    }

    public function test_user_can_login_with_valid_email_code(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'admin']);
        $code = '123456';

        $response = $this
            ->withSession([
                'login_email_code_hash' => hash_hmac('sha256', $code, config('app.key')),
                'login_email_code_user_id' => $user->id,
                'login_email_code_remember' => false,
                'login_email_code_expires_at' => now()->addMinutes(10)->timestamp,
                'login_email_code_attempts' => 0,
            ])
            ->post(route('login.email-code.verify'), [
                'email_code' => $code,
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertCookie('pm_trusted_device');
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->refresh()->email_verified_at);
        $this->assertDatabaseHas('user_trusted_devices', ['user_id' => $user->id]);
    }

    public function test_trusted_device_skips_email_code_step(): void
    {
        $this->enableRecaptcha();
        $this->fakeSuccessfulRecaptchaVerification();
        $user = User::factory()->create(['role' => 'admin']);
        $token = 'trusted-device-token-that-is-long-enough-for-validation';

        UserTrustedDevice::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $token),
            'device_name' => 'Feature test browser',
            'last_used_ip' => '127.0.0.1',
            'last_used_at' => now(),
            'expires_at' => now()->addDays(90),
        ]);

        $response = $this
            ->withCookie('pm_trusted_device', $token)
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'password',
                'g-recaptcha-response' => 'valid-token',
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionMissing('login_email_code_hash');
        $this->assertAuthenticatedAs($user);
    }

    public function test_email_code_redirects_to_two_factor_challenge_when_enabled(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->enableTwoFactorAuthenticationFor($user);
        $code = '123456';

        $response = $this
            ->withSession([
                'login_email_code_hash' => hash_hmac('sha256', $code, config('app.key')),
                'login_email_code_user_id' => $user->id,
                'login_email_code_remember' => true,
                'login_email_code_expires_at' => now()->addMinutes(10)->timestamp,
                'login_email_code_attempts' => 0,
            ])
            ->post(route('login.email-code.verify'), [
                'email_code' => $code,
            ]);

        $response->assertRedirect(route('two-factor.login'));
        $response->assertSessionHas('login.id', $user->id);
        $response->assertSessionHas('login.remember', true);
        $this->assertGuest();
    }

    public function test_user_can_finish_login_with_valid_two_factor_code(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $secret = $this->enableTwoFactorAuthenticationFor($user);
        $code = app(Google2FA::class)->getCurrentOtp($secret);

        $response = $this
            ->withSession([
                'login.id' => $user->id,
                'login.remember' => false,
            ])
            ->post(route('two-factor.login.store'), [
                'code' => $code,
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionMissing('login.id');
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_email_code_keeps_user_guest(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this
            ->withSession([
                'login_email_code_hash' => hash_hmac('sha256', '123456', config('app.key')),
                'login_email_code_user_id' => $user->id,
                'login_email_code_remember' => false,
                'login_email_code_expires_at' => now()->addMinutes(10)->timestamp,
                'login_email_code_attempts' => 0,
            ])
            ->post(route('login.email-code.verify'), [
                'email_code' => '654321',
            ]);

        $response->assertSessionHasErrors('email_code');
        $this->assertGuest();
    }

    private function enableTwoFactorAuthenticationFor(User $user): string
    {
        $secret = app(TwoFactorAuthenticationProvider::class)->generateSecretKey();

        $user->forceFill([
            'two_factor_secret' => Fortify::currentEncrypter()->encrypt($secret),
            'two_factor_recovery_codes' => Fortify::currentEncrypter()->encrypt(json_encode([
                'recovery-code-1',
                'recovery-code-2',
            ])),
            'two_factor_confirmed_at' => now(),
        ])->save();

        return $secret;
    }

    private function enableRecaptcha(): void
    {
        config([
            'services.recaptcha.enabled' => true,
            'services.recaptcha.site_key' => 'test-site-key',
            'services.recaptcha.secret_key' => 'test-secret-key',
            'services.recaptcha.verify_url' => 'https://recaptcha.test/siteverify',
        ]);
    }

    private function fakeSuccessfulRecaptchaVerification(): void
    {
        Http::fake([
            'https://recaptcha.test/siteverify' => Http::response(['success' => true]),
        ]);
    }

    private function fakeFailedRecaptchaVerification(): void
    {
        Http::fake([
            'https://recaptcha.test/siteverify' => Http::response(['success' => false], 200),
        ]);
    }
}

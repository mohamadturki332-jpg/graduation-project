<?php

namespace Tests\Feature;

use App\Mail\LoginCodeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LoginOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_helpdesk_local_account_logs_in_without_code(): void
    {
        Mail::fake();

        User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@nctkap.com',
            'password' => bcrypt('password'),
        ]);

        $this->post('/login', ['email' => 'admin@nctkap.com', 'password' => 'password'])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticated();
        Mail::assertNothingSent();
    }

    public function test_external_email_gets_code_and_is_not_signed_in_yet(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'role' => 'employee',
            'email' => 'person@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->post('/login', ['email' => 'person@example.com', 'password' => 'password'])
            ->assertRedirect(route('login.verify'));

        $this->assertGuest();
        Mail::assertSent(LoginCodeMail::class, fn ($mail) => $mail->hasTo('person@example.com'));

        $this->assertNotNull($user->fresh()->otp_code);
    }

    public function test_correct_code_signs_the_user_in(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'role' => 'employee',
            'email' => 'person@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->post('/login', ['email' => 'person@example.com', 'password' => 'password']);

        // Grab the real code off the captured mailable.
        $code = null;
        Mail::assertSent(LoginCodeMail::class, function ($mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        $this->post(route('login.verify.submit'), ['code' => $code])
            ->assertRedirect('/my-tickets');

        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->fresh()->otp_code);
    }

    public function test_wrong_code_is_rejected(): void
    {
        Mail::fake();

        User::factory()->create([
            'role' => 'employee',
            'email' => 'person@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->post('/login', ['email' => 'person@example.com', 'password' => 'password']);

        $this->post(route('login.verify.submit'), ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_expired_code_is_rejected(): void
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'email' => 'person@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->forceFill([
            'otp_code' => Hash::make('123456'),
            'otp_expires_at' => now()->subMinute(),
        ])->save();

        $this->withSession(['otp_user_id' => $user->id])
            ->post(route('login.verify.submit'), ['code' => '123456'])
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        User::factory()->create([
            'role' => 'employee',
            'email' => 'person@example.com',
            'password' => bcrypt('password'),
        ]);

        foreach (range(1, 5) as $i) {
            $this->post('/login', ['email' => 'person@example.com', 'password' => 'wrong'])
                ->assertSessionHasErrors('email');
        }

        // Sixth attempt is blocked even with the CORRECT password.
        $response = $this->post('/login', ['email' => 'person@example.com', 'password' => 'password']);
        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_verify_page_redirects_to_login_without_pending_otp(): void
    {
        $this->get(route('login.verify'))->assertRedirect(route('login'));
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Demo/seeded accounts use non-deliverable placeholder addresses, so a
     * verification code could never reach them — they log in with password only.
     */
    private const OTP_EXEMPT_DOMAIN = '@nctkap.com';

    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Brute-force guard: 5 attempts per email+IP, then a 60s lockout.
        $throttleKey = 'login|'.Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => __('Too many attempts. Please try again in :seconds seconds.', [
                    'seconds' => RateLimiter::availableIn($throttleKey),
                ]),
            ]);
        }

        if (! Auth::validate($credentials)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($throttleKey);

        $user = User::whereRaw('LOWER(email) = ?', [Str::lower($credentials['email'])])->first();

        if (Str::endsWith(Str::lower($user->email), self::OTP_EXEMPT_DOMAIN)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return $this->redirectByRole($user);
        }

        // Password OK — now require a one-time emailed code before signing in.
        $this->sendLoginCode($user);

        $request->session()->put([
            'otp_user_id'  => $user->id,
            'otp_remember' => $request->boolean('remember'),
        ]);

        return redirect()->route('login.verify');
    }

    public function showVerifyForm(Request $request)
    {
        $user = $this->pendingOtpUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        return view('auth.verify-code', [
            'maskedEmail' => $this->maskEmail($user->email),
        ]);
    }

    public function verify(Request $request)
    {
        $user = $this->pendingOtpUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $throttleKey = 'otp|'.$user->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'code' => __('Too many attempts. Please try again in :seconds seconds.', [
                    'seconds' => RateLimiter::availableIn($throttleKey),
                ]),
            ]);
        }

        $valid = $user->otp_code
            && $user->otp_expires_at
            && now()->lte($user->otp_expires_at)
            && Hash::check($request->string('code'), $user->otp_code);

        if (! $valid) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'code' => __('The code is incorrect or has expired.'),
            ]);
        }

        RateLimiter::clear($throttleKey);

        $user->forceFill(['otp_code' => null, 'otp_expires_at' => null])->save();

        $remember = (bool) $request->session()->pull('otp_remember', false);
        $request->session()->forget('otp_user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    public function resend(Request $request)
    {
        $user = $this->pendingOtpUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        // One resend per 60 seconds so the mailbox isn't flooded.
        $throttleKey = 'otp-resend|'.$user->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 1)) {
            return back()->with('error', __('Too many attempts. Please try again in :seconds seconds.', [
                'seconds' => RateLimiter::availableIn($throttleKey),
            ]));
        }

        RateLimiter::hit($throttleKey, 60);

        $this->sendLoginCode($user);

        return back()->with('status', __('A new code has been sent.'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Signed out.');
    }

    protected function redirectByRole($user)
    {
        return match (true) {
            $user->hasRole('admin')    => redirect()->route('admin.dashboard'),
            $user->hasRole('agent')    => redirect()->route('tickets.index'),
            $user->hasRole('employee') => redirect()->route('my-tickets.index'),
            default                    => redirect()->route('login')->with('error', 'Your account has no role assigned. Contact an administrator.'),
        };
    }

    private function sendLoginCode(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        $user->forceFill([
            'otp_code'       => Hash::make($code),
            'otp_expires_at' => now()->addMinutes(10),
        ])->save();

        try {
            Mail::to($user->email)->send(new LoginCodeMail($code, $user->name));
        } catch (\Throwable $e) {
            report($e);

            throw ValidationException::withMessages([
                'email' => __('Could not send the verification code. Please try again.'),
            ]);
        }
    }

    private function pendingOtpUser(Request $request): ?User
    {
        $id = $request->session()->get('otp_user_id');

        return $id ? User::find($id) : null;
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);

        $visible = mb_substr($local, 0, min(2, mb_strlen($local)));

        return $visible.str_repeat('*', max(3, mb_strlen($local) - 2)).'@'.$domain;
    }
}

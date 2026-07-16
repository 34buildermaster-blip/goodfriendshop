<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route($this->homeRouteFor(Auth::user()));
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials['email']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => 'พยายามเข้าสู่ระบบบ่อยเกินไป กรุณารอสักครู่แล้วลองใหม่',
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
            ]);
        }

        if (! $request->user()->isActive()) {
            Auth::logout();
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'บัญชีนี้ถูกระงับการใช้งาน กรุณาติดต่อผู้ดูแล',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route($this->homeRouteFor($request->user())));
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route($this->homeRouteFor(Auth::user()));
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'line_id' => ['nullable', 'string', 'max:80'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'line_id' => $data['line_id'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('profile.show');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $state = Str::random(40);
        $request->session()->put('oauth_google_state', $state);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ]));
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        if ($request->filled('error')) {
            return $this->redirectSocialFailure($request->string('error')->toString());
        }

        $expectedState = $request->session()->pull('oauth_google_state');

        if (blank($expectedState) || ! hash_equals($expectedState, $request->string('state')->toString())) {
            return $this->redirectSocialFailure('invalid_state');
        }

        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => config('services.google.redirect'),
            'grant_type' => 'authorization_code',
            'code' => $request->string('code')->toString(),
        ]);

        if (! $tokenResponse->successful() || blank($tokenResponse->json('access_token'))) {
            return $this->redirectSocialFailure('token_failed');
        }

        $profileResponse = Http::withToken($tokenResponse->json('access_token'))
            ->get('https://openidconnect.googleapis.com/v1/userinfo');

        if (! $profileResponse->successful() || blank($profileResponse->json('sub'))) {
            return $this->redirectSocialFailure('profile_failed');
        }

        $profile = $profileResponse->json();
        $user = $this->resolveGoogleUser($profile);
        $token = $this->issueApiToken($user);

        $payload = base64_encode(json_encode([
            'token' => $token,
            'user' => $this->userPayload($user),
        ], JSON_THROW_ON_ERROR));

        return redirect()->away($this->frontendUrl('/login/social-callback#payload='.rawurlencode($payload)));
    }

    public function redirectToLine(Request $request): RedirectResponse
    {
        $state = Str::random(40);
        $nonce = Str::random(40);
        $request->session()->put('oauth_line_state', $state);
        $request->session()->put('oauth_line_nonce', $nonce);

        return redirect()->away('https://access.line.me/oauth2/v2.1/authorize?'.http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.line.client_id'),
            'redirect_uri' => config('services.line.redirect'),
            'state' => $state,
            'scope' => 'profile openid email',
            'nonce' => $nonce,
        ]));
    }

    public function handleLineCallback(Request $request): RedirectResponse
    {
        if ($request->filled('error')) {
            return $this->redirectSocialFailure($request->string('error')->toString());
        }

        $expectedState = $request->session()->pull('oauth_line_state');
        $request->session()->forget('oauth_line_nonce');

        if (blank($expectedState) || ! hash_equals($expectedState, $request->string('state')->toString())) {
            return $this->redirectSocialFailure('invalid_state');
        }

        $tokenResponse = Http::asForm()->post('https://api.line.me/oauth2/v2.1/token', [
            'grant_type' => 'authorization_code',
            'code' => $request->string('code')->toString(),
            'redirect_uri' => config('services.line.redirect'),
            'client_id' => config('services.line.client_id'),
            'client_secret' => config('services.line.client_secret'),
        ]);

        if (! $tokenResponse->successful() || blank($tokenResponse->json('access_token'))) {
            return $this->redirectSocialFailure('token_failed');
        }

        $profile = $this->lineProfileFromAccessToken((string) $tokenResponse->json('access_token'));

        if (blank($profile['sub'] ?? $profile['userId'] ?? null)) {
            return $this->redirectSocialFailure('profile_failed');
        }

        $user = $this->resolveLineUser($profile);
        $token = $this->issueApiToken($user);

        $payload = base64_encode(json_encode([
            'token' => $token,
            'user' => $this->userPayload($user),
        ], JSON_THROW_ON_ERROR));

        return redirect()->away($this->frontendUrl('/login/social-callback#payload='.rawurlencode($payload)));
    }

    private function homeRouteFor(?User $user): string
    {
        return $user?->isAdmin() ? 'admin.dashboard' : 'profile.show';
    }

    private function resolveGoogleUser(array $profile): User
    {
        $providerId = (string) $profile['sub'];
        $email = isset($profile['email']) ? Str::lower((string) $profile['email']) : null;
        $name = (string) ($profile['name'] ?? $profile['given_name'] ?? 'Google Member');
        $avatar = isset($profile['picture']) ? (string) $profile['picture'] : null;

        $socialAccount = SocialAccount::query()
            ->where('provider', 'google')
            ->where('provider_user_id', $providerId)
            ->first();

        if ($socialAccount) {
            $user = $socialAccount->user;
        } elseif ($email) {
            $user = User::query()->where('email', $email)->first();
        } else {
            $user = null;
        }

        if (! $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email ?: "google-{$providerId}@goodfriendshop.local",
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(40)),
                'role' => User::ROLE_CUSTOMER,
                'status' => User::STATUS_ACTIVE,
                'avatar_path' => $avatar,
            ]);
        } else {
            $updates = [];

            if (blank($user->avatar_path) && $avatar) {
                $updates['avatar_path'] = $avatar;
            }

            if (blank($user->email_verified_at) && ($profile['email_verified'] ?? false)) {
                $updates['email_verified_at'] = now();
            }

            if ($updates) {
                $user->forceFill($updates)->save();
            }
        }

        SocialAccount::query()->updateOrCreate(
            [
                'provider' => 'google',
                'provider_user_id' => $providerId,
            ],
            [
                'user_id' => $user->id,
                'email' => $email,
                'name' => $name,
                'avatar_url' => $avatar,
                'raw_profile' => $profile,
                'last_login_at' => now(),
            ],
        );

        return $user->refresh();
    }

    private function lineProfileFromAccessToken(string $accessToken): array
    {
        $profileResponse = Http::withToken($accessToken)
            ->get('https://api.line.me/oauth2/v2.1/userinfo');

        if ($profileResponse->successful() && filled($profileResponse->json('sub'))) {
            return $profileResponse->json();
        }

        $legacyProfileResponse = Http::withToken($accessToken)
            ->get('https://api.line.me/v2/profile');

        return $legacyProfileResponse->successful() ? $legacyProfileResponse->json() : [];
    }

    private function resolveLineUser(array $profile): User
    {
        $providerId = (string) ($profile['sub'] ?? $profile['userId']);
        $email = isset($profile['email']) ? Str::lower((string) $profile['email']) : null;
        $name = (string) ($profile['name'] ?? $profile['displayName'] ?? 'LINE Member');
        $avatar = isset($profile['picture']) ? (string) $profile['picture'] : ($profile['pictureUrl'] ?? null);

        $socialAccount = SocialAccount::query()
            ->where('provider', 'line')
            ->where('provider_user_id', $providerId)
            ->first();

        if ($socialAccount) {
            $user = $socialAccount->user;
        } elseif ($email) {
            $user = User::query()->where('email', $email)->first();
        } else {
            $user = null;
        }

        if (! $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email ?: "line-{$providerId}@goodfriendshop.local",
                'email_verified_at' => $email ? now() : null,
                'password' => Hash::make(Str::random(40)),
                'role' => User::ROLE_CUSTOMER,
                'status' => User::STATUS_ACTIVE,
                'avatar_path' => $avatar,
            ]);
        } else {
            $updates = [];

            if (blank($user->avatar_path) && $avatar) {
                $updates['avatar_path'] = $avatar;
            }

            if ($email && blank($user->email_verified_at)) {
                $updates['email_verified_at'] = now();
            }

            if ($updates) {
                $user->forceFill($updates)->save();
            }
        }

        SocialAccount::query()->updateOrCreate(
            [
                'provider' => 'line',
                'provider_user_id' => $providerId,
            ],
            [
                'user_id' => $user->id,
                'email' => $email,
                'name' => $name,
                'avatar_url' => $avatar,
                'raw_profile' => $profile,
                'last_login_at' => now(),
            ],
        );

        return $user->refresh();
    }

    private function issueApiToken(User $user): string
    {
        $token = Str::random(80);
        $user->forceFill(['api_token' => hash('sha256', $token)])->save();

        return $token;
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'line_id' => $user->line_id,
            'avatar_url' => $user->avatarUrl(),
        ];
    }

    private function redirectSocialFailure(string $reason): RedirectResponse
    {
        return redirect()->away($this->frontendUrl('/login?social_error='.rawurlencode($reason)));
    }

    private function frontendUrl(string $path): string
    {
        return rtrim((string) env('FRONTEND_URL', 'http://127.0.0.1:3001'), '/').$path;
    }
}

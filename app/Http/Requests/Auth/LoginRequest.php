<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['nullable', 'string', 'required_without:email'],
            'email' => ['nullable', 'string', 'email', 'required_without:login'],
            'login_country_code' => ['nullable', 'string', 'max:8'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = trim((string) $this->input('login', ''));
        if ($identifier === '') {
            $identifier = Str::lower((string) $this->input('email', ''));
        }

        $credentials = $this->buildCredentials($identifier);

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $identifier = Str::lower(trim((string) $this->input('login', '')));
        if ($identifier === '') {
            $identifier = Str::lower(trim((string) $this->input('email', '')));
        }

        return Str::transliterate($identifier.'|'.$this->ip());
    }

    /**
     * Build the credential array for the guard attempt.
     */
    protected function buildCredentials(string $identifier): array
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return [
                'email' => Str::lower((string) $this->input('email', '')),
                'password' => $this->input('password'),
            ];
        }

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return [
                'email' => Str::lower($identifier),
                'password' => $this->input('password'),
            ];
        }

        $normalizedPhone = $this->resolvePhone($identifier);

        return [
            'phone' => $normalizedPhone,
            'password' => $this->input('password'),
        ];
    }

    protected function resolvePhone(string $value): string
    {
        if (Str::startsWith($value, '+')) {
            return '+' . preg_replace('/\D+/', '', $value);
        }

        if (Str::startsWith($value, '00')) {
            $trimmed = ltrim(substr($value, 2));
            return '+' . preg_replace('/\D+/', '', $trimmed);
        }

        $digits = $this->normalizePhone($value);
        if ($digits === '') {
            return '';
        }

        $countryCode = $this->normalizeCountryCode($this->input('login_country_code'));

        return $countryCode . $digits;
    }

    protected function normalizePhone(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    protected function normalizeCountryCode(?string $code): string
    {
        $trimmed = trim((string) $code);
        if ($trimmed === '') {
            $trimmed = config('phone.default', '+962');
        }

        if (! Str::startsWith($trimmed, '+')) {
            $trimmed = '+'.ltrim($trimmed, '+');
        }

        return $trimmed;
    }
}

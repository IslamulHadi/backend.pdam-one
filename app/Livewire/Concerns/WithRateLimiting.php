<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\RateLimiter;
use Livewire\Features\SupportValidation\ValidationException;

trait WithRateLimiting
{
    /**
     * Rate limit an action by the number of attempts per minute.
     *
     * @throws ValidationException
     */
    protected function rateLimit(int $maxAttempts, int $decaySeconds = 60, ?string $method = null): void
    {
        $method = $method ?: debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        $key = 'rate-limit:'.get_class($this).':'.$method.':'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            throw \Illuminate\Validation\ValidationException::withMessages([
                'rateLimiter' => "Terlalu banyak percobaan. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        RateLimiter::hit($key, $decaySeconds);
    }
}

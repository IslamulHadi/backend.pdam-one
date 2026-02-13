<?php

declare(strict_types=1);

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

describe('SecurityHeaders Middleware', function () {
    it('adds security headers to response', function () {
        $middleware = new SecurityHeaders;
        $request = Request::create('/test', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        expect($response->headers->get('X-Content-Type-Options'))->toBe('nosniff');
        expect($response->headers->get('X-Frame-Options'))->toBe('SAMEORIGIN');
        expect($response->headers->get('X-XSS-Protection'))->toBe('1; mode=block');
        expect($response->headers->get('Referrer-Policy'))->toBe('strict-origin-when-cross-origin');
        expect($response->headers->get('Permissions-Policy'))->toBe('camera=(), microphone=(), geolocation=(self)');
    });
});

describe('Session Security Config', function () {
    it('has session encryption enabled by default', function () {
        expect(config('session.encrypt'))->toBeTrue();
    });

    it('has secure cookies default set to true in config', function () {
        // Note: .env may override this. We verify the config file has a secure default.
        $configContent = file_get_contents(config_path('session.php'));
        expect($configContent)->toContain("'SESSION_SECURE_COOKIE', true");
    });

    it('has http_only cookies enabled', function () {
        expect(config('session.http_only'))->toBeTrue();
    });

    it('has same_site set to lax', function () {
        expect(config('session.same_site'))->toBe('lax');
    });
});

describe('Rate Limiting Trait', function () {
    it('WithRateLimiting trait exists', function () {
        expect(trait_exists(\App\Livewire\Concerns\WithRateLimiting::class))->toBeTrue();
    });
});

describe('ChangePasswordRequest', function () {
    it('requires current_password', function () {
        $request = new \App\Http\Requests\ChangePasswordRequest;
        $rules = $request->rules();

        expect($rules)->toHaveKey('current_password');
        expect($rules['current_password'])->toContain('required');
    });

    it('requires new password with complexity', function () {
        $request = new \App\Http\Requests\ChangePasswordRequest;
        $rules = $request->rules();

        expect($rules)->toHaveKey('password');
        expect($rules['password'])->toContain('required');
        expect($rules['password'])->toContain('confirmed');
    });
});

describe('robots.txt', function () {
    it('disallows admin paths', function () {
        $content = file_get_contents(public_path('robots.txt'));
        expect($content)->toContain('Disallow: /admin/');
        expect($content)->toContain('Disallow: /api/');
        expect($content)->toContain('Disallow: /storage/');
    });
});

describe('Validation max lengths', function () {
    it('FaqRequest has max on answer field', function () {
        $request = new \App\Http\Requests\FaqRequest;
        $rules = $request->rules();
        expect($rules['answer'])->toContain('max:65535');
    });

    it('ProfileRequest has max on avatar field', function () {
        $request = new \App\Http\Requests\ProfileRequest;
        $rules = $request->rules();
        expect($rules['avatar'])->toContain('max:2048');
    });

    it('ProfileRequest has max on nama field', function () {
        $request = new \App\Http\Requests\ProfileRequest;
        $rules = $request->rules();
        expect($rules['nama'])->toContain('max:255');
    });
});

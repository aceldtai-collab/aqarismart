<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $emails = config('auth.super_admin_emails', []);

        if ($emails === []) {
            return $next($request);
        }

        $email = strtolower((string) $user->email);

        if (! in_array($email, $emails, true)) {
            abort(403);
        }

        return $next($request);
    }
}


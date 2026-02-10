<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCustomToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->extractToken($request);

        if (!$token) {
            return response()->json(['detail' => 'Not authenticated (missing token)'], 403);
        }

        if (!str_starts_with($token, '1|')) {
            return response()->json(['detail' => 'Not authenticated (invalid token prefix)'], 403);
        }

        $parts = explode('|', $token);
        if (count($parts) < 3) {
            return response()->json(['detail' => 'Invalid token format'], 403);
        }

        $role = $parts[1];
        $username = $parts[2] ?? 'unknown';

        $user = User::where('username', $username)->where('role', $role)->first();

        if (!$user) {
            return response()->json(['detail' => 'Role atau username tidak valid'], 403);
        }

        $request->merge([
            'auth_token' => $token,
            'auth_role' => $role,
            'auth_username' => $user->username,
            'auth_user_id' => $user->id,
            'auth_user' => $user,
        ]);

        return $next($request);
    }

    protected function extractToken(Request $request): ?string
    {
        // Try Authorization header
        $auth = $request->header('Authorization');
        if ($auth) {
            $token = $this->extractFromHeaderValue($auth);
            if ($token) return $token;
        }

        // Try X-Access-Token header
        $xToken = $request->header('X-Access-Token');
        if ($xToken) {
            $token = $this->extractFromHeaderValue($xToken);
            if ($token) return $token;
        }

        // Try Token header
        $tokenHeader = $request->header('Token');
        if ($tokenHeader) {
            $token = $this->extractFromHeaderValue($tokenHeader);
            if ($token) return $token;
        }

        return null;
    }

    protected function extractFromHeaderValue(string $value): ?string
    {
        $value = trim($value);
        if (!$value) return null;

        if (str_starts_with(strtolower($value), 'bearer ')) {
            return trim(explode(' ', $value, 2)[1]);
        }

        return $value;
    }
}

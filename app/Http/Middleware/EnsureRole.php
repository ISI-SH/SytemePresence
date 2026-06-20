<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Accès non autorisé.');
        }

        $allowed = collect($roles)->flatMap(function (string $role) {
            return match ($role) {
                'employee', 'employe' => ['employee', 'employe'],
                default               => [$role],
            };
        })->unique()->values()->all();

        if (!in_array($user->role, $allowed, true)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    // Middleware chargé de vérifier le rôle de l'utilisateur
    // avant de lui donner accès à une page
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Récupère l'utilisateur connecté
        $user = $request->user();

        // Si aucun utilisateur n'est connecté
        // accès refusé
        if (!$user) {
            abort(403, 'Accès non autorisé.');
        }

        // Construction de la liste des rôles autorisés
        // Permet d'accepter "employee" et "employe"
        $allowed = collect($roles)->flatMap(function (string $role) {
            return match ($role) {

                // Gestion des différentes écritures du rôle employé
                'employee', 'employe' => ['employee', 'employe'],

                // Tous les autres rôles sont conservés tels quels
                default => [$role],
            };
        })->unique()->values()->all();

        // Vérifie si le rôle de l'utilisateur
        // fait partie des rôles autorisés
        if (!in_array($user->role, $allowed, true)) {
            abort(403, 'Accès non autorisé.');
        }

        // Si tout est correct, la requête continue
        return $next($request);
    }
}
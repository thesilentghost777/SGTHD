<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AccountAccessController extends Controller
{
    private const WORKSPACE_ROUTES = [
        'alimentation' => [
            'chef_rayoniste' => ['code' => 75804, 'route' => 'alimchef.workspace'],
            'caissiere' => ['code' => 75804, 'route' => 'cashier.workspace'],
            'calviste' => ['code' => 75804, 'route' => 'mc.workspace'],
            'magasinier' => ['code' => 75804, 'route' => 'mc.workspace'],
            'rayoniste' => ['code' => 75804, 'route' => 'alim.workspace'],
            'controleur' => ['code' => 75804, 'route' => 'alim.workspace'],
            'tech_surf' => ['code' => 75804, 'route' => 'alim.workspace']
        ],
        'production' => [
            'patissier' => ['code' => 182736, 'route' => 'producteur.workspace'],
            'boulanger' => ['code' => 394857, 'route' => 'producteur.workspace'],
            'pointeur' => ['code' => 527194, 'route' => 'pointer.workspace'],
            'enfourneur' => ['code' => 639285, 'route' => 'alim.workspace'],
            'tech_surf' => ['code' => 748196, 'route' => 'alim.workspace']
        ],
        'glace' => [
            'glace' => ['code' => 583492, 'route' => 'ice.workspace']
        ],
        'administration' => [
            'chef_production' => ['code' => 948371, 'route' => 'production.chief.workspace'],
            'dg' => ['code' => 217634, 'route' => 'dg.workspace'],
            'ddg' => ['code' => 365982, 'route' => 'pdg.workspace'],
            'gestionnaire_alimentation' => ['code' => 365982, 'route' => 'alimchef.workspace'],
            'pdg' => ['code' => 592483, 'route' => 'pdg.workspace']
        ],
        'vente' => [
            'vendeur_boulangerie' => ['code' => 748596, 'route' => 'seller.workspace'],
            'vendeur_patisserie' => ['code' => 983214, 'route' => 'seller.workspace']
        ]
    ];

    /**
     * Afficher la liste des employés accessibles
     */
    public function index()
    {
        $user = Auth::user();
        $accessibleUsers = $user->getAccessibleUsers();

        return view('account-access.index', compact('accessibleUsers'));
    }

    /**
     * Accéder au compte d'un employé
     */
    public function accessAccount($id)
    {
        $currentUser = Auth::user();
        $targetUser = User::findOrFail($id);

        // Vérifier si l'utilisateur actuel peut accéder au compte cible
        if (!$currentUser->canAccessUser($targetUser)) {
            return redirect()->route('account-access.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation d\'accéder à ce compte.');
        }

        // Stocker les informations de l'utilisateur actuel dans la session
        Session::put('original_user_id', $currentUser->id);
        Session::put('impersonating', true);

        // Connecter en tant que l'utilisateur cible
        Auth::login($targetUser);

        // Déterminer la route de redirection appropriée
        $redirectRoute = $this->getRedirectRoute($targetUser);

        return redirect()->route($redirectRoute)
            ->with('success', 'Vous êtes maintenant connecté en tant que ' . $targetUser->name);
    }

    /**
     * Revenir au compte original
     */
    public function returnToOriginal()
    {
        // Vérifier si l'utilisateur est en train d'usurper l'identité d'un autre
        if (!Session::has('original_user_id') || !Session::get('impersonating')) {
            return redirect()->route('dashboard');
        }

        $originalUserId = Session::get('original_user_id');
        $originalUser = User::findOrFail($originalUserId);

        // Supprimer les informations d'usurpation d'identité de la session
        Session::forget('original_user_id');
        Session::forget('impersonating');

        // Connecter en tant qu'utilisateur original
        Auth::login($originalUser);

        return redirect()->route('account-access.index')
            ->with('success', 'Vous êtes revenu à votre compte original.');
    }

    /**
     * Déterminer la route de redirection appropriée pour un utilisateur
     */
    private function getRedirectRoute($user)
    {
        // Vérifier si le secteur existe
        if (!isset(self::WORKSPACE_ROUTES[$user->secteur])) {
            return 'dashboard';
        }

        // Vérifier si le rôle existe dans le secteur
        if (!isset(self::WORKSPACE_ROUTES[$user->secteur][$user->role])) {
            return 'dashboard';
        }

        // Récupérer les informations de route
        $routeInfo = self::WORKSPACE_ROUTES[$user->secteur][$user->role];
        return $routeInfo['route'];
    }
}

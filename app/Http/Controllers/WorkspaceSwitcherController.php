<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class WorkspaceSwitcherController extends Controller
{
    private const DEFAULT_ACCESS_CODE = 2025;

    /**
     * Constante définissant la hiérarchie des rôles et leurs accès
     */
    private const ROLE_HIERARCHY = [
        'pdg' => ['dg', 'chef_production', 'producteur', 'employee'],
        'dg' => ['chef_production', 'producteur', 'employee'],
        'ddg' => ['dg', 'chef_production', 'producteur', 'employee'],
        'chef_production' => ['producteur', 'employee'],
        'gestionnaire_alimentation' => ['employee'],
    ];

    /**
     * Constante définissant les routes de workspace par rôle et secteur
     */
    private const WORKSPACE_ROUTES = [
        'alimentation' => [
            'chef_rayoniste' => ['route' => 'alimchef.workspace'],
            'caissiere' => ['route' => 'cashier.workspace'],
            'calviste' => ['route' => 'mc.workspace'],
            'magasinier' => ['route' => 'mc.workspace'],
            'rayoniste' => ['route' => 'alim.workspace'],
            'controleur' => ['route' => 'alim.workspace'],
            'tech_surf' => ['route' => 'alim.workspace'],
            'virgile'  => ['route' => 'alim.workspace'],
            'employee' => ['route' => 'alim.workspace']
        ],
        'production' => [
            'patissier' => ['route' => 'producteur.workspace'],
            'boulanger' => ['route' => 'producteur.workspace'],
            'pointeur' => ['route' => 'pointer.workspace'],
            'enfourneur' => ['route' => 'alim.workspace'],
            'tech_surf' => ['route' => 'alim.workspace'],
            'producteur' => ['route' => 'producteur.workspace'],
            'chef_production' => ['route' => 'production.chief.workspace'],
            'employee' => ['route' => 'alim.workspace']
        ],
        'glace' => [
            'glace' => ['route' => 'ice.workspace'],
            'employee' => ['route' => 'alim.workspace']
        ],
        'administration' => [
            'chef_production' => ['route' => 'production.chief.workspace'],
            'dg' => ['route' => 'dg.workspace'],
            'ddg' => ['route' => 'pdg.workspace'],
            'gestionnaire_alimentation' => ['route' => 'alimchef.workspace'],
            'pdg' => ['route' => 'pdg.workspace'],
            'employee' => ['route' => 'alim.workspace']
        ],
        'vente' => [
            'vendeur_boulangerie' => ['route' => 'seller.workspace'],
            'vendeur_patisserie' => ['route' => 'seller.workspace'],
            'employee' => ['route' => 'alim.workspace']
        ]
    ];

    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $role = $user->role;
        $sector = $user->sector;
        $availableModes = $this->getAvailableModes($role, $sector);
        $currentMode = Session::get('current_workspace_mode', $role);

        return view('workspace.switcher', compact('availableModes', 'currentMode', 'user'));
    }

    public function switchMode(Request $request)
    {
        $request->validate([
            'mode' => 'required|string',
            'sector' => 'required|string',
            'access_code' => 'required|numeric',
        ]);

        $user = Auth::user();
        $targetMode = $request->input('mode');
        $targetSector = $request->input('sector');
        $accessCode = (int)$request->input('access_code');

        // Vérifier si l'utilisateur peut accéder à ce mode
        if (!$this->canAccessMode($user->role, $targetMode)) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les permissions pour accéder à ce mode de travail.');
        }

        // Vérifier le code d'accès
        if ($accessCode !== self::DEFAULT_ACCESS_CODE) {
            return redirect()->back()->with('error', 'Code d\'accès incorrect.');
        }

        // Stocker le nouveau mode dans la session
        Session::put('current_workspace_mode', $targetMode);
        Session::put('current_workspace_sector', $targetSector);

        // Rediriger vers l'espace de travail approprié
        $route = $this->getWorkspaceRoute($targetSector, $targetMode);

        return redirect()->route($route)
            ->with('success', 'Mode de travail changé avec succès. Vous êtes maintenant en mode ' . ucfirst($targetMode) . '.');
    }

    private function canAccessMode($userRole, $targetMode)
    {
        if ($userRole === $targetMode) {
            return true;
        }

        if (!isset(self::ROLE_HIERARCHY[$userRole])) {
            return false;
        }

        return in_array($targetMode, self::ROLE_HIERARCHY[$userRole]);
    }

    private function getWorkspaceRoute($sector, $role)
{
    // Vérifier si le secteur et le rôle existent dans le tableau
    if (isset(self::WORKSPACE_ROUTES[$sector][$role])) {
        return self::WORKSPACE_ROUTES[$sector][$role]['route'];
    }

    // Route par défaut si la combinaison n'existe pas
    return 'alim.workspace';
}


    private function getAvailableModes($userRole, $userSector)
    {
        $modes = [];

        $modes[] = [
            'role' => $userRole,
            'sector' => $userSector,
            'name' => ucfirst($userRole),
            'description' => 'Votre rôle principal'
        ];

        if (!isset(self::ROLE_HIERARCHY[$userRole])) {
            return $modes;
        }

        foreach (self::ROLE_HIERARCHY[$userRole] as $accessibleRole) {
            $sector = $this->determineSectorForRole($accessibleRole);

            $modes[] = [
                'role' => $accessibleRole,
                'sector' => $sector,
                'name' => ucfirst($accessibleRole),
                'description' => 'Accéder aux fonctionnalités de ' . ucfirst($accessibleRole)
            ];
        }

        return $modes;
    }

    private function determineSectorForRole($role)
    {
        $defaultSectors = [
            'pdg' => 'administration',
            'dg' => 'administration',
            'ddg' => 'administration',
            'chef_production' => 'production',
            'producteur' => 'production',
            'employee' => 'administration',
        ];

        return $defaultSectors[$role] ?? 'administration';
    }
}
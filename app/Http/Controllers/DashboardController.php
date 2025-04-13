<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Configuration;

class DashboardController extends Controller
{
    private const WORKSPACE_ROUTES = [
        'alimentation' => [
            'chef_rayoniste' => ['code' => 75804, 'route' => 'alimchef.workspace'],//ok
            'caissiere' => ['code' => 75804, 'route' => 'cashier.workspace'],//ok
            'calviste' => ['code' => 75804, 'route' => 'mc.workspace'],//ok
            'magasinier' => ['code' => 75804, 'route' => 'mc.workspace'],//ok
            'rayoniste' => ['code' => 75804, 'route' => 'alim.workspace'],//ok
            'controleur' => ['code' => 75804, 'route' => 'alim.workspace'],//ok
            'tech_surf' => ['code' => 75804, 'route' => 'alim.workspace'],//ok
            'virgile'  => ['code' => 75804, 'route' => 'alim.workspace']//ok
        ],
        'production' => [
            'patissier' => ['code' => 182736, 'route' => 'producteur.workspace'],//ok
            'boulanger' => ['code' => 394857, 'route' => 'producteur.workspace'],//ok
            'pointeur' => ['code' => 527194, 'route' => 'pointer.workspace'],//ok
            'enfourneur' => ['code' => 639285, 'route' => 'alim.workspace'],//ok
            'tech_surf' => ['code' => 748196, 'route' => 'alim.workspace']//ok
        ],
        'glace' => [
            'glace' => ['code' => 583492, 'route' => 'ice.workspace']
        ],
        'administration' => [
            'chef_production' => ['code' => 948371, 'route' => 'production.chief.workspace'],//ok
            'dg' => ['code' => 217634, 'route' => 'dg.workspace'],//ok
            'ddg' => ['code' => 365982, 'route' => 'pdg.workspace'],//ok
            'gestionnaire_alimentation' => ['code' => 365982, 'route' => 'alimchef.workspace'],//ok
            'pdg' => ['code' => 592483, 'route' => 'pdg.workspace']//ok
        ],
        'vente' => [
            'vendeur_boulangerie' => ['code' => 748596, 'route' => 'seller.workspace'],//ok
            'vendeur_patisserie' => ['code' => 983214, 'route' => 'seller.workspace']//ok
        ]
    ];

    public function index()
    {
        return view('dashboard.index');
    }

    public function redirectToWorkspace()
    {
        $user = Auth::user();

        // Vérification spéciale pour le DG si first_config = 0
        if ($user->secteur === 'administration' && $user->role === 'dg') {
            $config = Configuration::find(1);
            if ($config && $config->first_config === false) {
                return redirect()->route('setup.create');
            }
        }

        // Vérifier si le secteur existe
        if (!isset(self::WORKSPACE_ROUTES[$user->secteur])) {
            return redirect()->route('problem')->with('error', 'Secteur non autorisé');
        }

        // Vérifier si le rôle existe dans le secteur
        if (!isset(self::WORKSPACE_ROUTES[$user->secteur][$user->role])) {
            return redirect()->route('problem')->with('error', 'Rôle non autorisé pour ce secteur');
        }

        // Vérifier si le code secret correspond
        $routeInfo = self::WORKSPACE_ROUTES[$user->secteur][$user->role];
        if ($user->code_secret !== $routeInfo['code']) {
            return redirect()->route('problem')->with('error', 'Code secteur invalide');
        }

        // Redirection vers l'espace de travail approprié
        return redirect()->route($routeInfo['route']);
    }

    public function problem()
    {
        return view('dashboard.problem');
    }

    public function about()
    {
        return view('dashboard.about');
    }
}

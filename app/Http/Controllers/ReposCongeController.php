<?php

namespace App\Http\Controllers;

use App\Models\ReposConge;
use App\Models\User;
use App\Services\DayService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\NotificationController;
use App\Traits\HistorisableActions;



class ReposCongeController extends Controller
{
    protected $dayService;
    use HistorisableActions;


    public function __construct(NotificationController $notificationController,DayService $dayService)
    {
        $this->notificationController = $notificationController;
        $this->dayService = $dayService;
    }

    public function index()
    {
        $nom = auth()->user()->name;
        $role = auth()->user()->role;
        $employes = User::all();
        $reposConges = ReposConge::with('employe')->get();

        return view('repos-conges.index', compact('employes', 'reposConges','nom','role'));
    }

    public function show()
    {
        $reposConge = ReposConge::where('employe_id', auth()->id())->first();
        $jourNumber = $reposConge ? $this->dayService->getDayNumber($reposConge->jour) : null;

        return view('repos-conges.employee', compact('reposConge', 'jourNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employe_id' => 'required|exists:users,id',
            'jour' => 'required|in:' . implode(',', $this->dayService->getAllDays()),
            'conges' => 'nullable|integer|min:1',
            'debut_c' => 'nullable|date',
            'raison_c' => 'nullable|in:maladie,evenement,accouchement,autre',
            'autre_raison' => 'nullable|required_if:raison_c,autre|string|max:255',
        ]);

        ReposConge::updateOrCreate(
            ['employe_id' => $request->employe_id],
            $validated
        );
        //notifier l'employer
        $employe = User::find($request->employe_id);
        $request->merge([
            'recipient_id' => $employe->id,
            'subject' => 'Jour de repos ou congé',
            'message' => 'Votre jour de repos ou congé a été enregistré.Acceder a la plateforme pour plus de details',
        ]);
        // Appel de la méthode send
        $this->notificationController->send($request);
        //historiser
        $this->historiser("repos assigné à $employe->name",'create_repos');
        return redirect()->route('repos-conges.index')
            ->with('success', 'Informations enregistrées avec succès');
    }

    public function update(Request $request, ReposConge $reposConge)
    {
        $validated = $request->validate([
            'jour' => 'required|in:' . implode(',', $this->dayService->getAllDays()),
            'conges' => 'nullable|integer|min:1',
            'debut_c' => 'nullable|date',
            'raison_c' => 'nullable|in:maladie,evenement,accouchement,autre',
            'autre_raison' => 'nullable|required_if:raison_c,autre|string|max:255',
        ]);

        $reposConge->update($validated);

        return redirect()->route('repos-conges.index')
            ->with('success', 'Informations mises à jour avec succès');
    }
}

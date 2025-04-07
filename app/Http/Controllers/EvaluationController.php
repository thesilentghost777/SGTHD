<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Traits\HistorisableActions;
use App\Http\Controllers\NotificationController;

class EvaluationController extends Controller
{
    use HistorisableActions;

    public function __construct(NotificationController $notificationController)
	{
    		$this->notificationController = $notificationController;
	}

    public function index()
    {
        $employees = User::where('role', '!=', 'DG')
            ->with('evaluation')
            ->get()
            ->map(function ($employee) {
                $employee->age = Carbon::parse($employee->date_naissance)->age;
                return $employee;
            });

        return view('employees.index', compact('employees'));
    }

    public function show(User $user)
    {
        $user->age = Carbon::parse($user->date_naissance)->age;
        $user->load('evaluation');
        return view('employees.show', compact('user'));
    }

    public function evaluate(Request $request, User $user)
    {
        $validated = $request->validate([
            'note' => 'required|numeric|min:0|max:20',
            'appreciation' => 'required|string|max:1000'
        ]);

        $evaluation = Evaluation::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );
        $request->merge([
            'recipient_id' => $user->id,
            'subject' => 'Votre évaluation est prête',
            'message' => 'Vous avez été évalué par Mr' . auth()->user()->name . '. Votre note est ' . $validated['note'] . '/20 et votre appréciation est : ' . $validated['appreciation']
        ]);
        // Appel de la méthode send
        $this->notificationController->send($request);
        //historisation
        $this->historiser('Évaluation de ' . $user->name, 'Évaluation de ' . $user->name . ' avec la note de ' . $validated['note'] . ' et l\'appréciation : ' . $validated['appreciation'],'create_evaluation');
        return redirect()->route('employees.show', $user)
            ->with('success', 'Évaluation enregistrée avec succès');
    }

    public function stats()
    {
        $stats = [
            'total_employees' => User::where('role', '!=', 'DG')->count(),
            'average_note' => Evaluation::avg('note'),
            'employees' => User::where('role', '!=', 'DG')
                ->with('evaluation')
                ->get()
                ->map(function ($employee) {
                    $employee->age = Carbon::parse($employee->date_naissance)->age;
                    return $employee;
                })
        ];

        return view('employees.stats', compact('stats'));
    }
}

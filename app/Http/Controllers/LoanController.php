<?php

namespace App\Http\Controllers;

use App\Models\ACouper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Affiche la vue de gestion des prêts pour l'employé connecté
     */
    public function employeeView()
    {
        $employee = Auth::user();
        $loanData = ACouper::where('id_employe', $employee->id)->first();

        if (!$loanData) {
            $loanData = ACouper::create([
                'id_employe' => $employee->id,
                'pret' => 0,
                'remboursement' => 0,
                'date' => Carbon::now()
            ]);
        }

        return view('loans.employee', compact('loanData'));
    }

    /**
     * Traite la demande de prêt d'un employé
     */
    public function requestLoan(Request $request)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric|min:1000'
        ]);

        $employee = Auth::user();
        $loanData = ACouper::where('id_employe', $employee->id)->first();

        if ($loanData && $loanData->pret > 0) {
            return redirect()->back()->with('error', 'Vous avez déjà un prêt en cours.');
        }

        // Création d'une demande de prêt en attente
        DB::table('loan_requests')->insert([
            'user_id' => $employee->id,
            'amount' => $validated['montant'],
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Votre demande de prêt a été soumise et est en attente d\'approbation.');
    }

    /**
     * Affiche la liste des demandes de prêt pour le DG
     */
    public function pendingLoans()
    {
        $pendingLoans = DB::table('loan_requests')
            ->where('status', 'pending')
            ->join('users', 'users.id', '=', 'loan_requests.user_id')
            ->select('loan_requests.*', 'users.name')
            ->get();

        return view('loans.pending', compact('pendingLoans'));
    }

    /**
     * Approuve une demande de prêt
     */
    public function approveLoan(Request $request, $id)
    {
        $loan = DB::table('loan_requests')->where('id', $id)->first();

        if (!$loan) {
            return redirect()->back()->with('error', 'Demande de prêt introuvable.');
        }

        // Mettre à jour l'enregistrement ACouper
        $aCouper = ACouper::where('id_employe', $loan->user_id)->first();

        if (!$aCouper) {
            $aCouper = ACouper::create([
                'id_employe' => $loan->user_id,
                'pret' => $loan->amount,
                'remboursement' => 0,
                'date' => Carbon::now()
            ]);
        } else {
            $aCouper->pret += $loan->amount;
            $aCouper->save();
        }

        // Mettre à jour le statut de la demande
        DB::table('loan_requests')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'Le prêt a été approuvé avec succès.');
    }

    /**
     * Refuse une demande de prêt
     */
    public function rejectLoan(Request $request, $id)
    {
        DB::table('loan_requests')
            ->where('id', $id)
            ->update([
                'status' => 'rejected',
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'La demande de prêt a été refusée.');
    }

    /**
     * Liste tous les employés ayant un prêt en cours pour le DG
     */
    public function employeesWithLoans()
    {
        $employeesWithLoans = ACouper::where('pret', '>', 0)
                                    ->with('employe')
                                    ->get();

        return view('loans.employees-with-loans', compact('employeesWithLoans'));
    }

    /**
     * Permet au DG de définir le montant de remboursement mensuel
     */
    public function setMonthlyRepayment(Request $request, $id)
    {
        $validated = $request->validate([
            'remboursement' => 'required|numeric|min:0'
        ]);

        $aCouper = ACouper::where('id_employe', $id)->first();

        if (!$aCouper) {
            return redirect()->back()->with('error', 'Enregistrement introuvable.');
        }

        if ($validated['remboursement'] > $aCouper->pret) {
            return redirect()->back()->with('error', 'Le montant de remboursement ne peut pas être supérieur au prêt restant.');
        }

        $aCouper->remboursement = $validated['remboursement'];
        $aCouper->save();

        return redirect()->back()->with('success', 'Le montant de remboursement mensuel a été défini avec succès.');
    }

    /**
     * Affiche le détail d'un employé avec son prêt pour le DG
     */
    public function employeeDetail($id)
    {
        $employee = User::findOrFail($id);
        $loanData = ACouper::where('id_employe', $id)->first();

        if (!$loanData) {
            return redirect()->back()->with('error', 'Cet employé n\'a pas d\'enregistrement de prêt.');
        }

        // Historique des remboursements
        $repaymentHistory = DB::table('loan_repayments')
                             ->where('user_id', $id)
                             ->orderBy('created_at', 'desc')
                             ->get();

        return view('loans.employee-detail', compact('employee', 'loanData', 'repaymentHistory'));
    }

    /**
     * Enregistre un remboursement manuel
     */
    public function recordRepayment(Request $request, $id)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric|min:1'
        ]);

        $aCouper = ACouper::where('id_employe', $id)->first();

        if (!$aCouper || $aCouper->pret <= 0) {
            return redirect()->back()->with('error', 'Cet employé n\'a pas de prêt en cours.');
        }

        if ($validated['montant'] > $aCouper->pret) {
            $validated['montant'] = $aCouper->pret;
        }

        // Enregistrer le remboursement dans l'historique
        DB::table('loan_repayments')->insert([
            'user_id' => $id,
            'amount' => $validated['montant'],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Mettre à jour le prêt restant
        $aCouper->pret -= $validated['montant'];
        $aCouper->save();

        return redirect()->back()->with('success', 'Le remboursement a été enregistré avec succès.');
    }
}

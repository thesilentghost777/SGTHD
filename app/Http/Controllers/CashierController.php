<?php

namespace App\Http\Controllers;

use App\Models\CashierSession;
use App\Models\CashWithdrawal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function index()
    {
        $openSession = CashierSession::where('user_id', Auth::id())
            ->whereNull('end_time')
            ->first();

        $sessions = CashierSession::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('cashier.index', compact('openSession', 'sessions'));
    }

    public function startSession(Request $request)
    {
        $validated = $request->validate([
            'initial_cash' => 'required|numeric|min:0',
            'initial_change' => 'required|numeric|min:0',
            'initial_mobile_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        // Check if there's already an open session
        $openSession = CashierSession::where('user_id', Auth::id())
            ->whereNull('end_time')
            ->first();

        if ($openSession) {
            return redirect()->route('cashier.index')
                ->with('error', 'Vous avez déjà une session ouverte. Veuillez la fermer avant d\'en ouvrir une nouvelle.');
        }

        $session = new CashierSession();
        $session->user_id = Auth::id();
        $session->start_time = now();
        $session->initial_cash = $validated['initial_cash'];
        $session->initial_change = $validated['initial_change'];
        $session->initial_mobile_balance = $validated['initial_mobile_balance'];
        $session->notes = $validated['notes'];
        $session->save();

        return redirect()->route('cashier.session', $session->id)
            ->with('success', 'Session de caisse démarrée avec succès.');
    }

    public function showSession(CashierSession $session)
{
    // Ensure the session belongs to the authenticated user
    if ($session->user_id !== Auth::id()) {
        abort(403, 'Accès non autorisé.');
    }

    $withdrawals = CashWithdrawal::where('cashier_session_id', $session->id)
        ->orderBy('created_at', 'desc')
        ->get();

    // Récupérer les employés du secteur administration
    $adminEmployees = User::where('secteur', 'admin')
        ->orWhere('secteur', 'administration')
        ->orderBy('name')
        ->get();

    return view('cashier.session', compact('session', 'withdrawals', 'adminEmployees'));
}

    public function recordWithdrawal(Request $request, CashierSession $session)
    {
        // Ensure the session belongs to the authenticated user
        if ($session->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        // Ensure the session is still open
        if ($session->end_time !== null) {
            return redirect()->route('cashier.session', $session->id)
                ->with('error', 'Cette session est déjà fermée.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string',
            'withdrawn_by' => 'required|string'
        ]);

        $withdrawal = new CashWithdrawal();
        $withdrawal->cashier_session_id = $session->id;
        $withdrawal->amount = $validated['amount'];
        $withdrawal->reason = $validated['reason'];
        $withdrawal->withdrawn_by = $validated['withdrawn_by'];
        $withdrawal->created_at = now();
        $withdrawal->save();

        return redirect()->route('cashier.session', $session->id)
            ->with('success', 'Retrait enregistré avec succès.');
    }

    public function endSession(Request $request, CashierSession $session)
    {
        // Ensure the session belongs to the authenticated user
        if ($session->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        // Ensure the session is still open
        if ($session->end_time !== null) {
            return redirect()->route('cashier.session', $session->id)
                ->with('error', 'Cette session est déjà fermée.');
        }

        $validated = $request->validate([
            'final_cash' => 'required|numeric|min:0',
            'final_change' => 'required|numeric|min:0',
            'final_mobile_balance' => 'required|numeric|min:0',
            'cash_remitted' => 'required|numeric|min:0',
            'end_notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $session->final_cash = $validated['final_cash'];
            $session->final_change = $validated['final_change'];
            $session->final_mobile_balance = $validated['final_mobile_balance'];
            $session->cash_remitted = $validated['cash_remitted'];
            $session->end_notes = $validated['end_notes'];
            $session->end_time = now();

            // Calculate total withdrawals
            $totalWithdrawals = CashWithdrawal::where('cashier_session_id', $session->id)
                ->sum('amount');

            $session->total_withdrawals = $totalWithdrawals;

            // Calculate expected balance
            $expectedBalance = ($session->initial_cash + ($session->final_mobile_balance - $session->initial_mobile_balance)) -
                            $session->total_withdrawals;

            // Calculate any discrepancy
            $session->discrepancy = $session->final_cash - $expectedBalance;

            $session->save();

            DB::commit();

            return redirect()->route('cashier.session', $session->id)
                ->with('success', 'Session de caisse clôturée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cashier.session', $session->id)
                ->with('error', 'Une erreur est survenue lors de la clôture de la session: ' . $e->getMessage());
        }
    }

    public function generateReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Convert to datetime objects for comparison
        $startDateTime = \Carbon\Carbon::parse($startDate . ' 00:00:00');
        $endDateTime = \Carbon\Carbon::parse($endDate . ' 23:59:59');

        $sessions = CashierSession::whereBetween('start_time', [$startDateTime, $endDateTime])
            ->where('user_id', Auth::id())
            ->orderBy('start_time', 'desc')
            ->get();

        $statistics = [
            'total_sessions' => $sessions->count(),
            'total_cash_handled' => $sessions->sum('initial_cash') + $sessions->sum('final_cash'),
            'total_remitted' => $sessions->sum('cash_remitted'),
            'total_withdrawals' => $sessions->sum('total_withdrawals'),
            'total_discrepancies' => $sessions->sum('discrepancy'),
            'mobile_transactions' => $sessions->sum(function ($session) {
                return $session->final_mobile_balance - $session->initial_mobile_balance;
            })
        ];

        return view('cashier.report', compact('sessions', 'statistics', 'startDate', 'endDate'));
    }
}

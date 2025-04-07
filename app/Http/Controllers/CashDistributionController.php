<?php

namespace App\Http\Controllers;

use App\Models\CashDistribution;
use App\Models\User;
use App\Models\VersementCsg;
use App\Models\Transaction;
use App\Models\BagSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashDistributionController extends Controller
{
    public function index(Request $request)
    {
        $query = CashDistribution::with('user');

        // Filtrer par date
        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', Carbon::today());
        }

        // Filtrer par vendeuse
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Filtrer par statut
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $distributions = $query->orderBy('date', 'desc')->paginate(10);

        // Utiliser le champ role directement au lieu de la relation roles
        $sellers = User::where('secteur', 'vente')->get();

        return view('cash.distributions.index', compact('distributions', 'sellers'));
    }

    public function create()
    {
        // Utiliser le champ role directement au lieu de la relation roles
        $sellers = User::where('secteur', 'vente')->get();

        return view('cash.distributions.create', compact('sellers'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'bill_amount' => 'required|numeric|min:0',
            'initial_coin_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Check if the user has the 'vente' sector
        if ($user->secteur !== 'vente') {
            return redirect()->route('cash.distributions.create')
                            ->with('error', 'Seuls les utilisateurs du secteur vente peuvent créer des distributions de monnaie.')
                            ->withInput();
        }

        // Vérifier s'il n'existe pas déjà une distribution pour cette vendeuse à cette date
        $existing = CashDistribution::where('user_id', $user->id)
                                   ->whereDate('date', $validated['date'])
                                   ->first();

        if ($existing) {
            return redirect()->route('cash.distributions.index')
                            ->with('error', 'Une distribution existe déjà pour vous à cette date');
        }

        // Calculer le montant des ventes pour cette vendeuse à cette date
        $salesAmount = DB::table('transaction_ventes')
                        ->where('serveur', $user->id)
                        ->whereDate('date_vente', $validated['date'])
                        ->sum(DB::raw('quantite * prix'));

        $distribution = CashDistribution::create([
            'user_id' => $user->id,
            'date' => $validated['date'],
            'bill_amount' => $validated['bill_amount'],
            'initial_coin_amount' => $validated['initial_coin_amount'],
            'sales_amount' => $salesAmount,
            'notes' => $validated['notes'],
            'status' => 'en_cours'
        ]);

        return redirect()->route('cash.distributions.index')
                        ->with('success', 'Distribution de monnaie créée avec succès');
    }

    public function show(CashDistribution $distribution)
    {
        // Charger les relations nécessaires
        $distribution->load([
            'user',
            'closedByUser'
        ]);

        // Récupérer les détails des ventes pour cette vendeuse à cette date
        $sales = DB::table('transaction_ventes')
            ->join('Produit_fixes', 'transaction_ventes.produit', '=', 'Produit_fixes.code_produit')
            ->where('serveur', $distribution->user_id)
            ->whereDate('date_vente', $distribution->date)
            ->where('type','vente')
            ->select(
                'transaction_ventes.*',
                'Produit_fixes.nom as produit_nom',
                DB::raw('transaction_ventes.quantite * transaction_ventes.prix as total')
            )
            ->orderBy('produit_nom')
            ->get();

        // Calculer le montant total des ventes si nécessaire
        $totalSales = $sales->sum('total');

        // Calculer le montant total des ventes de sacs pour cette vendeuse à cette date
        $bagSalesAmount = 0;
        $bagSales = BagSale::whereHas('reception.assignment', function ($query) use ($distribution) {
            $query->where('user_id', $distribution->user_id);
        })
        ->whereDate('created_at', $distribution->date)
        ->get();

        foreach ($bagSales as $bagSale) {
            $bagPrice = $bagSale->getBagAttribute()->price;
            $bagSalesAmount += $bagPrice * $bagSale->quantity_sold;
        }

        // Si le montant des ventes dans la distribution est différent du calcul actuel
        if ($distribution->sales_amount != $totalSales) {
            $distribution->update([
                'sales_amount' => $totalSales
            ]);
        }

        // Si la distribution est clôturée, recalculer le montant manquant
        if ($distribution->status === 'cloture') {
            $expectedAmount = $distribution->sales_amount + $bagSalesAmount + $distribution->bill_amount +
                ($distribution->initial_coin_amount - $distribution->final_coin_amount);
            $missingAmount = $expectedAmount - $distribution->deposited_amount;

            if ($distribution->missing_amount != $missingAmount) {
                $distribution->update([
                    'missing_amount' => $missingAmount
                ]);
            }
        }

        return view('cash.distributions.show', compact('distribution', 'sales', 'bagSalesAmount'));
    }


    public function edit(CashDistribution $distribution)
    {
        if ($distribution->status === 'cloture') {
            return redirect()->route('cash.distributions.index')
                            ->with('error', 'Impossible de modifier une distribution clôturée');
        }

        $sellers = User::whereHas('roles', function($q) {
            $q->where('name', 'vendeuse');
        })->get();

        return view('cash.distributions.edit', compact('distribution', 'sellers'));
    }

    public function update(Request $request, CashDistribution $distribution)
    {
        if ($distribution->status === 'cloture') {
            return redirect()->route('cash.distributions.index')
                            ->with('error', 'Impossible de modifier une distribution clôturée');
        }

        $validated = $request->validate([
            'bill_amount' => 'required|numeric|min:0',
            'initial_coin_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $distribution->update($validated);

        return redirect()->route('cash.distributions.show', $distribution)
                        ->with('success', 'Distribution de monnaie mise à jour avec succès');
    }

    public function closeForm(CashDistribution $distribution)
    {
        if ($distribution->status === 'cloture') {
            return redirect()->route('cash.distributions.index')
                            ->with('error', 'Cette distribution est déjà clôturée');
        }

        return view('cash.distributions.close', compact('distribution'));
    }

    public function close(Request $request, CashDistribution $distribution)
{
    if ($distribution->status === 'cloture') {
        return redirect()->route('cash.distributions.index')
                        ->with('error', 'Cette distribution est déjà clôturée');
    }

    $validated = $request->validate([
        'final_coin_amount' => 'required|numeric|min:0',
        'deposited_amount' => 'required|numeric|min:0',
    ]);
    // Calculer le montant total des ventes de sacs pour cette vendeuse à cette date
    $bagSalesAmount = 0;
    $bagSales = BagSale::whereHas('reception.assignment', function ($query) use ($distribution) {
        $query->where('user_id', $distribution->user_id);
    })
    ->whereDate('created_at', $distribution->date)
    ->get();

    foreach ($bagSales as $bagSale) {
        $bagPrice = $bagSale->getBagAttribute()->price;
        $bagSalesAmount += $bagPrice * $bagSale->quantity_sold;
    }

    try {
        \DB::beginTransaction();

        $distribution->update([
            'final_coin_amount' => $validated['final_coin_amount'],
            'deposited_amount' => $validated['deposited_amount'],
            'status' => 'cloture',
            'closed_by' => auth()->id(),
            'closed_at' => now()
        ]);

        // Calculer le manquant
        $distribution->calculateMissingAmount();
        $distribution->missing_amount += $bagSalesAmount;

        // Si un montant manquant est détecté
        if ($distribution->missing_amount > 0) {
            // Chercher si l'employé a déjà un enregistrement "en_attente" dans la table
            $existingRecord = \DB::table('manquant_temporaire')
                ->where('employe_id', $distribution->user_id)
                ->where('statut', 'en_attente')
                ->first();

            if ($existingRecord) {
                // Mettre à jour l'enregistrement existant en ajoutant le nouveau montant
                \DB::table('manquant_temporaire')
                    ->where('id', $existingRecord->id)
                    ->update([
                        'montant' => $existingRecord->montant + $distribution->missing_amount,
                        'explication' => $existingRecord->explication . ' + Manquant additionnel de ' .
                            number_format($distribution->missing_amount, 0, ',', ' ') .
                            ' FCFA survenu le ' . now()->format('d/m/Y') .
                            ' après un versement)',
                        'updated_at' => now()
                    ]);
            } else {
                // Créer un nouvel enregistrement si aucun n'existe
                \DB::table('manquant_temporaire')->insert([
                    'employe_id' => $distribution->user_id,
                    'montant' => $distribution->missing_amount,
                    'explication' => 'Manquant de ' . number_format($distribution->missing_amount, 0, ',', ' ') .
                        ' FCFA survenu le ' . now()->format('d/m/Y') .
                        ' après un versement)',
                    'statut' => 'en_attente',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        $distribution->save();

        \DB::commit();

        return redirect()->route('cash.distributions.show', $distribution)
                        ->with('success', 'Distribution clôturée avec succès');
    } catch (\Exception $e) {
        \DB::rollBack();

        return redirect()->back()
                        ->with('error', 'Une erreur est survenue lors de la clôture: ' . $e->getMessage())
                        ->withInput();
    }
}

    public function updateMissingAmount(CashDistribution $distribution)
    {
        if ($distribution->status !== 'cloture') {
            return redirect()->route('cash.distributions.index')
                            ->with('error', 'Impossible de calculer le manquant pour une distribution non clôturée');
        }

        $distribution->calculateMissingAmount();
        $distribution->save();

        return redirect()->route('cash.distributions.show', $distribution)
                        ->with('success', 'Montant manquant recalculé avec succès');
    }

    public function updateSalesAmount(CashDistribution $distribution)
    {
        // Recalculer le montant des ventes
        $salesAmount = DB::table('transaction_ventes')
                        ->where('serveur', $distribution->user_id)
                        ->whereDate('date_vente', $distribution->date)
                        ->sum(DB::raw('quantite * prix'));

        $distribution->update(['sales_amount' => $salesAmount]);

        // Si la distribution est clôturée, recalculer le manquant
        if ($distribution->status === 'cloture') {
            $distribution->calculateMissingAmount();
            $distribution->save();
        }

        return redirect()->route('cash.distributions.show', $distribution)
                        ->with('success', 'Montant des ventes mis à jour avec succès');
    }
}

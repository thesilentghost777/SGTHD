<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

use App\Models\Category;

use Illuminate\Http\Request;

use App\Traits\HistorisableActions;

class TransactionController extends Controller

{
    use HistorisableActions;

    public function index(Request $request)

    {

        $query = Transaction::with('category');

        if ($request->has('search')) {

            $search = $request->get('search');

            $query->where('description', 'like', "%{$search}%")

                  ->orWhereHas('category', function($q) use ($search) {

                      $q->where('name', 'like', "%{$search}%");

                  });

        }

        if ($request->has('sort')) {

            $sort = $request->get('sort');

            $direction = $request->get('direction', 'desc');

            $query->orderBy($sort, $direction);

        } else {

            $query->orderBy('date', 'desc');

        }

        $transactions = $query->paginate(10);

        $categories = Category::all();

        return view('transactions.index', compact('transactions', 'categories'));

    }

    public function store(Request $request)

    {

        $request->validate([

            'type' => 'required|in:income,outcome',

            'category_id' => 'required|exists:categories,id',

            'amount' => 'required|numeric|min:0',

            'date' => 'required|date',

            'description' => 'nullable|string'

        ]);

        Transaction::create($request->all());
        $user = auth()->user();
        $this->historiser("L'utilisateur {$user->name} a créé une transaction de type $request->type de montant $request->amount ", 'create_transaction');

        return redirect()->route('transactions.index')->with('success', 'Transaction créée avec succès');

    }

    public function update(Request $request, Transaction $transaction)

    {

        $request->validate([

            'type' => 'required|in:income,outcome',

            'category_id' => 'required|exists:categories,id',

            'amount' => 'required|numeric|min:0',

            'date' => 'required|date',

            'description' => 'nullable|string'

        ]);
        $user = auth()->user();
        $this->historiser("L'utilisateur {$user->name} a modifier la transaction d'id: {$transaction->id} type $transaction->type -> $request->type de montant $transaction->amount -> $request->amount", 'update_transaction');

        $transaction->update($request->all());

        return redirect()->route('transactions.index')->with('success', 'Transaction mise à jour avec succès');

    }

    public function destroy(Transaction $transaction)

    {

        $transaction->delete();
        $user = auth()->user();
        $this->historiser("L'utilisateur {$user->name} a detruit une transaction: type {$transaction->type} de montant {$transaction->amount} ", 'destroy_transaction');

        return redirect()->route('transactions.index')->with('success', 'Transaction supprimée avec succès');

    }

    public function edit(Transaction $transaction)
    {
        return response()->json($transaction);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\BagAssignment;

use Illuminate\Http\Request;

class BagDiscrepancyController extends Controller

{

    /**

     * Afficher les incohérences entre les sacs assignés et reçus.

     */

    public function index()

    {

        // Pour les chefs de production (voir toutes les assignations)

        if (auth()->user()->role === 'chef') {

            $assignments = BagAssignment::with(['bag', 'user', 'receptions'])

                ->get()

                ->map(function ($assignment) {

                    $assignment->total_received = $assignment->total_received;

                    $assignment->discrepancy = $assignment->discrepancy;

                    return $assignment;

                })

                ->filter(function ($assignment) {

                    // Filtrer pour ne garder que les assignations avec des écarts

                    return $assignment->discrepancy != 0;

                });

        }

        // Pour les serveurs (voir uniquement leurs assignations)

        else {

            $assignments = BagAssignment::with(['bag', 'user', 'receptions'])

                ->where('user_id', auth()->id())

                ->get()

                ->map(function ($assignment) {

                    $assignment->total_received = $assignment->total_received;

                    $assignment->discrepancy = $assignment->discrepancy;

                    return $assignment;

                })

                ->filter(function ($assignment) {

                    // Filtrer pour ne garder que les assignations avec des écarts

                    return $assignment->discrepancy != 0;

                });

        }



        return view('bags.discrepancies.index', compact('assignments'));

    }

}

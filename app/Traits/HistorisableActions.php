<?php

namespace App\Traits;

use App\Http\Controllers\HistoryController;

trait HistorisableActions
{
    /**
     * Enregistre une action dans l'historique
     *
     * @param string $description Description de l'action
     * @param string|null $actionType Type d'action (create, update, delete, etc.)
     * @return void
     */
    protected function historiser($description, $actionType = null)
    {
        $historyController = new HistoryController();
        return $historyController->historiser($description, $actionType);
    }
}
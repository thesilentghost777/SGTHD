<div class="space-y-4">
    @if($fichePaie['manquants'] > 0)
    <div class="flex justify-between py-2 border-b text-red-600">
        <span class="font-medium">Manquants</span>
        <span>- {{ number_format($fichePaie['manquants'], 2) }} FCFA</span>
    </div>
    @endif

    @if($fichePaie['remboursement'] > 0)
    <div class="flex justify-between py-2 border-b text-red-600">
        <span class="font-medium">Remboursement</span>
        <span>- {{ number_format($fichePaie['remboursement'], 2) }} FCFA</span>
    </div>
    @endif

    @if($fichePaie['caisse_sociale'] > 0)
    <div class="flex justify-between py-2 border-b text-red-600">
        <span class="font-medium">Caisse sociale</span>
        <span>- {{ number_format($fichePaie['caisse_sociale'], 2) }} FCFA</span>
    </div>
    @endif

    @if($fichePaie['pret'] > 0)
    <div class="flex justify-between py-2 border-b text-blue-600">
        <span class="font-medium">Prêt en cours</span>
        <span>{{ number_format($fichePaie['pret'], 2) }} FCFA</span>
    </div>
    @endif

</div>

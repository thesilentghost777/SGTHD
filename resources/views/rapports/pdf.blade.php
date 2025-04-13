<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport - {{ $employee->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .header h1 {
            color: #3b82f6;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            margin-top: 0;
        }
        .section {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .section h2 {
            color: #3b82f6;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .grid {
            display: block;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            margin-right: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f1f5f9;
            text-align: left;
            padding: 8px;
            font-size: 12px;
        }
        td {
            padding: 8px;
            font-size: 12px;
        }
        .conclusion {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 5px;
        }
        .text-green {
            color: #10b981;
        }
        .text-red {
            color: #ef4444;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport d'Employé</h1>
        <p>{{ $employee->name }} - {{ ucfirst($employee->role ?? 'Non défini') }}</p>
        <p>Période: {{ $month }} | Date du rapport: {{ now()->format('d/m/Y') }}</p>
    </div>

    <div class="section">
        <h2>Informations générales</h2>
        <div class="info-row">
            <span class="info-label">Date de naissance:</span> {{ $dateNaissance }}
        </div>
        <div class="info-row">
            <span class="info-label">Âge:</span> {{ $age }} ans
        </div>
        <div class="info-row">
            <span class="info-label">Numéro de téléphone:</span> {{ $employee->num_tel ?? 'Non spécifié' }}
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span> {{ $employee->email ?? 'Non spécifié' }}
        </div>
        <div class="info-row">
            <span class="info-label">Année de début de service:</span> {{ $employee->annee_debut_service ?? 'Non spécifiée' }}
        </div>
        <div class="info-row">
            <span class="info-label">Années de service:</span> {{ $anneeService }}
        </div>
        <div class="info-row">
            <span class="info-label">Jours de présence ce mois:</span> {{ $joursPresence }}
        </div>
        <div class="info-row">
            <span class="info-label">Total d'heures travaillées:</span> {{ $totalHeuresTravail }}
        </div>
    </div>

    <div class="section">
        <h2>Salaire et finances</h2>
        <div class="info-row">
            <span class="info-label">Salaire mensuel:</span> {{ number_format($salaire, 0, ',', ' ') }} FCFA
        </div>
        <div class="info-row">
            <span class="info-label">Avance sur salaire:</span> {{ number_format($avanceSalaire, 0, ',', ' ') }} FCFA
        </div>
        <div class="info-row">
            <span class="info-label">Total des primes:</span> {{ number_format($totalPrimes, 0, ',', ' ') }} FCFA
        </div>

        @if($acouper)
        <div class="info-row">
            <span class="info-label">Montants à déduire:</span>
            @if($acouper->manquants > 0)
                Manquants: {{ number_format($acouper->manquants, 0, ',', ' ') }} FCFA
            @endif
            @if($acouper->remboursement > 0)
                | Remboursement: {{ number_format($acouper->remboursement, 0, ',', ' ') }} FCFA
            @endif
            @if($acouper->pret > 0)
                | Prêt: {{ number_format($acouper->pret, 0, ',', ' ') }} FCFA
            @endif
            @if($acouper->caisse_sociale > 0)
                | Caisse sociale: {{ number_format($acouper->caisse_sociale, 0, ',', ' ') }} FCFA
            @endif
        </div>
        @endif

        @if(count($primes) > 0)
        <h3>Détail des primes:</h3>
        <table>
            <thead>
                <tr>
                    <th>Libellé</th>
                    <th>Montant</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($primes as $prime)
                <tr>
                    <td>{{ $prime->libelle }}</td>
                    <td>{{ number_format($prime->montant, 0, ',', ' ') }} FCFA</td>
                    <td>{{ $prime->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    @if($evaluation)
    <div class="section">
        <h2>Évaluation</h2>
        <div class="info-row">
            <span class="info-label">Note:</span> {{ $evaluation->note }}/10
        </div>
        <div class="info-row">
            <span class="info-label">Appréciation:</span> {{ $evaluation->appreciation }}
        </div>
    </div>
    @endif

    @if($reposConge)
    <div class="section">
        <h2>Congés et repos</h2>
        <div class="info-row">
            <span class="info-label">Jour de repos hebdomadaire:</span> {{ ucfirst($reposConge->jour) }}
        </div>
        @if($reposConge->conges)
        <div class="info-row">
            <span class="info-label">Jours de congés disponibles:</span> {{ $reposConge->conges }}
        </div>
        @endif
        @if($reposConge->debut_c)
        <div class="info-row">
            <span class="info-label">Début du dernier congé:</span> {{ \Carbon\Carbon::parse($reposConge->debut_c)->format('d/m/Y') }}
        </div>
        <div class="info-row">
            <span class="info-label">Raison:</span> {{ ucfirst($reposConge->raison_c ?? 'Non spécifiée') }}
        </div>
        @if($reposConge->autre_raison)
        <div class="info-row">
            <span class="info-label">Détail:</span> {{ $reposConge->autre_raison }}
        </div>
        @endif
        @endif
    </div>
    @endif

    @if(count($delits) > 0)
    <div class="section">
        <h2>Délits et incidents</h2>
        <table>
            <thead>
                <tr>
                    <th>Délit</th>
                    <th>Description</th>
                    <th>Montant</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($delits as $delit)
                <tr>
                    <td>{{ $delit->deli->nom }}</td>
                    <td>{{ $delit->deli->description }}</td>
                    <td>{{ number_format($delit->deli->montant, 0, ',', ' ') }} FCFA</td>
                    <td>{{ \Carbon\Carbon::parse($delit->date_incident)->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="info-row">
            <span class="info-label">Montant total des délits:</span> {{ number_format($totalDelits, 0, ',', ' ') }} FCFA
        </div>
    </div>
    @endif

    @if($employee->role == 'vendeur')
    <div class="section">
        <h2>Performance de vente</h2>
        <div class="info-row">
            <span class="info-label">Chiffre d'affaires du mois:</span> {{ number_format($chiffreAffaires, 0, ',', ' ') }} FCFA
        </div>
        <div class="info-row">
            <span class="info-label">Nombre de transactions:</span> {{ $nbTransactions }}
        </div>
        <div class="info-row">
            <span class="info-label">Moyenne journalière:</span> {{ number_format($moyenneVentesParJour, 0, ',', ' ') }} FCFA
        </div>
    </div>
    @elseif($employee->role == 'boulanger' || $employee->role == 'patissier')
    <div class="section">
        <h2>Performance de production</h2>
        <div class="info-row">
            <span class="info-label">Valeur totale de production:</span> {{ number_format($valeurTotaleProduction, 0, ',', ' ') }} FCFA
        </div>
        <div class="info-row">
            <span class="info-label">Coût des matières premières:</span> {{ number_format($coutMatieresPremieres, 0, ',', ' ') }} FCFA
        </div>
        <div class="info-row">
            <span class="info-label">Ratio dépense/gain:</span>
            <span class="{{ $ratioDepenseGain >= 1 ? 'text-green' : 'text-red' }}">
                {{ number_format($ratioDepenseGain, 2, ',', ' ') }}
                ({{ $ratioDepenseGain >= 1 ? 'Rentable' : 'Non rentable' }})
            </span>
        </div>
    </div>
    @endif

    <div class="section conclusion">
        <h2>Conclusion</h2>
        <p>
            @if($employee->role == 'vendeur')
                <span style="font-weight: bold;">{{ $employee->name }}</span> a effectué
                <span style="font-weight: bold;">{{ $nbTransactions }}</span> transactions ce mois-ci,
                générant un chiffre d'affaires de
                <span style="font-weight: bold;">{{ number_format($chiffreAffaires, 0, ',', ' ') }} FCFA</span>.
                @if($joursPresence > 0)
                    Sa performance quotidienne moyenne est de
                    <span style="font-weight: bold;">{{ number_format($moyenneVentesParJour, 0, ',', ' ') }} FCFA</span>.
                @endif

                @if($acouper && ($acouper->manquants > 0 || $acouper->remboursement > 0 || $acouper->pret > 0 || $acouper->caisse_sociale > 0))
                    Des déductions d'un montant total de
                    <span style="font-weight: bold;">{{ number_format($acouper->manquants + $acouper->remboursement + $acouper->pret + $acouper->caisse_sociale, 0, ',', ' ') }} FCFA</span>
                    seront appliquées à son salaire.
                @endif

                @if($totalPrimes > 0)
                    L'employé a reçu des primes d'un montant total de
                    <span style="font-weight: bold;">{{ number_format($totalPrimes, 0, ',', ' ') }} FCFA</span> ce mois-ci.
                @endif
            @elseif($employee->role == 'boulanger' || $employee->role == 'patissier')
                <span style="font-weight: bold;">{{ $employee->name }}</span> a produit des articles d'une valeur totale de
                <span style="font-weight: bold;">{{ number_format($valeurTotaleProduction, 0, ',', ' ') }} FCFA</span> ce mois-ci,
                utilisant des matières premières d'un coût de
                <span style="font-weight: bold;">{{ number_format($coutMatieresPremieres, 0, ',', ' ') }} FCFA</span>.

                @if($ratioDepenseGain >= 1)
                    Avec un ratio dépense/gain de <span style="font-weight: bold; color: #10b981;">{{ number_format($ratioDepenseGain, 2, ',', ' ') }}</span>,
                    sa production est rentable pour l'entreprise.
                @else
                    Avec un ratio dépense/gain de <span style="font-weight: bold; color: #ef4444;">{{ number_format($ratioDepenseGain, 2, ',', ' ') }}</span>,
                    sa production n'est actuellement pas rentable pour l'entreprise.
                @endif

                @if($acouper && ($acouper->manquants > 0 || $acouper->remboursement > 0 || $acouper->pret > 0 || $acouper->caisse_sociale > 0))
                    Des déductions d'un montant total de
                    <span style="font-weight: bold;">{{ number_format($acouper->manquants + $acouper->remboursement + $acouper->pret + $acouper->caisse_sociale, 0, ',', ' ') }} FCFA</span>
                    seront appliquées à son salaire.
                @endif

                @if($totalPrimes > 0)
                    L'employé a reçu des primes d'un montant total de
                    <span style="font-weight: bold;">{{ number_format($totalPrimes, 0, ',', ' ') }} FCFA</span> ce mois-ci.
                @endif
            @else
                <span style="font-weight: bold;">{{ $employee->name }}</span> a été présent
                <span style="font-weight: bold;">{{ $joursPresence }}</span> jours ce mois-ci,
                cumulant un total de <span style="font-weight: bold;">{{ $totalHeuresTravail }}</span> heures de travail.

                @if($acouper && ($acouper->manquants > 0 || $acouper->remboursement > 0 || $acouper->pret > 0 || $acouper->caisse_sociale > 0))
                    Des déductions d'un montant total de
                    <span style="font-weight: bold;">{{ number_format($acouper->manquants + $acouper->remboursement + $acouper->pret + $acouper->caisse_sociale, 0, ',', ' ') }} FCFA</span>
                    seront appliquées à son salaire.
                @endif

                @if($totalPrimes > 0)
                    L'employé a reçu des primes d'un montant total de
                    <span style="font-weight: bold;">{{ number_format($totalPrimes, 0, ',', ' ') }} FCFA</span> ce mois-ci.
                @endif
            @endif
        </p>
    </div>

    <div class="footer">
        <p>Rapport généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

</body>
</html>

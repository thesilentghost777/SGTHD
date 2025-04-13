<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attestation de Stage - {{ $stagiaire->nom }} {{ $stagiaire->prenom }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }
        h2 {
            color: #2c5282;
            font-size: 20px;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            color: #2c5282;
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin-left: auto;
            margin-top: 50px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>TH MARKET</h1>
        <h2>Attestation de Stage</h2>
    </div>

    <div class="section">
        <p>Nous, TH MARKET, attestons par la présente que <strong>{{ $stagiaire->nom }} {{ $stagiaire->prenom }}</strong>, étudiant(e) en <strong>{{ $stagiaire->filiere }}</strong> à <strong>{{ $stagiaire->ecole }}</strong>, a effectué un stage au sein de notre entreprise.</p>
    </div>

    <div class="section">
        <div class="section-title">Coordonnées du Stagiaire</div>
        <p>Email : <strong>{{ $stagiaire->email }}</strong></p>
        <p>Téléphone : <strong>{{ $stagiaire->telephone }}</strong></p>
    </div>

    <div class="section">
        <div class="section-title">Période de Stage</div>
        <p>Le stage s'est déroulé du <strong>{{ $stagiaire->date_debut->format('d/m/Y') }}</strong> au <strong>{{ $stagiaire->date_fin->format('d/m/Y') }}</strong>, permettant à {{ $stagiaire->prenom }} d'acquérir des compétences pratiques en lien avec sa formation.</p>
    </div>

    <div class="section">
        <div class="section-title">Département d'Affection</div>
        <p>{{ $stagiaire->departement }}</p>
    </div>

    <div class="section">
        <div class="section-title">Missions et Travaux Réalisés</div>
        <p>Durant cette période, {{ $stagiaire->prenom }} a participé activement aux différentes missions qui lui ont été confiées, notamment le travail de:</p><p>{{ $stagiaire->nature_travail }}</p>
    </div>

    @if($stagiaire->type_stage === 'professionnel')
    <div class="section">
        <div class="section-title">Rémunération</div>
        <p>Une indemnité de stage de <strong>{{ number_format($stagiaire->remuneration, 2) }} F CFA</strong> lui a été attribuée.</p>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Appréciation</div>
        <p>{{ $stagiaire->appreciation ?? 'Le stagiaire a fait preuve de sérieux et d’engagement tout au long de son stage.' }}</p>
    </div>

    <div class="footer">
        <p>Fait à _____________, le {{ now()->format('d/m/Y') }}</p>
        <div class="signature-line">
            <p>Le Directeur Général</p>
        </div>
    </div>
</body>
</html>

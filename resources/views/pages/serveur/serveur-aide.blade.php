<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aide</title>
    <style>
 /* ====== SECTION PRINCIPALE ====== */
.help-section {
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 8px;
    width: 100vh;
    max-width: 800px;
    margin: 20px auto;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    font-family: 'Arial', sans-serif;
}

/* ====== TITRE ====== */
.help-title {
    font-size: 24px;
    color: #333;
    margin-bottom: 15px;
    text-align: center;
}

/* ====== INTRODUCTION ====== */
.help-intro {
    font-size: 16px;
    color: #555;
    margin-bottom: 20px;
    line-height: 1.6;
    text-align: justify;
}

/* ====== LISTE ====== */
.help-list {
    list-style-type: none;
    padding: 0;
}

.help-list li {
    font-size: 16px;
    margin-bottom: 10px;
    line-height: 1.6;
    color: #333;
}

.help-list strong {
    color: #007BFF;
}

/* ====== PIED DE PAGE ====== */
.help-footer {
    margin-top: 20px;
    font-size: 14px;
    color: #666;
    text-align: center;
}

/* ====== LIEN DE TÉLÉCHARGEMENT ====== */
.help-download {
    color: #007BFF;
    text-decoration: none;
}

.help-download:hover {
    text-decoration: underline;
}

/* ====== RESPONSIVE DESIGN ====== */
@media screen and (max-width: 768px) {
    .help-section {
        padding: 15px;
    }

    .help-title {
        font-size: 20px;
    }

    .help-intro {
        font-size: 14px;
    }

    .help-list li {
        font-size: 14px;
    }

    .help-footer {
        font-size: 12px;
    }
}

@media screen and (max-width: 480px) {
    .help-title {
        font-size: 18px;
    }

    .help-intro {
        font-size: 13px;
    }

    .help-list li {
        font-size: 13px;
    }

    .help-footer {
        font-size: 11px;
    }
}

     </style>
</head>
<body>
    <!-- Modal Guide d'Utilisation -->
<!-- Fenêtre modale - Guide d'Utilisation -->
<div class="help-section">
    <h2 class="help-title">📘 Guide d'Utilisation</h2>
    <p class="help-intro">
        Bienvenue dans l'application <strong>TH MARKET</strong> ! Voici un guide rapide pour vous aider à naviguer efficacement :
    </p>
    
    <ul class="help-list">
        <li><strong>🛒 Produits reçus :</strong> Enregistrez les produits que vous avez reçus aujourd'hui.</li>
        <li><strong>💰 Ventes du jour :</strong> Enregistrez les ventes effectuées dans la journée.</li>
        <li><strong>📦 Produits invendus :</strong> Consultez la liste des produits non vendus.</li>
        <li><strong>💳 Versements :</strong> Gérez les versements en caisse et au CP.</li>
        <li><strong>📊 Statistiques :</strong> Consultez les statistiques de vos ventes.</li>
    </ul>

    <p class="help-footer">
        Si vous avez des questions supplémentaires, veuillez contacter l'administrateur.
    </p>
    <p class="help-footer">
        📄 Vous pouvez également <a href="{{ asset('docs/Guide_Utilisation.pdf') }}" download class="help-download">télécharger le guide d'utilisation en PDF</a>.
    </p>
</div>

</body>
</html>
@include('pages/dg/dg_default')

@vite(['resources/css/dg/dg-dashboard.css','resources/js/dg/dg-dashboard.js'])
<!DOCTYPE html>
<html><head><base href="/" />
<title>
    Formulaire avec Affichage Dynamique
</title>
</head>
<body>
<div class="main-content">
<div class="stats-container">
            <div class="stat-card">
                <h3>Chiffre d'affaires</h3>
                <p class="stat-value">2,500,000 FCFA</p>
                <span class="stat-trend positive">+15% vs mois dernier</span>
            </div>
            <div class="stat-card">
                <h3>Bénéfice net</h3>
                <p class="stat-value">850,000 FCFA</p>
                <span class="stat-trend positive">+8% vs mois dernier</span>
            </div>
            <div class="stat-card">
                <h3>Dépenses</h3>
                <p class="stat-value">450,000 FCFA</p>
                <span class="stat-trend negative">+12% vs mois dernier</span>
            </div>
            <div class="stat-card">
                <h3>Effectif total</h3>
                <p class="stat-value">45</p>
                <span class="stat-trend neutral">Stable</span>
            </div>
        </div>

        <div class="global-charts">
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Demandes AS en attente</h3>
                <div class="as-requests">
                    <!-- AS requests list -->
                </div>
            </div>

            <div class="dashboard-card">
                <h3>Performance chefs de production</h3>
                <div class="performance-ratings">
                    Je vais passer en revue toutes les tables et lister toutes les statistiques possibles, par catégorie :

**Statistiques liées aux Employés/RH :**
- Taux de ponctualité par employé (via table Horaire)
- Moyenne des heures travaillées par jour/semaine/mois par employé
- Taux d'absentéisme par secteur
- Evolution des salaires moyens par secteur/rôle
- Classement des employés par ancienneté (année_debut_service)
- Ratio des avances sur salaire par rapport au salaire total
- Taux d'utilisation des congés par employé/département
- Evolution des primes par employé/période
- Classement des employés par montant total des primes reçues
- Top/Bottom des employés par ponctualité
- Ratio des remboursements/prêts par employé
- Taux de rotation du personnel par secteur
- Distribution des rôles dans l'entreprise
- Evolution des notes d'évaluation par employé
- Moyenne des évaluations par secteur

**Statistiques Production :**
- Taux de réalisation des objectifs de production quotidiens
- Ratio production réelle vs production suggérée
- Classement des producteurs par quantité produite
- Evolution de la productivité par employé/période
- Taux d'utilisation des matières premières par produit
- Ratio production/matière première utilisée
- Top/Bottom des produits par quantité produite
- Taux de respect des assignations quotidiennes
- Evolution des quantités produites par période/produit
- Rendement matière première par produit
- Taux d'écart entre production attendue et réalisée

**Statistiques Matières Premières :**
- Taux de consommation par matière première
- Evolution des prix unitaires par matière
- Ratio utilisation/stock par matière
- Classement des matières par coût total
- Taux de réservation approuvée vs refusée
- Evolution des stocks par matière
- Taux d'utilisation des assignations de matière
- Ratio coût matière première/produit fini
- Taux de perte par matière première
- Top/Bottom des matières par fréquence d'utilisation

**Statistiques Ventes/Finance :**
- Evolution du chiffre d'affaires quotidien/mensuel/annuel
- Taux de marge par produit
- Ratio ventes/production par produit
- Classement des produits par revenu généré
- Evolution des prix de vente
- Taux de rendement par type de monnaie
- Part des ventes par catégorie de produit
- Top/Bottom des serveurs par chiffre d'affaires
- Ratio revenu/coût par produit
- Evolution des versements CSG
- Taux de validation des versements
- Evolution du solde du complexe
- Ratio dépenses/revenus

**Statistiques Stocks :**
- Taux de rotation des stocks par produit
- Ratio produits invendus/production totale
- Evolution des quantités en stock
- Taux de produits avariés
- Ratio stock/ventes par produit
- Fréquence de rupture de stock
- Taux d'utilisation de la capacité de stockage
- Evolution des niveaux de stock minimum
- Top/Bottom des produits par durée en stock
- Ratio stock moyen/ventes moyennes

**Statistiques Commandes :**
- Taux de validation des commandes
- Délai moyen de traitement des commandes
- Evolution du volume de commandes par période
- Ratio commandes validées/refusées
- Distribution des commandes par catégorie
- Top/Bottom des produits commandés
- Taux de respect des délais de livraison
- Evolution des quantités commandées par produit

**Statistiques Sacs :**
- Taux de rotation des stocks de sacs
- Ratio ventes/réceptions de sacs
- Evolution des prix des sacs
- Fréquence des ruptures de stock
- Ratio stock/seuil d'alerte
- Top/Bottom des types de sacs par vente

**Statistiques Dépenses :**
- Répartition des dépenses par type
- Evolution des coûts par catégorie
- Ratio dépenses/revenus par période
- Top/Bottom des postes de dépenses
- Taux de validation des dépenses
- Evolution des coûts de réparation
- Ratio coûts matières/coûts totaux

                </div>
            </div>
</div>
</div>
</body>
</html>

<!-- resources/views/mode-selection.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>TH MARKET - Sélection du mode</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FF6B6B;
            --primary-dark: #FF5252;
            --accent: #FFD93D;
            --dark: #2C3E50;
            --light: #F8F9FA;
            --success: #4CAF50;
            --success-dark: #388E3C;
            --font-main: 'Poppins', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--dark);
        }

        .header {
            background: linear-gradient(to right, #1a1a1a, #2c2c2c, #1a1a1a);
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 15px rgba(0,0,0,0.2);
        }

        .header .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .header svg {
            width: 35px;
            height: 35px;
            fill: var(--primary);
            filter: drop-shadow(0 0 5px var(--primary));
        }

        .header h1 {
            font-size: 1.6rem;
            color: white;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .container {
            flex: 1;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            max-width: 500px;
            margin: 0 auto;
            width: 100%;
        }

        .title-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .title-section h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .title-section p {
            color: #666;
            font-size: 0.95rem;
        }

        .mode-selection {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            width: 100%;
        }

        .mode-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 130px;
            position: relative;
        }

        .mode-card:active {
            transform: scale(0.98);
        }

        .mode-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: white;
            height: 100%;
            padding: 1.5rem;
            text-align: center;
        }

        .production-mode {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }

        .sales-mode {
            background: linear-gradient(135deg, var(--success), var(--success-dark));
        }

        .mode-icon {
            font-size: 2rem;
            margin-bottom: 0.7rem;
        }

        .mode-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .mode-description {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-top: 0.3rem;
        }

        .ripple {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.4);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        .footer {
            text-align: center;
            padding: 1rem;
            font-size: 0.8rem;
            color: #666;
            background: #f1f1f1;
        }

        /* Animation pour les cartes */
        .mode-card {
            animation: fadeInUp 0.5s ease backwards;
        }

        .mode-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .mode-card:nth-child(2) {
            animation-delay: 0.3s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Media Queries */
        @media (min-width: 768px) {
            .mode-selection {
                flex-direction: row;
            }

            .mode-card {
                flex: 1;
                height: 150px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .title-section h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

    <main class="container">
        <div class="title-section">
            <h2>Choisissez votre mode</h2>
        </div>

        <div class="mode-selection">
            <div class="mode-card">
                <a href="{{ route('producteur.workspace') }}" class="mode-link production-mode">
                    <div class="mode-icon">🏭</div>
                    <div class="mode-title">Mode Production</div>
                    <div class="mode-description">Gestion des stocks et préparations</div>
                </a>
            </div>

            <div class="mode-card">
                <a href="{{ route('seller.workspace') }}" class="mode-link sales-mode">
                    <div class="mode-icon">🛒</div>
                    <div class="mode-title">Mode Vente</div>
                    <div class="mode-description">Caisse et transactions</div>
                </a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>© 2025 TH MARKET - Powered by Silent Ghost Coding</p>
    </footer>

    <script>
        // Effet de ripple (onde) au clic
        const modeCards = document.querySelectorAll('.mode-card');

        modeCards.forEach(card => {
            card.addEventListener('click', function(e) {
                const x = e.clientX - e.target.getBoundingClientRect().left;
                const y = e.clientY - e.target.getBoundingClientRect().top;

                const ripple = document.createElement('span');
                ripple.classList.add('ripple');
                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;

                this.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    </script>
</body>
</html>

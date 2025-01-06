<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erreur - Avance sur Salaire</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f5f5f5;
            overflow: hidden;
        }

        .intro-error {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f5f5f5;
            z-index: 1000;
            animation: fadeOut 0.5s ease-in-out 2s forwards;
        }

        .intro-text {
            color: rgb(185, 28, 28);
            font-size: 2rem;
            font-weight: bold;
            opacity: 0;
            animation: scaleUp 2s ease-in-out forwards;
        }

        @keyframes scaleUp {
            0% {
                transform: scale(1);
                opacity: 0;
            }
            20% {
                opacity: 1;
            }
            100% {
                transform: scale(3);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
                visibility: hidden;
            }
        }

        .error {
            width: 100%;
            max-width: 500px;
            opacity: 0;
            animation: fadeIn 0.5s ease-in-out 2.5s forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-box {
            background-color: rgb(254, 242, 242);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                        0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(239, 68, 68, 0.2);
            transform: translateY(0);
            transition: all 0.2s ease;
        }

        .error-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px -1px rgba(0, 0, 0, 0.1),
                        0 4px 6px -1px rgba(0, 0, 0, 0.06);
        }

        .error-title {
            color: rgb(153, 27, 27);
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(239, 68, 68, 0.2);
        }

        .error-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
            margin-bottom: 1.5rem;
        }

        .error-item {
            display: flex;
            align-items: center;
            color: rgb(185, 28, 28);
            font-size: 0.925rem;
            margin-bottom: 0.75rem;
            padding: 0.5rem;
            background-color: rgba(254, 242, 242, 0.7);
            border-radius: 0.5rem;
            transition: background-color 0.2s ease;
        }

        .error-item:hover {
            background-color: rgba(254, 242, 242, 1);
        }

        .error-item:last-child {
            margin-bottom: 0;
        }

        .status-icon {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(239, 68, 68, 0.1);
            border-radius: 50%;
            padding: 4px;
        }

        .date-info {
            margin-left: auto;
            color: rgb(239, 68, 68);
            font-size: 0.85rem;
            opacity: 0.8;
        }

        .contact-info {
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 1rem;
            border-top: 1px solid rgba(239, 68, 68, 0.2);
            color: rgb(185, 28, 28);
            font-size: 0.925rem;
        }

        .contact-icon {
            margin-right: 0.5rem;
            opacity: 0.8;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .contact-info:hover .contact-icon {
            animation: pulse 1s infinite;
        }
    </style>
</head>
<body>
    <div class="intro-error">
        <div class="intro-text">ERROR</div>
    </div>

    <div class="error">
        <div class="error-box">
            <h4 class="error-title">Vérifiez les éléments suivants :</h4>
            <ul class="error-list">
                <li class="error-item">
                    <span class="status-icon" id="dateIcon"></span>
                    Nous sommes au-delà du 9 de ce mois
                    <span class="date-info" id="currentDate"></span>
                </li>
                <li class="error-item">
                    <span class="status-icon">✓</span>
                    Vous n'avez pas encore fait de requête d'AS ce mois
                </li>
            </ul>
            <div class="contact-info">
                <svg class="contact-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                </svg>
                <a href="/contact_us" style="text-decoration: none; color:rgb(185, 28, 28)">
                Contactez l'administration
                </a>
            </div>
        </div>
    </div>

    <script>
        // Format date as dd/mm/yyyy
        function formatDate(date) {
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }

        // Check if current day is after the 9th
        function updateDateStatus() {
            const now = new Date();
            const dateIcon = document.getElementById('dateIcon');
            dateIcon.textContent = now.getDate() >= 9 ? '✓' : '✗';

            // Update current date display
            const currentDate = document.getElementById('currentDate');
            currentDate.textContent = `(Aujourd'hui: ${formatDate(now)})`;
        }

        // Initialize
        updateDateStatus();
    </script>
</body>
</html>

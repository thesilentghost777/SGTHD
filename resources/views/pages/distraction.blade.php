<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chargement...</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f4ff;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            color: #333;
        }

        .container {
            text-align: center;
            width: 100%;
            max-width: 600px;
        }

        .animation-container {
            position: relative;
            height: 300px;
            margin-bottom: 40px;
        }

        .rocket {
            position: absolute;
            width: 80px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .star {
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: #FFD700;
            border-radius: 50%;
            opacity: 0;
        }

        .planet {
            position: absolute;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            opacity: 0;
        }

        #progress-container {
            width: 100%;
            background-color: #ddd;
            border-radius: 10px;
            height: 20px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        #progress-bar {
            height: 100%;
            background-color: #4CAF50;
            width: 0%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .message {
            font-size: 18px;
            margin-top: 20px;
            line-height: 1.6;
        }

        .language-toggle {
            margin-top: 20px;
            cursor: pointer;
            color: #3498db;
            font-weight: bold;
        }

        .planet-1 { background-color: #ff6b6b; }
        .planet-2 { background-color: #48dbfb; }
        .planet-3 { background-color: #1dd1a1; }
        .planet-4 { background-color: #f368e0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="animation-container" id="space-scene">
            <!-- Les éléments animés seront ajoutés par JavaScript -->
            <div class="rocket">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2.5C11.1716 2.5 10.5 3.17157 10.5 4V4.5H9C7.89543 4.5 7 5.39543 7 6.5V7H6.5C5.67157 7 5 7.67157 5 8.5C5 9.32843 5.67157 10 6.5 10H7V17.5C7 18.0523 7.44772 18.5 8 18.5C8.55228 18.5 9 18.0523 9 17.5V10H15V17.5C15 18.0523 15.4477 18.5 16 18.5C16.5523 18.5 17 18.0523 17 17.5V10H17.5C18.3284 10 19 9.32843 19 8.5C19 7.67157 18.3284 7 17.5 7H17V6.5C17 5.39543 16.1046 4.5 15 4.5H13.5V4C13.5 3.17157 12.8284 2.5 12 2.5Z" fill="#FF5722"/>
                    <path d="M8 20.5C8 19.6716 8.67157 19 9.5 19H14.5C15.3284 19 16 19.6716 16 20.5C16 21.3284 15.3284 22 14.5 22H9.5C8.67157 22 8 21.3284 8 20.5Z" fill="#E64A19"/>
                </svg>
            </div>
        </div>

        <div id="progress-container">
            <div id="progress-bar"></div>
        </div>

        <div class="message" id="french-message">
            Nous configurons votre compte pour mieux vous accueillir dans l'application. Veuillez patienter SVP...
        </div>

        <div class="message" id="english-message" style="display: none;">
            We are setting up your account to better welcome you to the application. Please wait...
        </div>

        <div class="language-toggle" id="toggle-language">
            Switch to English / Passer au français
        </div>
    </div>

    <script>
        // Animation GSAP
        function createSpaceScene() {
            const spaceScene = document.getElementById('space-scene');
            const rocket = document.querySelector('.rocket');

            // Animation de la fusée
            gsap.to(rocket, {
                y: -20,
                duration: 1.5,
                repeat: -1,
                yoyo: true,
                ease: "power1.inOut"
            });

            // Rotation de la fusée
            gsap.to(rocket, {
                rotation: 10,
                duration: 3,
                repeat: -1,
                yoyo: true,
                ease: "sine.inOut"
            });

            // Créer plusieurs étoiles
            for (let i = 0; i < 20; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                star.style.left = `${Math.random() * 100}%`;
                star.style.top = `${Math.random() * 100}%`;
                spaceScene.appendChild(star);

                // Animation des étoiles
                gsap.to(star, {
                    opacity: Math.random() * 0.8 + 0.2,
                    scale: Math.random() * 1.5 + 0.5,
                    duration: Math.random() * 2 + 1,
                    repeat: -1,
                    yoyo: true,
                    ease: "sine.inOut",
                    delay: Math.random() * 2
                });
            }

            // Créer des planètes
            for (let i = 1; i <= 4; i++) {
                const planet = document.createElement('div');
                planet.className = `planet planet-${i}`;
                planet.style.left = `${Math.random() * 80 + 10}%`;
                planet.style.top = `${Math.random() * 80 + 10}%`;
                spaceScene.appendChild(planet);

                // Animation des planètes
                gsap.fromTo(planet,
                    { opacity: 0, scale: 0.5 },
                    {
                        opacity: 1,
                        scale: 1,
                        duration: 4,
                        repeat: -1,
                        yoyo: true,
                        ease: "power1.inOut",
                        delay: i * 3
                    }
                );

                // Déplacement orbital
                gsap.to(planet, {
                    rotation: 360,
                    transformOrigin: "center center",
                    duration: Math.random() * 30 + 20,
                    repeat: -1,
                    ease: "none"
                });
            }
        }

        // Gestion de la progression
        let progress = 0;
        const progressBar = document.getElementById('progress-bar');
        const progressTimer = setInterval(() => {
            progress++;
            progressBar.style.width = `${progress / 60 * 100}%`;

            if (progress >= 60) {
                clearInterval(progressTimer);
                // Ici, vous pourriez rediriger l'utilisateur ou déclencher une autre action
                setTimeout(() => {
                    alert("Configuration terminée !");
                }, 500);
            }
        }, 1000);

        // Alternance français/anglais
        const toggleBtn = document.getElementById('toggle-language');
        const frenchMsg = document.getElementById('french-message');
        const englishMsg = document.getElementById('english-message');
        let isEnglish = false;

        toggleBtn.addEventListener('click', () => {
            if (isEnglish) {
                frenchMsg.style.display = 'block';
                englishMsg.style.display = 'none';
                isEnglish = false;
            } else {
                frenchMsg.style.display = 'none';
                englishMsg.style.display = 'block';
                isEnglish = true;
            }
        });

        // Initialiser la scène d'espace
        window.onload = function() {
            createSpaceScene();
        };
    </script>
</body>
</html>
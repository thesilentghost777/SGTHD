@vite(['resources/css/index.css','resources/js/index.js'])
<!DOCTYPE html>
<html lang="fr">
<head>
  <base href="https://www.thmarket.sn">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>TH MARKET - Boulangerie Pâtisserie</title>
  <meta name="description" content="TH MARKET - Solution de gestion complète pour votre boulangerie pâtisserie">
</head>
<body>
  <header class="header">
    <div class="logo">
      <svg viewBox="0 0 24 24">
        <path d="M12,3L4,9V21H20V9L12,3M12,5.5L18,10V19H6V10L12,5.5M12,7A3,3 0 0,0 9,10C9,11.31 9.83,12.42 11,12.83V17H13V12.83C14.17,12.42 15,11.31 15,10A3,3 0 0,0 12,7Z"/>
      </svg>
      <h1>TH MARKET</h1>
    </div>
    <div class="menu-toggle">
      <div class="bar"></div>
      <div class="bar"></div>
      <div class="bar"></div>
    </div>
  </header>

  <nav id="nav3">
    <ul>
      <li><a href="{{ route('login') }}">Connexion</a></li>
      <li><a href="{{ route('register') }}">Enregistrement</a></li>
    </ul>
  </nav>

  <section class="hero">
    <div id="hero_text">
      <h2>Easy Gest</h2>
      <p>Simplifiez la gestion de votre structure avec notre solution complète. Tout au même endroit.</p>
    </div>
  </section>

  <section class="features">
    <div class="feature-card">
      <svg viewBox="0 0 24 24">
        <path d="M16,13C15.71,13 15.38,13 15.03,13.05C16.19,13.89 17,15 17,16.5V19H23V16.5C23,14.17 18.33,13 16,13M8,13C5.67,13 1,14.17 1,16.5V19H15V16.5C15,14.17 10.33,13 8,13M8,11A3,3 0 0,0 11,8A3,3 0 0,0 8,5A3,3 0 0,0 5,8A3,3 0 0,0 8,11M16,11A3,3 0 0,0 19,8A3,3 0 0,0 16,5A3,3 0 0,0 13,8A3,3 0 0,0 16,11Z"/>
      </svg>
      <h3>Gestion des Employés</h3>
      <p>Gérez efficacement vos équipes et leurs plannings</p>
    </div>

    <div class="feature-card">
      <svg viewBox="0 0 24 24">
        <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,5V19H5V5H19Z"/>
      </svg>
      <h3>Gestion des Stocks</h3>
      <p>Contrôlez vos inventaires et anticipez vos besoins en matières premières</p>
    </div>

    <div class="feature-card">
      <svg viewBox="0 0 24 24">
        <path d="M21,18V19A2,2 0 0,1 19,21H5C3.89,21 3,20.1 3,19V5A2,2 0 0,1 5,3H19A2,2 0 0,1 21,5V6H12C10.89,6 10,6.9 10,8V16A2,2 0 0,0 12,18M12,16V8H21V16"/>
      </svg>
      <h3>Mini Comptabilité</h3>
      <p>Suivez vos revenus et dépenses simplement et efficacement</p>
    </div>
  </section>

  <footer>
    <p>© 2025 TH MARKET - Powered by Silent Ghost Coding</p>
  </footer>

  <script>
  document.querySelector('.menu-toggle').addEventListener('click', function() {
    this.classList.toggle('active');
    document.querySelector('nav').classList.toggle('active');
  });

  // Fermer le menu lors du clic sur un lien
  document.querySelectorAll('nav a').forEach(link => {
    link.addEventListener('click', () => {
      document.querySelector('.menu-toggle').classList.remove('active');
      document.querySelector('nav').classList.remove('active');
    });
  });

  // Animation pour les cartes de fonctionnalités
  const observerOptions = {
    threshold: 0.1
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);

  document.querySelectorAll('.feature-card').forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = 'all 0.6s ease-out';
    observer.observe(card);
  });

  // Fermer le menu lors du clic en dehors
  document.addEventListener('click', (event) => {
    const nav = document.querySelector('nav');
    const menuToggle = document.querySelector('.menu-toggle');

    if (nav && menuToggle && !nav.contains(event.target) && !menuToggle.contains(event.target)) {
      nav.classList.remove('active');
      menuToggle.classList.remove('active');
    }
  });
  </script>
</body>
</html>

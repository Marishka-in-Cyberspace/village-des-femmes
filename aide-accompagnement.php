<?php
require_once 'config/config.php';
$page_active = 'aide-accompagnement';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Aide &amp; Accompagnement — Village des Femmes</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="aide-accompagnement.css" />
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <section class="hero-page">
    <span class="section-tag">Nous sommes là</span>
    <h1>Aide &amp; <span class="highlight">Accompagnement</span></h1>
    <p>
      Vous traversez une période difficile ? Notre équipe vous
      écoute avec bienveillance et confidentialité, sans jugement.
    </p>
    <a href="contact.php" class="btn btn-noir">Demander de l'aide</a>
  </section>

  <section class="services">
    <div class="container">
      <h2 class="text-center-section">Nos accompagnements</h2>
      <div class="services-grid">
        <div class="service-card">
          <div class="service-icone rose"><img src="images/icone-ecoute.png" alt="ecoute"></div>
          <h3>Écoute et soutien</h3>
          <p>
            Des écoutants disponibles pour vous accompagner lors de
            permanences. Un espace confidentiel pour parler librement.
          </p>
          <a href="contact.php" class="btn-service">Prendre rendez-vous →</a>
        </div>

        <div class="service-card">
          <div class="service-icone vert"><img src="images/icone-juridique.png" alt="juridique"></div>
          <h3>Accompagnement</h3>
          <p>Aide aux démarches administratives</p>
          <a href="contact.php" class="btn-service">Prendre rendez-vous →</a>
        </div>

        <div class="service-card">
          <div class="service-icone vert-clair"><img src="images/icone-psy.png" alt=""></div>
          <h3>Ateliers collectifs</h3>
          <p>
            Ateliers création, cuisine…
            Des moments de partage pour briser l'isolement et recréer du lien.
          </p>
          <a href="evenements.php" class="btn-service">Voir les ateliers →</a>
        </div>

        <div class="service-card">
          <div class="service-icone vert"><img src="images/icone-urgence.png" alt="orientation"></div>
          <h3>Orientation urgente</h3>
          <p>
            En situation d'urgence, nous vous orientons vers les bons
            interlocuteurs et structures d'hébergement ou de protection.
          </p>
          <a href="contact.php" class="btn-service">Nous contacter →</a>
        </div>
      </div>
    </div>
  </section>

  <section class="comment">
    <div class="container">
      <h2 class="text-center-section">Comment ça marche ?</h2>
      <div class="etapes">
        <div class="etape">
          <div class="etape-num">1</div>
          <h3>Vous nous contactez</h3>
          <p>Par téléphone, email ou formulaire — en toute confidentialité.</p>
        </div>
        <div class="etape-fleche">→</div>
        <div class="etape">
          <div class="etape-num">2</div>
          <h3>Nous échangeons</h3>
          <p>Un premier entretien pour comprendre votre situation et vos besoins.</p>
        </div>
        <div class="etape-fleche">→</div>
        <div class="etape">
          <div class="etape-num">3</div>
          <h3>On construit ensemble</h3>
          <p>Un accompagnement personnalisé, à votre rythme, selon vos besoins.</p>
        </div>
      </div>
      <div class="comment-cta">
        <a href="contact.php" class="btn btn-rose">Commencer maintenant</a>
      </div>
    </div>
  </section>

  <section class="urgences">
    <div class="container">
      <h2 class="text-center-section">En cas d'urgence</h2>
      <div class="urgences-grid">
        <div class="urgence-card">
          <span class="urgence-numero">3919</span>
          <span class="urgence-label">Violences Femmes Info<br><small>24h/24 · Gratuit · Anonyme</small></span>
        </div>
        <div class="urgence-card">
          <span class="urgence-numero">115</span>
          <span class="urgence-label">SAMU Social<br><small>Hébergement d'urgence</small></span>
        </div>
        <div class="urgence-card">
          <span class="urgence-numero">17</span>
          <span class="urgence-label">Police Secours<br><small>Danger immédiat</small></span>
        </div>
        <div class="urgence-card">
          <span class="urgence-numero">15</span>
          <span class="urgence-label">SAMU<br><small>Urgence médicale</small></span>
        </div>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>

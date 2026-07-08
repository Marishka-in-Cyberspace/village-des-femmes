<?php
require_once 'config/config.php';
$page_active = 'notre-mission';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Notre Mission — Village des Femmes</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="notre-mission.css" />
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <section class="hero-mission">
    <div class="hero-mission-text">
      <span class="section-tag">Qui sommes-nous</span>
      <h1>Notre <span class="highlight">mission</span></h1>
      <p>
        Fondée sur les valeurs de solidarité, de bienveillance et de respect,
        le Village des Femmes s'engage chaque jour auprès des femmes
        qui en ont le plus besoin.
      </p>
      <div class="hero-btns">
        <a href="aide-accompagnement.php" class="btn btn-noir">Demander de l'aide</a>
        <a href="nous-soutenir.php" class="btn btn-outline-rose">Nous soutenir</a>
      </div>
    </div>
    <div class="hero-mission-photo">
      <div class="mission-img-placeholder">
        <img src="images/mission-hero.png" alt="Notre équipe">
      </div>
    </div>
  </section>

  <section class="histoire">
    <div class="container histoire-inner">
      <div class="histoire-photo">
        <?php if (file_exists(__DIR__ . '/images/histoire.jpg')): ?>
          <img src="images/histoire.jpg" alt="Histoire de l'association" />
        <?php elseif (file_exists(__DIR__ . '/images/mission.JPG')): ?>
          <img src="images/mission.JPG" alt="Histoire de l'association" />
        <?php else: ?>
          <div class="histoire-img-placeholder">
            <span>Photo<br><small style="font-weight:400; text-transform:none; letter-spacing:0;">Ajoute images/histoire.jpg</small></span>
          </div>
        <?php endif; ?>
      </div>
      <div class="histoire-text">
        <span class="section-tag">Notre histoire</span>
        <h2>Née d'un besoin, portée par une communauté</h2>
        <p>
          Le Village des Femmes a été créé en réponse à un constat simple :
          trop de femmes se retrouvent seules face à des situations difficiles,
          sans savoir vers qui se tourner.
        </p>
        <p>
          Depuis notre création, nous avons accompagné des centaines de femmes
          dans leurs démarches, en les entourant d'une équipe bienveillante
          de professionnels et de bénévoles engagés.
        </p>
      </div>
    </div>
  </section>

  <section class="valeurs-section">
    <div class="container">
      <h2 class="text-center">Nos valeurs fondamentales</h2>
      <div class="valeurs-cards">
        <div class="valeur-card">
          <div class="valeur-rond rose"><img src="images/icone-bienveillance.png" alt="bienveillance"></div>
          <h3>Bienveillance</h3>
          <p>Chaque femme est accueillie sans jugement, dans un espace de confiance et de sécurité.</p>
        </div>
        <div class="valeur-card">
          <div class="valeur-rond vert"><img src="images/icone-solidarite.png" alt=""></div>
          <h3>Solidarité</h3>
          <p>Nous croyons à la force du collectif et à l'entraide entre femmes pour surmonter les épreuves.</p>
        </div>
        <div class="valeur-card">
          <div class="valeur-rond rose-clair"><img src="images/icone-respect.png" alt="respect"></div>
          <h3>Respect</h3>
          <p>Chaque parcours est unique. Nous respectons les choix et le rythme de chaque femme accompagnée.</p>
        </div>
        <div class="valeur-card">
          <div class="valeur-rond vert-clair"><img src="images/icone-engagement.png" alt="engagement"></div>
          <h3>Engagement</h3>
          <p>Nos bénévoles s'investissent avec cœur pour offrir un accompagnement de qualité.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="chiffres">
    <div class="container chiffres-grid">
      <div class="chiffre-item">
        <span class="chiffre-nombre">DES</span>
        <span class="chiffre-label">Femmes accompagnées</span>
      </div>
      <div class="chiffre-item">
        <span class="chiffre-nombre">11</span>
        <span class="chiffre-label">Bénévoles engagés</span>
      </div>
      <div class="chiffre-item">
        <span class="chiffre-nombre"><?= date('Y') - 2023 ?></span>
        <span class="chiffre-label">Années d'existence</span>
      </div>
      <div class="chiffre-item">
        <span class="chiffre-nombre">DES</span>
        <span class="chiffre-label">Ateliers et événements</span>
      </div>
    </div>
  </section>

  <section class="cta-section">
    <div class="container cta-inner">
      <h2>Vous souhaitez nous rejoindre ?</h2>
      <p>Que vous souhaitiez faire un don, devenir bénévole ou simplement en savoir plus, nous sommes là.</p>
      <div class="cta-btns">
        <a href="nous-soutenir.php" class="btn btn-rose">Nous soutenir</a>
        <a href="contact.php" class="btn btn-outline-vert">Nous contacter</a>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>

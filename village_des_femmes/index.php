<?php
require_once 'config/config.php';
$page_active = 'index';

// Récupère les 3 prochains événements à venir depuis la BDD
$pdo = getPDO();
$stmt = $pdo->prepare("
    SELECT * FROM evenements
    WHERE statut = 'a_venir' AND date_debut >= NOW()
    ORDER BY date_debut ASC
    LIMIT 3
");
$stmt->execute();
$evenements_a_venir = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Accueil — Village des Femmes</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="index.css" />
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <!-- ══ HERO ══ -->
  <section class="hero-accueil">
    <div class="hero-text">
      <span class="section-tag">Bienvenue</span>
      <h1>Accueillir,<br><span class="highlight">protéger,<br>accompagner</span></h1>
      <p class="hero-desc">
        Le Village des Femmes est une association qui accueille, écoute et accompagne
        les femmes en situation de vulnérabilité vers un avenir plus serein et libre.
      </p>
      <div class="hero-btns">
        <a href="aide-accompagnement.php" class="btn btn-noir">Demander de l'aide</a>
        <a href="notre-mission.php" class="btn btn-outline-vert">En savoir plus</a>
      </div>
    </div>
    <div class="hero-photo">
      <div class="hero-photo-placeholder">
        <img src="images/hero-accueil.jpeg" alt="Équipe Village des Femmes" />
      </div>
    </div>
  </section>

  <!-- ══ VIDÉO DE PRÉSENTATION ══ -->
  <section class="video-section">
    <div class="container video-inner">
      <span class="section-tag">Nous découvrir</span>
      <h2>Le Village des Femmes <span class="highlight">en vidéo</span></h2>
      <p class="video-desc">
        Découvrez en quelques minutes notre association, nos actions
        et les femmes qui font vivre le Village des Femmes au quotidien.
      </p>
      <div class="video-wrap">
        <iframe
          src="https://www.youtube.com/embed/bYhr8jq3HlU"
          title="Présentation du Village des Femmes"
          frameborder="0"
          referrerpolicy="strict-origin-when-cross-origin"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          allowfullscreen>
        </iframe>
      </div>
    </div>
  </section>

  <!-- ══ VALEURS ══ -->
  <section class="valeurs">
    <div class="container valeurs-grid">
      <div class="valeur-item valeur-rose">
        <div class="valeur-icone"><img src="images/icone-accueil.png" alt="accueillir"></div>
        <span class="valeur-label">Accueillir</span>
      </div>
      <div class="valeur-item valeur-pale">
        <div class="valeur-icone"><img src="images/icone-ecoute.png" alt="écouter"></div>
        <span class="valeur-label">Écouter</span>
      </div>
      <div class="valeur-item valeur-vert">
        <div class="valeur-icone"><img src="images/icone-lien.png" alt="créer du lien"></div>
        <span class="valeur-label">Créer du lien</span>
      </div>
    </div>
  </section>

  <!-- ══ SECTION MISSION ══ -->
  <section class="mission-section">
    <div class="mission-inner">
      <div class="mission-photo">
        <div class="mission-photo-placeholder">
          <img src="images/mission.JPG" alt="Notre mission">
        </div>
      </div>
      <div class="mission-text">
        <span class="section-tag">Notre mission</span>
        <h2>Un espace sûr pour chacune de nous</h2>
        <p>
          Nous croyons que chaque femme mérite d'être entendue et soutenue.
          Notre équipe de bénévoles s'engage à offrir un
          accompagnement personnalisé, confidentiel et bienveillant.
        </p>
        <a href="notre-mission.php" class="btn btn-vert">Nos Missions</a>
      </div>
    </div>
  </section>

  <!-- ══ BANDEAU CITATION ══ -->
  <section class="bandeau-vert">
    <div class="container bandeau-inner">
      <div class="bandeau-icone">
        <img src="images/icone-citation.png" alt="icone citation" />
      </div>
      <p class="bandeau-texte">
        "Ensemble, nous construisons un village où chaque femme trouve sa place et sa force."
      </p>
    </div>
  </section>

  <!-- ══ ÉVÉNEMENTS À VENIR (dynamique depuis la BDD) ══ -->
  <section class="evenements">
    <div class="container">
      <div class="evenements-header">
        <h2><span class="highlight">Événements à venir</span></h2>
        <a href="evenements.php" class="voir-tous">Tous les événements →</a>
      </div>
      <div class="evenements-grid">

        <?php if (empty($evenements_a_venir)): ?>
          <p>Aucun événement à venir pour le moment. Revenez bientôt !</p>
        <?php else: ?>
          <?php foreach ($evenements_a_venir as $evt): ?>
            <a href="evenements.php#evt<?= $evt['id'] ?>" class="event-card">
              <div class="event-photo-wrap">
                <?php if (!empty($evt['image_principale'])): ?>
                  <img src="<?= htmlspecialchars($evt['image_principale']) ?>" alt="<?= htmlspecialchars($evt['titre']) ?>">
                <?php else: ?>
                  <div class="event-photo-placeholder">Visuel à venir</div>
                <?php endif; ?>
              </div>
              <div class="event-info">
                <h3><?= htmlspecialchars($evt['titre']) ?></h3>
                <p class="event-date">
                  <?= htmlspecialchars(ucfirst(date('l j F Y à H\hi', strtotime($evt['date_debut'])))) ?>
                </p>
                <p class="event-lieu">📍 <?= htmlspecialchars($evt['lieu'] ?? 'Lieu à confirmer') ?></p>
                <p class="event-desc"><?= htmlspecialchars($evt['description_courte'] ?? '') ?></p>
              </div>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>

      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>

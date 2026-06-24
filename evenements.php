<?php
require_once 'config/config.php';
$page_active = 'evenements';

$pdo = getPDO();

$stmt = $pdo->prepare("
    SELECT * FROM evenements
    WHERE statut = 'a_venir' AND date_debut >= NOW()
    ORDER BY date_debut ASC
");
$stmt->execute();
$evenements_a_venir = $stmt->fetchAll();

$stmt = $pdo->prepare("
    SELECT * FROM evenements
    WHERE statut = 'passe'
    ORDER BY date_debut DESC
");
$stmt->execute();
$evenements_passes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Événements — Village des Femmes</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="evenements.css" />
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <!-- ══ HERO ══ -->
  <section class="hero-page">
    <span class="section-tag">Agenda</span>
    <h1><span class="highlight">Événements</span> à venir</h1>
    <p>
      Ateliers, permanences, rencontres et festivités — retrouvez tous nos événements
      et venez nous rejoindre !
    </p>
  </section>

  <!-- ══ LISTE ÉVÉNEMENTS À VENIR (dynamique) ══ -->
  <section class="liste-evenements">
    <div class="container">

      <h2 class="sous-titre-section">Événements à venir</h2>

      <?php if (empty($evenements_a_venir)): ?>
        <p>Aucun événement à venir pour le moment. Revenez bientôt !</p>
      <?php else: ?>
        <?php foreach ($evenements_a_venir as $evt): ?>
          <article class="evt-detail" id="evt<?= $evt['id'] ?>">
            <div class="evt-photo">
              <?php if (!empty($evt['image_principale'])): ?>
                <img src="<?= htmlspecialchars($evt['image_principale']) ?>" alt="<?= htmlspecialchars($evt['titre']) ?>">
              <?php else: ?>
                <div class="evt-photo-placeholder"><span>Visuel à venir</span></div>
              <?php endif; ?>
            </div>
            <div class="evt-contenu">
              <div class="evt-meta">
                <span class="evt-date-badge"><?= htmlspecialchars(date('j M.', strtotime($evt['date_debut']))) ?></span>
                <?php if (!empty($evt['type_evenement'])): ?>
                  <span class="evt-type"><?= htmlspecialchars($evt['type_evenement']) ?></span>
                <?php endif; ?>
              </div>
              <h2><?= htmlspecialchars($evt['titre']) ?></h2>
              <div class="evt-infos">
                <span>📅 <?= htmlspecialchars(ucfirst(date('l j F Y à H\hi', strtotime($evt['date_debut'])))) ?></span>
                <span>📍 <?= htmlspecialchars($evt['lieu'] ?? 'Lieu à confirmer') ?></span>
                <?php if (!empty($evt['prix_public'])): ?>
                  <span>👤 <?= htmlspecialchars($evt['prix_public']) ?></span>
                <?php endif; ?>
              </div>
              <p><?= htmlspecialchars($evt['description_courte'] ?? '') ?></p>
              <a href="contact.php" class="btn btn-rose">S'inscrire</a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>
  </section>

  <!-- ══ ÉVÉNEMENTS PASSÉS (dynamique) ══ -->
  <section class="evenements-passes" id="evenements-passes">
    <div class="container">
      <h2 class="sous-titre-section">Événements passés</h2>
      <p class="passes-intro">Retour sur nos derniers événements et ce qu'ils ont apporté à notre communauté.</p>

      <div class="passes-grid">
        <?php foreach ($evenements_passes as $evt): ?>
          <a href="evenement-passe.php?slug=<?= urlencode($evt['slug']) ?>" class="passe-card">
            <div class="passe-photo-wrap">
              <?php if (!empty($evt['image_principale'])): ?>
                <img src="<?= htmlspecialchars($evt['image_principale']) ?>" alt="<?= htmlspecialchars($evt['titre']) ?>">
              <?php else: ?>
                <div class="passe-photo-placeholder"><span>Visuel à venir</span></div>
              <?php endif; ?>
              <span class="passe-badge">Terminé</span>
            </div>
            <div class="passe-info">
              <span class="passe-date"><?= htmlspecialchars(ucfirst(date('j F Y', strtotime($evt['date_debut'])))) ?></span>
              <h3><?= htmlspecialchars($evt['titre']) ?></h3>
              <p><?= htmlspecialchars($evt['description_courte'] ?? '') ?></p>
              <span class="passe-lien">Voir le compte-rendu →</span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ══ CTA ══ -->
  <section class="cta-evenements">
    <div class="container cta-evt-inner">
      <h2>Vous souhaitez proposer un atelier ?</h2>
      <p>Vous êtes professionnel(le), bénévole ou simplement passionné(e) ?
         Contactez-nous pour co-organiser un événement !</p>
      <a href="contact.php" class="btn btn-noir">Nous contacter</a>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>

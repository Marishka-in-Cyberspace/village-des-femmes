<?php
require_once 'config/config.php';
$page_active = 'evenements';

$pdo = getPDO();

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: evenements.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM evenements WHERE slug = :slug AND statut = 'passe' LIMIT 1");
$stmt->execute(['slug' => $slug]);
$evt = $stmt->fetch();

if (!$evt) {
    header('Location: evenements.php');
    exit;
}

// Galerie photo associée
$stmt = $pdo->prepare("SELECT * FROM evenements_galerie WHERE evenement_id = :id ORDER BY ordre_affichage ASC");
$stmt->execute(['id' => $evt['id']]);
$galerie = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($evt['titre']) ?> — Compte-rendu — Village des Femmes</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="evenement-passe-template.css" />
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <!-- ══ HERO COMPTE-RENDU ══ -->
  <section class="cr-hero">
    <div class="container">
      <a href="evenements.php#evenements-passes" class="cr-retour">← Retour aux événements</a>
      <div class="cr-meta">
        <span class="cr-badge-termine">Terminé</span>
        <span class="cr-date">📅 <?= htmlspecialchars(ucfirst(date('j F Y', strtotime($evt['date_debut'])))) ?></span>
      </div>
      <h1><?= htmlspecialchars($evt['titre']) ?></h1>
    </div>
  </section>

  <!-- ══ CONTENU ══ -->
  <section class="cr-contenu">
    <div class="container cr-inner">

      <div class="cr-photo-principale">
        <?php if (!empty($evt['image_principale'])): ?>
          <img src="<?= htmlspecialchars($evt['image_principale']) ?>" alt="<?= htmlspecialchars($evt['titre']) ?>">
        <?php else: ?>
          <span>Photo principale</span>
        <?php endif; ?>
      </div>

      <div class="cr-texte">
        <?php
        // On affiche la description longue, paragraphe par paragraphe (séparés par des retours à la ligne)
        $paragraphes = array_filter(explode("\n", $evt['description_longue'] ?? ''));
        foreach ($paragraphes as $p):
        ?>
          <p><?= htmlspecialchars(trim($p)) ?></p>
        <?php endforeach; ?>
      </div>

      <?php if (!empty($evt['chiffre1_nombre']) || !empty($evt['chiffre2_nombre']) || !empty($evt['chiffre3_nombre'])): ?>
      <div class="cr-chiffres">
        <?php if (!empty($evt['chiffre1_nombre'])): ?>
        <div class="cr-chiffre">
          <span class="cr-chiffre-nombre"><?= htmlspecialchars($evt['chiffre1_nombre']) ?></span>
          <span class="cr-chiffre-label"><?= htmlspecialchars($evt['chiffre1_label']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($evt['chiffre2_nombre'])): ?>
        <div class="cr-chiffre">
          <span class="cr-chiffre-nombre"><?= htmlspecialchars($evt['chiffre2_nombre']) ?></span>
          <span class="cr-chiffre-label"><?= htmlspecialchars($evt['chiffre2_label']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($evt['chiffre3_nombre'])): ?>
        <div class="cr-chiffre">
          <span class="cr-chiffre-nombre"><?= htmlspecialchars($evt['chiffre3_nombre']) ?></span>
          <span class="cr-chiffre-label"><?= htmlspecialchars($evt['chiffre3_label']) ?></span>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($galerie)): ?>
      <div class="cr-galerie">
        <h2>Quelques photos du jour</h2>
        <div class="cr-galerie-grid">
          <?php foreach ($galerie as $photo): ?>
            <div class="cr-galerie-item">
              <img src="<?= htmlspecialchars($photo['image_url']) ?>" alt="">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <div class="cr-cta">
        <h3>Envie de participer à notre prochain événement ?</h3>
        <p>De nouveaux événements sont organisés régulièrement.</p>
        <div class="cr-cta-btns">
          <a href="evenements.php" class="btn btn-rose">Voir les prochains événements</a>
          <a href="contact.php" class="btn btn-outline-vert">Nous contacter</a>
        </div>
      </div>

    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>

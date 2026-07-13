<?php
require_once 'config/config.php';
$page_active = 'evenements';

$pdo = getPDO();

$stmt = $pdo->prepare("
    SELECT e.*,
        (SELECT COUNT(*) FROM inscriptions_evenements ie
         WHERE ie.evenement_id = e.id AND ie.statut = 'confirme') AS nb_inscrits
    FROM evenements e
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

// Si l'utilisateur est connecté, on récupère ses inscriptions pour ne pas afficher "S'inscrire" sur un event déjà rejoint
$mes_inscriptions = [];
if (estConnecte()) {
    $stmt = $pdo->prepare("SELECT evenement_id, statut FROM inscriptions_evenements WHERE utilisateur_id = :uid");
    $stmt->execute(['uid' => $_SESSION['utilisateur_id']]);
    foreach ($stmt->fetchAll() as $row) {
        $mes_inscriptions[$row['evenement_id']] = $row['statut'];
    }
}

// Messages flash
$statut_flash = $_GET['statut'] ?? '';
$erreur_flash = $_GET['erreur'] ?? '';
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

      <?php if ($statut_flash === 'inscrit'): ?>
        <p class="admin-msg-success" style="margin-bottom:24px;">🎉 Ton inscription est confirmée !</p>
      <?php elseif ($statut_flash === 'liste_attente'): ?>
        <p class="admin-msg-success" style="margin-bottom:24px;">⏳ L'événement est complet — tu as été ajouté(e) sur liste d'attente.</p>
      <?php elseif ($statut_flash === 'deja_inscrit'): ?>
        <p class="admin-msg-error" style="margin-bottom:24px;">Tu es déjà inscrit(e) à cet événement.</p>
      <?php elseif ($erreur_flash): ?>
        <p class="admin-msg-error" style="margin-bottom:24px;">Une erreur est survenue. Merci de réessayer.</p>
      <?php endif; ?>

      <?php if (empty($evenements_a_venir)): ?>
        <p>Aucun événement à venir pour le moment. Revenez bientôt !</p>
      <?php else: ?>
        <?php foreach ($evenements_a_venir as $evt): ?>
          <?php
            $est_complet = $evt['places_max'] && $evt['nb_inscrits'] >= $evt['places_max'];
            $mon_statut  = $mes_inscriptions[$evt['id']] ?? null;
          ?>
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
                <?php if ($evt['places_max']): ?>
                  <span class="evt-type" style="border-color:#888; color:#888;">
                    <?= $evt['nb_inscrits'] ?>/<?= $evt['places_max'] ?> places
                  </span>
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

              <!-- ── Bouton / formulaire d'inscription ── -->
              <?php if ($mon_statut === 'confirme'): ?>
                <p class="inscription-ok">✅ Tu es inscrit(e) à cet événement</p>

              <?php elseif ($mon_statut === 'liste_attente'): ?>
                <p class="inscription-ok" style="background:#fff8e1; color:#b08000; border-color:#ffe082;">
                  ⏳ Tu es sur liste d'attente
                </p>

              <?php elseif (estConnecte()): ?>
                <!-- Connecté → 1 clic -->
                <form action="inscrire-evenement.php" method="POST">
                  <input type="hidden" name="evenement_id" value="<?= $evt['id'] ?>" />
                  <button type="submit" class="btn <?= $est_complet ? 'btn-outline-vert' : 'btn-rose' ?>">
                    <?= $est_complet ? "Rejoindre la liste d'attente" : "S'inscrire" ?>
                  </button>
                </form>

              <?php else: ?>
                <!-- Non connecté → mini formulaire dépliable -->
                <div class="inscription-guest">
                  <p class="inscription-guest-intro">
                    <a href="connexion.php" class="btn btn-rose btn-sm">Se connecter pour s'inscrire</a>
                    <span style="font-size:0.8rem; color:#888; margin:0 8px;">ou</span>
                  </p>
                  <details class="inscription-guest-form">
                    <summary><?= $est_complet ? "Rejoindre la liste d'attente sans compte" : "S'inscrire sans compte" ?></summary>
                    <form action="inscrire-evenement.php" method="POST" style="margin-top:12px;">
                      <input type="hidden" name="evenement_id" value="<?= $evt['id'] ?>" />
                      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
                        <input type="text" name="nom_invite" placeholder="Votre nom *" required
                               style="padding:9px 14px; border:2px solid #e0e0e0; border-radius:8px; font-size:0.88rem;" />
                        <input type="email" name="email_invite" placeholder="Votre email *" required
                               style="padding:9px 14px; border:2px solid #e0e0e0; border-radius:8px; font-size:0.88rem;" />
                      </div>
                      <button type="submit" class="btn <?= $est_complet ? 'btn-outline-vert' : 'btn-rose' ?>" style="font-size:0.8rem; padding:9px 20px;">
                        <?= $est_complet ? "Rejoindre la liste d'attente" : "Confirmer mon inscription" ?>
                      </button>
                    </form>
                  </details>
                </div>
              <?php endif; ?>

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

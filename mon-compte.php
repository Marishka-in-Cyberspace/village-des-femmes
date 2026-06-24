<?php
require_once 'config/config.php';
exigerConnexion();
$page_active = 'mon-compte';

// Les admins ont leur propre espace dédié
if ($_SESSION['role'] === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

$pdo = getPDO();
$utilisateur = utilisateurConnecte();

if (!$utilisateur) {
    header('Location: deconnexion.php');
    exit;
}

// Historique des dons de la personne
$stmt = $pdo->prepare("SELECT * FROM dons WHERE utilisateur_id = :id ORDER BY date_don DESC");
$stmt->execute(['id' => $utilisateur['id']]);
$mes_dons = $stmt->fetchAll();
$total_dons = array_sum(array_map(fn($d) => $d['statut'] === 'valide' ? (float) $d['montant'] : 0, $mes_dons));

// Profil bénévole le cas échéant
$profil_benevole = null;
if ($utilisateur['role'] === 'benevole') {
    $stmt = $pdo->prepare("SELECT * FROM profils_benevoles WHERE utilisateur_id = :id LIMIT 1");
    $stmt->execute(['id' => $utilisateur['id']]);
    $profil_benevole = $stmt->fetch();
}

// Mise à jour du profil bénévole (disponibilité / compétences)
$msg_succes = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'maj_profil_benevole') {
    $disponibilite = nettoyer($_POST['disponibilite'] ?? '');
    $competences   = nettoyer($_POST['competences'] ?? '');
    $dispos_valides = ['quelques_heures_mois', 'un_jour_semaine', 'plusieurs_jours_semaine', 'ponctuel_evenements'];

    if (in_array($disponibilite, $dispos_valides)) {
        if ($profil_benevole) {
            $pdo->prepare("UPDATE profils_benevoles SET disponibilite = :d, competences = :c WHERE utilisateur_id = :uid")
                ->execute(['d' => $disponibilite, 'c' => $competences, 'uid' => $utilisateur['id']]);
        } else {
            $pdo->prepare("INSERT INTO profils_benevoles (utilisateur_id, disponibilite, competences) VALUES (:uid, :d, :c)")
                ->execute(['uid' => $utilisateur['id'], 'd' => $disponibilite, 'c' => $competences]);
        }
        $msg_succes = "Profil mis à jour !";
        $stmt = $pdo->prepare("SELECT * FROM profils_benevoles WHERE utilisateur_id = :id LIMIT 1");
        $stmt->execute(['id' => $utilisateur['id']]);
        $profil_benevole = $stmt->fetch();
    }
}

$libelles_dispo = [
    'quelques_heures_mois'    => 'Quelques heures par mois',
    'un_jour_semaine'         => 'Un jour par semaine',
    'plusieurs_jours_semaine' => 'Plusieurs jours par semaine',
    'ponctuel_evenements'     => 'Ponctuellement (événements)',
];
$libelles_statut_candidature = [
    'en_attente' => ['En attente de validation', 'badge-gris'],
    'accepte'    => ['Candidature acceptée ✓', 'badge-vert'],
    'refuse'     => ['Candidature non retenue', 'badge-rouge'],
    'inactif'    => ['Inactif', 'badge-gris'],
];

$bienvenue = $_GET['bienvenue'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mon compte — Village des Femmes</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="contact.css" />
  <link rel="stylesheet" href="compte.css" />
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <section class="hero-page">
    <span class="section-tag">Espace personnel</span>
    <h1>Bonjour <span class="highlight"><?= htmlspecialchars($utilisateur['prenom']) ?></span></h1>
    <p>
      <?php if ($utilisateur['role'] === 'benevole'): ?>
        Merci pour ton engagement auprès du Village des Femmes.
      <?php else: ?>
        Merci pour ton soutien auprès du Village des Femmes.
      <?php endif; ?>
    </p>
  </section>

  <section class="contact-section">
    <div class="container" style="max-width:760px;">

      <?php if ($bienvenue === 'benevole'): ?>
        <p class="admin-msg-success" style="margin-bottom:24px;">
          🎉 Bienvenue ! Ta candidature bénévole a été enregistrée, elle est en attente de validation par notre équipe.
        </p>
      <?php elseif ($bienvenue === 'donateur'): ?>
        <p class="admin-msg-success" style="margin-bottom:24px;">
          🎉 Bienvenue ! Ton compte a bien été créé.
        </p>
      <?php endif; ?>

      <?php if ($msg_succes): ?>
        <p class="admin-msg-success" style="margin-bottom:24px;">✅ <?= htmlspecialchars($msg_succes) ?></p>
      <?php endif; ?>

      <!-- ── Infos du compte ── -->
      <div class="compte-card">
        <h2>Mes informations</h2>
        <div class="compte-infos-grid">
          <div><span class="compte-label">Nom</span><span><?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></span></div>
          <div><span class="compte-label">Email</span><span><?= htmlspecialchars($utilisateur['email']) ?></span></div>
          <div><span class="compte-label">Téléphone</span><span><?= htmlspecialchars($utilisateur['telephone'] ?: '—') ?></span></div>
          <div><span class="compte-label">Statut</span><span><?= $utilisateur['role'] === 'benevole' ? 'Bénévole' : 'Donateur / Donatrice' ?></span></div>
        </div>
      </div>

      <!-- ── Profil bénévole ── -->
      <?php if ($utilisateur['role'] === 'benevole'): ?>
      <div class="compte-card">
        <h2>Mon profil bénévole</h2>

        <?php if ($profil_benevole): ?>
          <p style="margin-bottom:18px;">
            Statut de ma candidature :
            <span class="badge <?= $libelles_statut_candidature[$profil_benevole['statut_candidature']][1] ?? 'badge-gris' ?>">
              <?= htmlspecialchars($libelles_statut_candidature[$profil_benevole['statut_candidature']][0] ?? $profil_benevole['statut_candidature']) ?>
            </span>
          </p>
        <?php endif; ?>

        <form method="POST">
          <input type="hidden" name="action" value="maj_profil_benevole" />
          <div class="form-group">
            <label for="disponibilite">Mes disponibilités</label>
            <select id="disponibilite" name="disponibilite">
              <?php foreach ($libelles_dispo as $valeur => $libelle): ?>
                <option value="<?= $valeur ?>" <?= ($profil_benevole['disponibilite'] ?? '') === $valeur ? 'selected' : '' ?>>
                  <?= htmlspecialchars($libelle) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="competences">Mes compétences / centres d'intérêt</label>
            <textarea id="competences" name="competences" rows="4"><?= htmlspecialchars($profil_benevole['competences'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn btn-rose">Mettre à jour mon profil</button>
        </form>
      </div>
      <?php endif; ?>

      <!-- ── Historique des dons ── -->
      <div class="compte-card">
        <h2>Mes dons</h2>
        <p style="margin-bottom:18px; font-size:0.9rem; color:var(--texte-clair);">
          Total donné (validé) : <strong style="color:var(--rose-vif);"><?= number_format($total_dons, 2, ',', ' ') ?> €</strong>
        </p>

        <?php if (empty($mes_dons)): ?>
          <p style="font-size:0.9rem; color:var(--texte-clair);">
            Tu n'as pas encore fait de don. <a href="nous-soutenir.php#faire-un-don" style="color:var(--rose-vif); font-weight:700;">Faire un don →</a>
          </p>
        <?php else: ?>
          <table class="admin-table" style="width:100%;">
            <thead><tr><th>Date</th><th>Montant</th><th>Statut</th></tr></thead>
            <tbody>
              <?php foreach ($mes_dons as $don): ?>
                <tr>
                  <td><?= htmlspecialchars(date('d/m/Y', strtotime($don['date_don']))) ?></td>
                  <td><?= number_format($don['montant'], 2, ',', ' ') ?> €</td>
                  <td>
                    <?php
                      $badge_map = ['valide' => 'badge-vert', 'en_attente' => 'badge-gris', 'echoue' => 'badge-rouge', 'rembourse' => 'badge-rose'];
                      $classe = $badge_map[$don['statut']] ?? 'badge-gris';
                    ?>
                    <span class="badge <?= $classe ?>"><?= htmlspecialchars($don['statut']) ?></span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>

      <div style="text-align:center; margin-top:8px;">
        <a href="deconnexion.php" class="btn btn-outline-vert">Se déconnecter</a>
      </div>

    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>

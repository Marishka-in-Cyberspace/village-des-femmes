<?php
require_once '../config/config.php';
exigerAdmin();
$admin_page_active = 'dashboard';

$pdo = getPDO();

$nb_evenements_a_venir = $pdo->query("SELECT COUNT(*) FROM evenements WHERE statut = 'a_venir' AND date_debut >= NOW()")->fetchColumn();
$nb_messages_non_traites = $pdo->query("SELECT COUNT(*) FROM messages_contact WHERE statut = 'non_traite'")->fetchColumn();
$total_dons_valides = $pdo->query("SELECT COALESCE(SUM(montant),0) FROM dons WHERE statut = 'valide'")->fetchColumn();
$nb_benevoles_en_attente = $pdo->query("SELECT COUNT(*) FROM profils_benevoles WHERE statut_candidature = 'en_attente'")->fetchColumn();

// Derniers messages
$derniers_messages = $pdo->query("SELECT * FROM messages_contact ORDER BY date_envoi DESC LIMIT 5")->fetchAll();

// Derniers dons
$derniers_dons = $pdo->query("
    SELECT d.*, u.prenom, u.nom
    FROM dons d
    LEFT JOIN utilisateurs u ON u.id = d.utilisateur_id
    ORDER BY d.date_don DESC LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tableau de bord — Admin Village des Femmes</title>
  <link rel="stylesheet" href="../styles.css" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body class="admin-body">

  <?php include 'includes/admin-header.php'; ?>

  <div class="admin-content">
    <h1>Tableau de bord</h1>
    <p class="admin-sous-titre">Vue d'ensemble de l'activité du site.</p>

    <div class="admin-stats">
      <div class="admin-stat-card">
        <span class="admin-stat-nombre"><?= $nb_evenements_a_venir ?></span>
        <span class="admin-stat-label">Événements à venir</span>
      </div>
      <div class="admin-stat-card">
        <span class="admin-stat-nombre"><?= $nb_messages_non_traites ?></span>
        <span class="admin-stat-label">Messages non traités</span>
      </div>
      <div class="admin-stat-card">
        <span class="admin-stat-nombre"><?= number_format($total_dons_valides, 0, ',', ' ') ?> €</span>
        <span class="admin-stat-label">Total des dons validés</span>
      </div>
      <div class="admin-stat-card">
        <span class="admin-stat-nombre"><?= $nb_benevoles_en_attente ?></span>
        <span class="admin-stat-label">Candidatures bénévoles en attente</span>
      </div>
    </div>

    <h2 style="margin-bottom:14px; font-size:1.2rem;">Derniers messages de contact</h2>
    <div class="admin-table-wrap" style="margin-bottom:36px;">
      <table class="admin-table">
        <thead>
          <tr><th>Date</th><th>Nom</th><th>Sujet</th><th>Statut</th></tr>
        </thead>
        <tbody>
          <?php if (empty($derniers_messages)): ?>
            <tr><td colspan="4">Aucun message pour le moment.</td></tr>
          <?php endif; ?>
          <?php foreach ($derniers_messages as $msg): ?>
            <tr>
              <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($msg['date_envoi']))) ?></td>
              <td><?= htmlspecialchars($msg['prenom'] . ' ' . $msg['nom']) ?> <?= $msg['urgent'] ? '🆘' : '' ?></td>
              <td><?= htmlspecialchars($msg['sujet']) ?></td>
              <td>
                <?php if ($msg['statut'] === 'non_traite'): ?>
                  <span class="badge badge-rouge">Non traité</span>
                <?php elseif ($msg['statut'] === 'en_cours'): ?>
                  <span class="badge badge-gris">En cours</span>
                <?php else: ?>
                  <span class="badge badge-vert">Traité</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <h2 style="margin-bottom:14px; font-size:1.2rem;">Derniers dons</h2>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr><th>Date</th><th>Donateur</th><th>Montant</th><th>Méthode</th><th>Statut</th></tr>
        </thead>
        <tbody>
          <?php if (empty($derniers_dons)): ?>
            <tr><td colspan="5">Aucun don pour le moment.</td></tr>
          <?php endif; ?>
          <?php foreach ($derniers_dons as $don): ?>
            <tr>
              <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($don['date_don']))) ?></td>
              <td><?= htmlspecialchars($don['prenom'] ? $don['prenom'] . ' ' . $don['nom'] : ($don['nom_donateur'] ?: 'Anonyme')) ?></td>
              <td><?= number_format($don['montant'], 2, ',', ' ') ?> €</td>
              <td><?= htmlspecialchars($don['methode_paiement']) ?></td>
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
    </div>

  </div>

</body>
</html>

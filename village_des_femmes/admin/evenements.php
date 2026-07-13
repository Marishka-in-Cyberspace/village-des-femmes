<?php
require_once '../config/config.php';
exigerAdmin();
$admin_page_active = 'evenements';

$pdo = getPDO();

// Suppression d'un événement
if (isset($_GET['supprimer'])) {
    $id = (int) $_GET['supprimer'];
    $pdo->prepare("DELETE FROM evenements WHERE id = :id")->execute(['id' => $id]);
    header('Location: evenements.php?statut=supprime');
    exit;
}

$evenements = $pdo->query("SELECT * FROM evenements ORDER BY date_debut DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Événements — Admin Village des Femmes</title>
  <link rel="stylesheet" href="../styles.css" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body class="admin-body">

  <?php include 'includes/admin-header.php'; ?>

  <div class="admin-content">
    <h1>Événements</h1>
    <p class="admin-sous-titre">Gérez les événements à venir et les comptes-rendus des événements passés.</p>

    <?php if (isset($_GET['statut']) && $_GET['statut'] === 'supprime'): ?>
      <p class="admin-msg-success">✅ Événement supprimé.</p>
    <?php elseif (isset($_GET['statut']) && $_GET['statut'] === 'enregistre'): ?>
      <p class="admin-msg-success">✅ Événement enregistré.</p>
    <?php endif; ?>

    <div class="admin-page-actions">
      <a href="evenement-form.php" class="btn btn-rose">+ Ajouter un événement</a>
    </div>

    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr><th>Date</th><th>Titre</th><th>Type</th><th>Statut</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php if (empty($evenements)): ?>
            <tr><td colspan="5">Aucun événement enregistré.</td></tr>
          <?php endif; ?>
          <?php foreach ($evenements as $evt): ?>
            <tr>
              <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($evt['date_debut']))) ?></td>
              <td><?= htmlspecialchars($evt['titre']) ?></td>
              <td><?= htmlspecialchars($evt['type_evenement'] ?? '—') ?></td>
              <td>
                <?php
                  $badge_map = ['a_venir' => 'badge-vert', 'passe' => 'badge-gris', 'en_cours' => 'badge-rose', 'annule' => 'badge-rouge'];
                  $classe = $badge_map[$evt['statut']] ?? 'badge-gris';
                ?>
                <span class="badge <?= $classe ?>"><?= htmlspecialchars($evt['statut']) ?></span>
              </td>
              <td class="admin-actions">
                <a href="evenement-form.php?id=<?= $evt['id'] ?>" class="btn-small btn-edit">Modifier</a>
                <a href="evenements.php?supprimer=<?= $evt['id'] ?>"
                   class="btn-small btn-delete"
                   onclick="return confirm('Supprimer définitivement cet événement ?');">Supprimer</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>

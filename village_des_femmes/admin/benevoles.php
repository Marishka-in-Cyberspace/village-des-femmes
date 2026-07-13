<?php
require_once '../config/config.php';
exigerAdmin();
$admin_page_active = 'benevoles';

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['profil_id'])) {
    $profil_id = (int) $_POST['profil_id'];
    $nouveau_statut = $_POST['nouveau_statut'] ?? '';
    if (in_array($nouveau_statut, ['en_attente', 'accepte', 'refuse', 'inactif'])) {
        $pdo->prepare("UPDATE profils_benevoles SET statut_candidature = :statut WHERE id = :id")
            ->execute(['statut' => $nouveau_statut, 'id' => $profil_id]);
    }
    header('Location: benevoles.php');
    exit;
}

$benevoles = $pdo->query("
    SELECT p.*, u.prenom, u.nom, u.email, u.telephone
    FROM profils_benevoles p
    JOIN utilisateurs u ON u.id = p.utilisateur_id
    ORDER BY p.date_candidature DESC
")->fetchAll();

$libelles_dispo = [
    'quelques_heures_mois'    => 'Quelques heures / mois',
    'un_jour_semaine'         => 'Un jour / semaine',
    'plusieurs_jours_semaine' => 'Plusieurs jours / semaine',
    'ponctuel_evenements'     => 'Ponctuel (événements)',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bénévoles — Admin Village des Femmes</title>
  <link rel="stylesheet" href="../styles.css" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body class="admin-body">

  <?php include 'includes/admin-header.php'; ?>

  <div class="admin-content">
    <h1>Candidatures bénévoles</h1>
    <p class="admin-sous-titre">Toutes les personnes ayant proposé leur aide via le site.</p>

    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr><th>Date</th><th>Nom</th><th>Contact</th><th>Disponibilité</th><th>Compétences</th><th>Statut</th></tr>
        </thead>
        <tbody>
          <?php if (empty($benevoles)): ?>
            <tr><td colspan="6">Aucune candidature pour le moment.</td></tr>
          <?php endif; ?>
          <?php foreach ($benevoles as $b): ?>
            <tr>
              <td><?= htmlspecialchars(date('d/m/Y', strtotime($b['date_candidature']))) ?></td>
              <td><?= htmlspecialchars($b['prenom'] . ' ' . $b['nom']) ?></td>
              <td>
                <a href="mailto:<?= htmlspecialchars($b['email']) ?>"><?= htmlspecialchars($b['email']) ?></a>
                <?php if ($b['telephone']): ?><br><small><?= htmlspecialchars($b['telephone']) ?></small><?php endif; ?>
              </td>
              <td><?= htmlspecialchars($libelles_dispo[$b['disponibilite']] ?? $b['disponibilite']) ?></td>
              <td style="max-width:240px; white-space:normal;"><?= nl2br(htmlspecialchars(mb_strimwidth($b['competences'] ?? '', 0, 160, '…'))) ?></td>
              <td>
                <form method="POST">
                  <input type="hidden" name="profil_id" value="<?= $b['id'] ?>" />
                  <select name="nouveau_statut" onchange="this.form.submit()" style="font-size:0.78rem; padding:4px 8px; border-radius:6px; border:1px solid #ddd;">
                    <option value="en_attente" <?= $b['statut_candidature'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                    <option value="accepte" <?= $b['statut_candidature'] === 'accepte' ? 'selected' : '' ?>>Accepté</option>
                    <option value="refuse" <?= $b['statut_candidature'] === 'refuse' ? 'selected' : '' ?>>Refusé</option>
                    <option value="inactif" <?= $b['statut_candidature'] === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                  </select>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>

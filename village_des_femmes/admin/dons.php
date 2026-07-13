<?php
require_once '../config/config.php';
exigerAdmin();
$admin_page_active = 'dons';

$pdo = getPDO();

// Mise à jour manuelle du statut d'un don (ex: confirmer un virement reçu)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['don_id'])) {
    $don_id = (int) $_POST['don_id'];
    $nouveau_statut = $_POST['nouveau_statut'] ?? '';
    if (in_array($nouveau_statut, ['en_attente', 'valide', 'echoue', 'rembourse'])) {
        $pdo->prepare("UPDATE dons SET statut = :statut WHERE id = :id")
            ->execute(['statut' => $nouveau_statut, 'id' => $don_id]);
    }
    header('Location: dons.php');
    exit;
}

// Marquer le reçu fiscal comme envoyé
if (isset($_GET['recu_envoye'])) {
    $pdo->prepare("UPDATE dons SET recu_fiscal_envoye = 1 WHERE id = :id")
        ->execute(['id' => (int) $_GET['recu_envoye']]);
    header('Location: dons.php');
    exit;
}

$dons = $pdo->query("
    SELECT d.*, u.prenom, u.nom, u.email AS email_compte
    FROM dons d
    LEFT JOIN utilisateurs u ON u.id = d.utilisateur_id
    ORDER BY d.date_don DESC
")->fetchAll();

$total_valide = $pdo->query("SELECT COALESCE(SUM(montant),0) FROM dons WHERE statut = 'valide'")->fetchColumn();
$total_attente = $pdo->query("SELECT COALESCE(SUM(montant),0) FROM dons WHERE statut = 'en_attente'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dons — Admin Village des Femmes</title>
  <link rel="stylesheet" href="../styles.css" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body class="admin-body">

  <?php include 'includes/admin-header.php'; ?>

  <div class="admin-content">
    <h1>Dons</h1>
    <p class="admin-sous-titre">Suivi de tous les dons reçus, validés ou en attente.</p>

    <div class="admin-stats" style="grid-template-columns: repeat(2, 1fr); margin-bottom:28px;">
      <div class="admin-stat-card">
        <span class="admin-stat-nombre"><?= number_format($total_valide, 2, ',', ' ') ?> €</span>
        <span class="admin-stat-label">Total validé</span>
      </div>
      <div class="admin-stat-card">
        <span class="admin-stat-nombre"><?= number_format($total_attente, 2, ',', ' ') ?> €</span>
        <span class="admin-stat-label">En attente de confirmation</span>
      </div>
    </div>

    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr><th>Date</th><th>Donateur</th><th>Montant</th><th>Méthode</th><th>Statut</th><th>Reçu fiscal</th></tr>
        </thead>
        <tbody>
          <?php if (empty($dons)): ?>
            <tr><td colspan="6">Aucun don enregistré.</td></tr>
          <?php endif; ?>
          <?php foreach ($dons as $don): ?>
            <tr>
              <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($don['date_don']))) ?></td>
              <td>
                <?php if ($don['prenom']): ?>
                  <?= htmlspecialchars($don['prenom'] . ' ' . $don['nom']) ?><br><small><?= htmlspecialchars($don['email_compte']) ?></small>
                <?php else: ?>
                  <?= htmlspecialchars($don['nom_donateur'] ?: 'Anonyme') ?><br><small><?= htmlspecialchars($don['email_donateur'] ?? '') ?></small>
                <?php endif; ?>
              </td>
              <td><strong><?= number_format($don['montant'], 2, ',', ' ') ?> €</strong></td>
              <td><?= htmlspecialchars($don['methode_paiement']) ?></td>
              <td>
                <form method="POST" style="display:inline-block;">
                  <input type="hidden" name="don_id" value="<?= $don['id'] ?>" />
                  <select name="nouveau_statut" onchange="this.form.submit()" style="font-size:0.78rem; padding:4px 8px; border-radius:6px; border:1px solid #ddd;">
                    <option value="en_attente" <?= $don['statut'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                    <option value="valide" <?= $don['statut'] === 'valide' ? 'selected' : '' ?>>Validé</option>
                    <option value="echoue" <?= $don['statut'] === 'echoue' ? 'selected' : '' ?>>Échoué</option>
                    <option value="rembourse" <?= $don['statut'] === 'rembourse' ? 'selected' : '' ?>>Remboursé</option>
                  </select>
                </form>
              </td>
              <td>
                <?php if ($don['recu_fiscal_envoye']): ?>
                  <span class="badge badge-vert">Envoyé</span>
                <?php else: ?>
                  <a href="dons.php?recu_envoye=<?= $don['id'] ?>" class="btn-small btn-valider">Marquer envoyé</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>

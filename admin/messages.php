<?php
require_once '../config/config.php';
exigerAdmin();
$admin_page_active = 'messages';

$pdo = getPDO();

// Mise à jour du statut d'un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
    $message_id = (int) $_POST['message_id'];
    $nouveau_statut = $_POST['nouveau_statut'] ?? '';
    if (in_array($nouveau_statut, ['non_traite', 'en_cours', 'traite'])) {
        $stmt = $pdo->prepare("
            UPDATE messages_contact
            SET statut = :statut, traite_par = :admin_id, date_traitement = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            'statut'   => $nouveau_statut,
            'admin_id' => $_SESSION['utilisateur_id'],
            'id'       => $message_id,
        ]);
    }
    header('Location: messages.php');
    exit;
}

// Suppression
if (isset($_GET['supprimer'])) {
    $pdo->prepare("DELETE FROM messages_contact WHERE id = :id")->execute(['id' => (int) $_GET['supprimer']]);
    header('Location: messages.php');
    exit;
}

// Filtre optionnel
$filtre = $_GET['filtre'] ?? 'tous';
$sql = "SELECT m.*, a.prenom AS admin_prenom FROM messages_contact m LEFT JOIN utilisateurs a ON a.id = m.traite_par";
if ($filtre === 'non_traite') {
    $sql .= " WHERE m.statut = 'non_traite'";
} elseif ($filtre === 'urgent') {
    $sql .= " WHERE m.urgent = 1";
}
$sql .= " ORDER BY m.urgent DESC, m.date_envoi DESC";
$messages = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Messages de contact — Admin Village des Femmes</title>
  <link rel="stylesheet" href="../styles.css" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body class="admin-body">

  <?php include 'includes/admin-header.php'; ?>

  <div class="admin-content">
    <h1>Messages de contact</h1>
    <p class="admin-sous-titre">Tous les messages reçus via le formulaire du site.</p>

    <div class="admin-page-actions" style="justify-content:flex-start; gap:8px;">
      <a href="messages.php?filtre=tous" class="btn-small <?= $filtre === 'tous' ? 'btn-valider' : 'btn-edit' ?>">Tous</a>
      <a href="messages.php?filtre=non_traite" class="btn-small <?= $filtre === 'non_traite' ? 'btn-valider' : 'btn-edit' ?>">Non traités</a>
      <a href="messages.php?filtre=urgent" class="btn-small <?= $filtre === 'urgent' ? 'btn-valider' : 'btn-edit' ?>">🆘 Urgents</a>
    </div>

    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr><th>Date</th><th>Nom</th><th>Contact</th><th>Sujet</th><th>Message</th><th>Statut</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php if (empty($messages)): ?>
            <tr><td colspan="7">Aucun message.</td></tr>
          <?php endif; ?>
          <?php foreach ($messages as $msg): ?>
            <tr>
              <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($msg['date_envoi']))) ?></td>
              <td><?= htmlspecialchars($msg['prenom'] . ' ' . $msg['nom']) ?> <?= $msg['urgent'] ? '🆘' : '' ?></td>
              <td>
                <a href="mailto:<?= htmlspecialchars($msg['email']) ?>"><?= htmlspecialchars($msg['email']) ?></a>
                <?php if ($msg['telephone']): ?><br><small><?= htmlspecialchars($msg['telephone']) ?></small><?php endif; ?>
              </td>
              <td><?= htmlspecialchars($msg['sujet']) ?></td>
              <td style="max-width:260px; white-space:normal;"><?= nl2br(htmlspecialchars(mb_strimwidth($msg['message'], 0, 160, '…'))) ?></td>
              <td>
                <form method="POST" style="display:flex; gap:6px; align-items:center;">
                  <input type="hidden" name="message_id" value="<?= $msg['id'] ?>" />
                  <select name="nouveau_statut" onchange="this.form.submit()" style="font-size:0.78rem; padding:4px 8px; border-radius:6px; border:1px solid #ddd;">
                    <option value="non_traite" <?= $msg['statut'] === 'non_traite' ? 'selected' : '' ?>>Non traité</option>
                    <option value="en_cours" <?= $msg['statut'] === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                    <option value="traite" <?= $msg['statut'] === 'traite' ? 'selected' : '' ?>>Traité</option>
                  </select>
                </form>
              </td>
              <td class="admin-actions">
                <a href="messages.php?supprimer=<?= $msg['id'] ?>" class="btn-small btn-delete"
                   onclick="return confirm('Supprimer ce message ?');">Supprimer</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>

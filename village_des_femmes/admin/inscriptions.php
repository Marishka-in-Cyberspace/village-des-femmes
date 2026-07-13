<?php
require_once '../config/config.php';
exigerAdmin();
$admin_page_active = 'inscriptions';

$pdo = getPDO();
$msg_succes = '';

// ── Annuler une inscription ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $inscription_id = (int) ($_POST['inscription_id'] ?? 0);
    if ($_POST['action'] === 'changer_statut') {
        $statut = $_POST['nouveau_statut'] ?? '';
        if (in_array($statut, ['confirme', 'liste_attente', 'annule'])) {
            $pdo->prepare("UPDATE inscriptions_evenements SET statut = :s WHERE id = :id")
                ->execute(['s' => $statut, 'id' => $inscription_id]);
            $msg_succes = "Statut mis à jour.";
        }
    } elseif ($_POST['action'] === 'supprimer') {
        $pdo->prepare("DELETE FROM inscriptions_evenements WHERE id = :id")
            ->execute(['id' => $inscription_id]);
        $msg_succes = "Inscription supprimée.";
    }
}

// ── Filtres ──
$evenement_id_filtre = isset($_GET['evenement_id']) ? (int) $_GET['evenement_id'] : null;
$utilisateur_id_filtre = isset($_GET['utilisateur_id']) ? (int) $_GET['utilisateur_id'] : null;

// Liste des événements pour le sélecteur de filtre
$evenements_liste = $pdo->query("SELECT id, titre, date_debut, statut FROM evenements ORDER BY date_debut DESC")->fetchAll();

// Requête principale
$sql = "
    SELECT ie.*,
           u.prenom, u.nom, u.email AS email_compte, u.telephone,
           e.titre AS titre_evenement, e.date_debut, e.lieu, e.statut AS statut_evt,
           e.places_max,
           (SELECT COUNT(*) FROM inscriptions_evenements ie2
            WHERE ie2.evenement_id = e.id AND ie2.statut = 'confirme') AS nb_inscrits
    FROM inscriptions_evenements ie
    LEFT JOIN utilisateurs u ON u.id = ie.utilisateur_id
    JOIN evenements e ON e.id = ie.evenement_id
    WHERE 1=1
";
$params = [];

if ($evenement_id_filtre) {
    $sql .= " AND ie.evenement_id = :evt_id";
    $params['evt_id'] = $evenement_id_filtre;
}
if ($utilisateur_id_filtre) {
    $sql .= " AND ie.utilisateur_id = :u_id";
    $params['u_id'] = $utilisateur_id_filtre;
}

$sql .= " ORDER BY e.date_debut DESC, ie.date_inscription ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$inscriptions = $stmt->fetchAll();

// Nom de l'événement filtré si applicable
$evenement_filtre_nom = '';
if ($evenement_id_filtre) {
    foreach ($evenements_liste as $e) {
        if ($e['id'] === $evenement_id_filtre) {
            $evenement_filtre_nom = $e['titre'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Participants — Admin Village des Femmes</title>
  <link rel="stylesheet" href="../styles.css" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body class="admin-body">

  <?php include 'includes/admin-header.php'; ?>

  <div class="admin-content">
    <h1>Participants aux événements</h1>
    <p class="admin-sous-titre">
      <?php if ($evenement_filtre_nom): ?>
        Filtré sur : <strong><?= htmlspecialchars($evenement_filtre_nom) ?></strong>
        — <a href="inscriptions.php" style="color:var(--rose-vif);">Voir toutes</a>
      <?php else: ?>
        Toutes les inscriptions à tous les événements.
      <?php endif; ?>
    </p>

    <?php if ($msg_succes): ?>
      <p class="admin-msg-success">✅ <?= htmlspecialchars($msg_succes) ?></p>
    <?php endif; ?>

    <!-- Filtre par événement -->
    <div style="margin-bottom:20px; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
      <label style="font-size:0.8rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#888;">
        Filtrer par événement :
      </label>
      <form method="GET" style="display:flex; gap:8px;">
        <select name="evenement_id" onchange="this.form.submit()"
                style="font-size:0.85rem; padding:8px 14px; border:2px solid #e0e0e0; border-radius:30px; outline:none;">
          <option value="">Tous les événements</option>
          <?php foreach ($evenements_liste as $e): ?>
            <option value="<?= $e['id'] ?>" <?= $evenement_id_filtre === $e['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars(date('d/m/Y', strtotime($e['date_debut'])) . ' — ' . $e['titre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>

    <?php if (empty($inscriptions)): ?>
      <div style="text-align:center; padding:60px 0; color:#888;">
        <p style="font-size:1.1rem;">Aucune inscription trouvée.</p>
        <?php if (!$evenement_id_filtre): ?>
          <p style="font-size:0.85rem; margin-top:8px;">
            Les inscriptions apparaîtront ici dès que des participants s'inscriront via le site.
          </p>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <!-- Résumé rapide si filtré sur un événement -->
      <?php if ($evenement_id_filtre && !empty($inscriptions)): ?>
        <?php
          $nb_confirmes = count(array_filter($inscriptions, fn($i) => $i['statut'] === 'confirme'));
          $nb_attente   = count(array_filter($inscriptions, fn($i) => $i['statut'] === 'liste_attente'));
          $places_max   = $inscriptions[0]['places_max'] ?? null;
        ?>
        <div class="admin-stats" style="grid-template-columns: repeat(3, 1fr); margin-bottom:24px;">
          <div class="admin-stat-card">
            <span class="admin-stat-nombre"><?= $nb_confirmes ?></span>
            <span class="admin-stat-label">Inscrits confirmés<?= $places_max ? " / $places_max places" : '' ?></span>
          </div>
          <div class="admin-stat-card">
            <span class="admin-stat-nombre"><?= $nb_attente ?></span>
            <span class="admin-stat-label">En liste d'attente</span>
          </div>
          <div class="admin-stat-card">
            <span class="admin-stat-nombre"><?= $places_max ? max(0, $places_max - $nb_confirmes) : '∞' ?></span>
            <span class="admin-stat-label">Places restantes</span>
          </div>
        </div>
      <?php endif; ?>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Inscrit le</th>
              <th>Participant</th>
              <th>Contact</th>
              <?php if (!$evenement_id_filtre): ?><th>Événement</th><?php endif; ?>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($inscriptions as $ins): ?>
              <tr>
                <td style="white-space:nowrap;">
                  <?= htmlspecialchars(date('d/m/Y H:i', strtotime($ins['date_inscription']))) ?>
                </td>
                <td>
                  <?php if ($ins['prenom']): ?>
                    <strong><?= htmlspecialchars($ins['prenom'] . ' ' . $ins['nom']) ?></strong>
                    <br><small style="color:#bbb;">Compte membre</small>
                  <?php else: ?>
                    <strong><?= htmlspecialchars($ins['nom_invite'] ?? 'Invité') ?></strong>
                    <br><small style="color:#bbb;">Sans compte</small>
                  <?php endif; ?>
                </td>
                <td>
                  <?php $email = $ins['email_compte'] ?: $ins['email_invite']; ?>
                  <?php if ($email): ?>
                    <a href="mailto:<?= htmlspecialchars($email) ?>" style="color:var(--rose-vif);">
                      <?= htmlspecialchars($email) ?>
                    </a>
                  <?php else: ?>
                    <span style="color:#ccc;">—</span>
                  <?php endif; ?>
                  <?php if ($ins['telephone']): ?>
                    <br><small><?= htmlspecialchars($ins['telephone']) ?></small>
                  <?php endif; ?>
                </td>
                <?php if (!$evenement_id_filtre): ?>
                  <td>
                    <a href="inscriptions.php?evenement_id=<?= $ins['evenement_id'] ?>"
                       style="color:var(--texte); font-weight:600;">
                      <?= htmlspecialchars($ins['titre_evenement']) ?>
                    </a>
                    <br><small style="color:#bbb;"><?= date('d/m/Y', strtotime($ins['date_debut'])) ?></small>
                  </td>
                <?php endif; ?>
                <td>
                  <form method="POST">
                    <input type="hidden" name="action" value="changer_statut" />
                    <input type="hidden" name="inscription_id" value="<?= $ins['id'] ?>" />
                    <?php if ($evenement_id_filtre): ?>
                      <input type="hidden" name="redirect_evenement" value="<?= $evenement_id_filtre ?>" />
                    <?php endif; ?>
                    <select name="nouveau_statut" onchange="this.form.submit()"
                            style="font-size:0.78rem; padding:4px 8px; border-radius:6px; border:1px solid #ddd;">
                      <option value="confirme" <?= $ins['statut'] === 'confirme' ? 'selected' : '' ?>>✅ Confirmé</option>
                      <option value="liste_attente" <?= $ins['statut'] === 'liste_attente' ? 'selected' : '' ?>>⏳ Liste d'attente</option>
                      <option value="annule" <?= $ins['statut'] === 'annule' ? 'selected' : '' ?>>❌ Annulé</option>
                    </select>
                  </form>
                </td>
                <td>
                  <form method="POST" onsubmit="return confirm('Supprimer cette inscription ?');">
                    <input type="hidden" name="action" value="supprimer" />
                    <input type="hidden" name="inscription_id" value="<?= $ins['id'] ?>" />
                    <button type="submit" class="btn-small btn-delete">Supprimer</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

</body>
</html>

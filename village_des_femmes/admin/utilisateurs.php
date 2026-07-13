<?php
require_once '../config/config.php';
exigerAdmin();
$admin_page_active = 'utilisateurs';

$pdo = getPDO();
$msg_succes = '';
$msg_erreur = '';

// ── Modification du rôle ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    $id = (int) ($_POST['utilisateur_id'] ?? 0);

    // Sécurité : on ne peut pas se modifier soi-même
    if ($id === (int) $_SESSION['utilisateur_id']) {
        $msg_erreur = "Tu ne peux pas modifier ton propre compte depuis cette page.";

    } elseif ($_POST['action'] === 'changer_role') {
        $nouveau_role = $_POST['nouveau_role'] ?? '';
        if (in_array($nouveau_role, ['utilisateur', 'benevole', 'admin'])) {
            $pdo->prepare("UPDATE utilisateurs SET role = :role WHERE id = :id")
                ->execute(['role' => $nouveau_role, 'id' => $id]);
            // Si on passe en bénévole, créer le profil s'il n'existe pas
            if ($nouveau_role === 'benevole') {
                $existe = $pdo->prepare("SELECT id FROM profils_benevoles WHERE utilisateur_id = :uid");
                $existe->execute(['uid' => $id]);
                if (!$existe->fetch()) {
                    $pdo->prepare("INSERT INTO profils_benevoles (utilisateur_id, statut_candidature) VALUES (:uid, 'en_attente')")
                        ->execute(['uid' => $id]);
                }
            }
            $msg_succes = "Rôle mis à jour.";
        }

    } elseif ($_POST['action'] === 'toggle_actif') {
        $actif_actuel = (int) ($_POST['actif_actuel'] ?? 1);
        $pdo->prepare("UPDATE utilisateurs SET actif = :actif WHERE id = :id")
            ->execute(['actif' => $actif_actuel ? 0 : 1, 'id' => $id]);
        $msg_succes = $actif_actuel ? "Compte désactivé." : "Compte réactivé.";

    } elseif ($_POST['action'] === 'supprimer') {
        // Suppression douce : on désactive plutôt que de supprimer pour garder l'historique des dons
        $pdo->prepare("UPDATE utilisateurs SET actif = 0 WHERE id = :id")->execute(['id' => $id]);
        $msg_succes = "Compte désactivé (les données sont conservées).";
    }
}

// ── Filtres ──
$filtre = $_GET['filtre'] ?? 'tous';
$recherche = nettoyer($_GET['q'] ?? '');

$sql = "SELECT u.*, pb.statut_candidature,
        (SELECT COUNT(*) FROM dons d WHERE d.utilisateur_id = u.id AND d.statut = 'valide') AS nb_dons,
        (SELECT COALESCE(SUM(montant),0) FROM dons d WHERE d.utilisateur_id = u.id AND d.statut = 'valide') AS total_dons,
        (SELECT COUNT(*) FROM inscriptions_evenements ie WHERE ie.utilisateur_id = u.id) AS nb_inscriptions
        FROM utilisateurs u
        LEFT JOIN profils_benevoles pb ON pb.utilisateur_id = u.id
        WHERE 1=1";

$params = [];

if ($filtre === 'admin') {
    $sql .= " AND u.role = 'admin'";
} elseif ($filtre === 'benevole') {
    $sql .= " AND u.role = 'benevole'";
} elseif ($filtre === 'utilisateur') {
    $sql .= " AND u.role = 'utilisateur'";
} elseif ($filtre === 'inactif') {
    $sql .= " AND u.actif = 0";
}

if ($recherche !== '') {
    $sql .= " AND (u.prenom LIKE :q OR u.nom LIKE :q OR u.email LIKE :q)";
    $params['q'] = "%$recherche%";
}

$sql .= " ORDER BY u.date_creation DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$utilisateurs = $stmt->fetchAll();

// Comptages pour les badges de filtres
$comptes = $pdo->query("
    SELECT
        COUNT(*) AS total,
        SUM(role = 'admin') AS admins,
        SUM(role = 'benevole') AS benevoles,
        SUM(role = 'utilisateur') AS utilisateurs,
        SUM(actif = 0) AS inactifs
    FROM utilisateurs
")->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Comptes utilisateurs — Admin Village des Femmes</title>
  <link rel="stylesheet" href="../styles.css" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body class="admin-body">

  <?php include 'includes/admin-header.php'; ?>

  <div class="admin-content">
    <h1>Comptes utilisateurs</h1>
    <p class="admin-sous-titre">
      Tous les comptes inscrits sur le site — utilisateurs, bénévoles et admins.
      <?= $comptes['total'] ?> comptes au total.
    </p>

    <?php if ($msg_succes): ?>
      <p class="admin-msg-success">✅ <?= htmlspecialchars($msg_succes) ?></p>
    <?php endif; ?>
    <?php if (isset($_GET['succes']) && $_GET['succes'] === 'compte_cree'): ?>
      <p class="admin-msg-success">✅ Compte créé avec succès.</p>
    <?php endif; ?>
    <?php if ($msg_erreur): ?>
      <p class="admin-msg-error">⚠️ <?= htmlspecialchars($msg_erreur) ?></p>
    <?php endif; ?>

    <div class="admin-page-actions">
      <a href="creer-compte.php" class="btn btn-rose">+ Créer un compte</a>
    </div>

    <!-- Filtres + recherche -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:18px;">
      <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <a href="utilisateurs.php" class="btn-small <?= $filtre === 'tous' ? 'btn-valider' : 'btn-edit' ?>">
          Tous (<?= $comptes['total'] ?>)
        </a>
        <a href="utilisateurs.php?filtre=admin" class="btn-small <?= $filtre === 'admin' ? 'btn-valider' : 'btn-edit' ?>">
          Admins (<?= $comptes['admins'] ?>)
        </a>
        <a href="utilisateurs.php?filtre=benevole" class="btn-small <?= $filtre === 'benevole' ? 'btn-valider' : 'btn-edit' ?>">
          Bénévoles (<?= $comptes['benevoles'] ?>)
        </a>
        <a href="utilisateurs.php?filtre=utilisateur" class="btn-small <?= $filtre === 'utilisateur' ? 'btn-valider' : 'btn-edit' ?>">
          Utilisateurs (<?= $comptes['utilisateurs'] ?>)
        </a>
        <a href="utilisateurs.php?filtre=inactif" class="btn-small <?= $filtre === 'inactif' ? 'btn-valider' : 'btn-edit' ?>">
          Inactifs (<?= $comptes['inactifs'] ?>)
        </a>
      </div>
      <form method="GET" style="display:flex; gap:8px;">
        <?php if ($filtre !== 'tous'): ?>
          <input type="hidden" name="filtre" value="<?= htmlspecialchars($filtre) ?>" />
        <?php endif; ?>
        <input type="text" name="q" value="<?= htmlspecialchars($recherche) ?>"
               placeholder="Rechercher nom, email…"
               style="padding:7px 14px; border:2px solid #e0e0e0; border-radius:30px; font-size:0.85rem; outline:none; width:220px;" />
        <button type="submit" class="btn-small btn-edit">Chercher</button>
        <?php if ($recherche): ?>
          <a href="utilisateurs.php?filtre=<?= htmlspecialchars($filtre) ?>" class="btn-small btn-delete">✕</a>
        <?php endif; ?>
      </form>
    </div>

    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Inscrit le</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Dons</th>
            <th>Événements</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($utilisateurs)): ?>
            <tr><td colspan="8" style="text-align:center; color:#888;">Aucun utilisateur trouvé.</td></tr>
          <?php endif; ?>

          <?php foreach ($utilisateurs as $u): ?>
            <tr <?= !$u['actif'] ? 'style="opacity:0.55;"' : '' ?>>
              <td style="white-space:nowrap;"><?= htmlspecialchars(date('d/m/Y', strtotime($u['date_creation']))) ?></td>
              <td>
                <strong><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></strong>
                <?php if ($u['telephone']): ?>
                  <br><small style="color:#999;"><?= htmlspecialchars($u['telephone']) ?></small>
                <?php endif; ?>
              </td>
              <td>
                <a href="mailto:<?= htmlspecialchars($u['email']) ?>" style="color:var(--rose-vif);">
                  <?= htmlspecialchars($u['email']) ?>
                </a>
                <?php if ($u['derniere_connexion']): ?>
                  <br><small style="color:#bbb;">Dernière co. : <?= date('d/m/Y', strtotime($u['derniere_connexion'])) ?></small>
                <?php endif; ?>
              </td>

              <!-- Changement de rôle inline -->
              <td>
                <?php if ($u['id'] === (int) $_SESSION['utilisateur_id']): ?>
                  <span class="badge badge-rose">admin (toi)</span>
                <?php else: ?>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="changer_role" />
                    <input type="hidden" name="utilisateur_id" value="<?= $u['id'] ?>" />
                    <select name="nouveau_role" onchange="this.form.submit()"
                            style="font-size:0.78rem; padding:4px 8px; border-radius:6px; border:1px solid #ddd;">
                      <option value="utilisateur" <?= $u['role'] === 'utilisateur' ? 'selected' : '' ?>>Utilisateur</option>
                      <option value="benevole" <?= $u['role'] === 'benevole' ? 'selected' : '' ?>>Bénévole</option>
                      <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                  </form>
                  <?php if ($u['role'] === 'benevole' && $u['statut_candidature']): ?>
                    <br><small style="color:#888;"><?= htmlspecialchars($u['statut_candidature']) ?></small>
                  <?php endif; ?>
                <?php endif; ?>
              </td>

              <td>
                <?php if ($u['nb_dons'] > 0): ?>
                  <span class="badge badge-vert"><?= $u['nb_dons'] ?> don<?= $u['nb_dons'] > 1 ? 's' : '' ?></span>
                  <br><small><?= number_format($u['total_dons'], 0, ',', ' ') ?> €</small>
                <?php else: ?>
                  <span style="color:#ccc;">—</span>
                <?php endif; ?>
              </td>

              <td>
                <?php if ($u['nb_inscriptions'] > 0): ?>
                  <a href="inscriptions.php?utilisateur_id=<?= $u['id'] ?>" class="badge badge-vert" style="text-decoration:none;">
                    <?= $u['nb_inscriptions'] ?> inscription<?= $u['nb_inscriptions'] > 1 ? 's' : '' ?>
                  </a>
                <?php else: ?>
                  <span style="color:#ccc;">—</span>
                <?php endif; ?>
              </td>

              <td>
                <?php if ($u['actif']): ?>
                  <span class="badge badge-vert">Actif</span>
                <?php else: ?>
                  <span class="badge badge-rouge">Inactif</span>
                <?php endif; ?>
              </td>

              <td class="admin-actions">
                <?php if ($u['id'] !== (int) $_SESSION['utilisateur_id']): ?>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="toggle_actif" />
                    <input type="hidden" name="utilisateur_id" value="<?= $u['id'] ?>" />
                    <input type="hidden" name="actif_actuel" value="<?= $u['actif'] ?>" />
                    <button type="submit" class="btn-small <?= $u['actif'] ? 'btn-delete' : 'btn-edit' ?>">
                      <?= $u['actif'] ? 'Désactiver' : 'Réactiver' ?>
                    </button>
                  </form>
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

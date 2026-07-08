<?php
require_once '../config/config.php';
exigerAdmin();
$admin_page_active = 'utilisateurs';

$pdo = getPDO();
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom       = nettoyer($_POST['prenom'] ?? '');
    $nom          = nettoyer($_POST['nom'] ?? '');
    $email        = nettoyer($_POST['email'] ?? '');
    $telephone    = nettoyer($_POST['telephone'] ?? '');
    $role         = nettoyer($_POST['role'] ?? 'utilisateur');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $actif        = isset($_POST['actif']) ? 1 : 0;

    $roles_valides = ['utilisateur', 'benevole', 'admin'];

    if (empty($prenom) || empty($nom)) {
        $erreur = "Le prénom et le nom sont obligatoires.";
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Email invalide.";
    } elseif (!in_array($role, $roles_valides)) {
        $erreur = "Rôle invalide.";
    } elseif (strlen($mot_de_passe) < 8) {
        $erreur = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $erreur = "Un compte existe déjà avec cet email.";
        } else {
            try {
                $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO utilisateurs (email, mot_de_passe, prenom, nom, telephone, role, actif, email_verifie)
                    VALUES (:email, :mdp, :prenom, :nom, :telephone, :role, :actif, 1)
                ");
                $stmt->execute([
                    'email'     => $email,
                    'mdp'       => $hash,
                    'prenom'    => $prenom,
                    'nom'       => $nom,
                    'telephone' => $telephone ?: null,
                    'role'      => $role,
                    'actif'     => $actif,
                ]);
                $nouvel_id = $pdo->lastInsertId();

                // Si bénévole, créer le profil automatiquement (statut accepté d'office car créé par admin)
                if ($role === 'benevole') {
                    $pdo->prepare("
                        INSERT INTO profils_benevoles (utilisateur_id, statut_candidature)
                        VALUES (:uid, 'accepte')
                    ")->execute(['uid' => $nouvel_id]);
                }

                header('Location: utilisateurs.php?succes=compte_cree');
                exit;

            } catch (PDOException $e) {
                $erreur = "Erreur lors de la création du compte. Merci de réessayer.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Créer un compte — Admin Village des Femmes</title>
  <link rel="stylesheet" href="../styles.css" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body class="admin-body">

  <?php include 'includes/admin-header.php'; ?>

  <div class="admin-content">
    <h1>Créer un compte utilisateur</h1>
    <p class="admin-sous-titre">
      Crée un compte manuellement — utile pour ajouter un bénévole ou un admin
      sans passer par le formulaire d'inscription public.
    </p>

    <?php if ($erreur): ?>
      <p class="admin-msg-error">⚠️ <?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form method="POST" class="admin-form-card">

      <div class="admin-form-row">
        <div class="form-group">
          <label for="prenom">Prénom *</label>
          <input type="text" id="prenom" name="prenom"
                 value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required />
        </div>
        <div class="form-group">
          <label for="nom">Nom *</label>
          <input type="text" id="nom" name="nom"
                 value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required />
        </div>
      </div>

      <div class="admin-form-row">
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" id="email" name="email"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required />
        </div>
        <div class="form-group">
          <label for="telephone">Téléphone</label>
          <input type="tel" id="telephone" name="telephone"
                 value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>"
                 placeholder="06 XX XX XX XX" />
        </div>
      </div>

      <div class="admin-form-row">
        <div class="form-group">
          <label for="role">Rôle *</label>
          <select id="role" name="role" required>
            <option value="utilisateur" <?= ($_POST['role'] ?? 'utilisateur') === 'utilisateur' ? 'selected' : '' ?>>
              Utilisateur (compte standard)
            </option>
            <option value="benevole" <?= ($_POST['role'] ?? '') === 'benevole' ? 'selected' : '' ?>>
              Bénévole
            </option>
            <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
              Admin
            </option>
          </select>
          <small style="color:#888; margin-top:4px; display:block;">
            Si bénévole : un profil bénévole est créé automatiquement (statut "accepté").
          </small>
        </div>
        <div class="form-group">
          <label for="mot_de_passe">Mot de passe temporaire * (8 caractères min.)</label>
          <input type="text" id="mot_de_passe" name="mot_de_passe"
                 placeholder="Choisis un mot de passe à communiquer à la personne"
                 minlength="8" required />
          <small style="color:#888; margin-top:4px; display:block;">
            La personne pourra le modifier depuis son espace personnel.
          </small>
        </div>
      </div>

      <div class="form-group" style="display:flex; align-items:center; gap:10px;">
        <input type="checkbox" id="actif" name="actif" checked
               style="width:18px; height:18px; accent-color:var(--rose-vif);" />
        <label for="actif" style="text-transform:none; letter-spacing:0; font-size:0.88rem; font-weight:400; color:var(--texte);">
          Compte actif immédiatement (décocher pour créer un compte désactivé)
        </label>
      </div>

      <div style="display:flex; gap:12px; margin-top:24px;">
        <button type="submit" class="btn btn-rose">Créer le compte</button>
        <a href="utilisateurs.php" class="btn btn-outline-vert">Annuler</a>
      </div>

    </form>
  </div>

</body>
</html>

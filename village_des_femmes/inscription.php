<?php
require_once 'config/config.php';
$page_active = 'connexion';

// Si déjà connecté, redirige vers l'espace personnel
if (estConnecte()) {
    header('Location: mon-compte.php');
    exit;
}

$erreur = '';
$valeurs = ['prenom' => '', 'nom' => '', 'email' => '', 'telephone' => '', 'role' => 'utilisateur'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs['prenom']    = nettoyer($_POST['prenom'] ?? '');
    $valeurs['nom']       = nettoyer($_POST['nom'] ?? '');
    $valeurs['email']     = nettoyer($_POST['email'] ?? '');
    $valeurs['telephone'] = nettoyer($_POST['telephone'] ?? '');
    $valeurs['role']      = nettoyer($_POST['role'] ?? 'utilisateur');
    $mot_de_passe         = $_POST['mot_de_passe'] ?? '';
    $mot_de_passe_confirm = $_POST['mot_de_passe_confirm'] ?? '';
    $rgpd                 = isset($_POST['rgpd']);

    $roles_valides = ['utilisateur', 'benevole'];

    if (empty($valeurs['prenom']) || empty($valeurs['nom'])) {
        $erreur = "Le prénom et le nom sont requis.";
    } elseif (empty($valeurs['email']) || !filter_var($valeurs['email'], FILTER_VALIDATE_EMAIL)) {
        $erreur = "Une adresse email valide est requise.";
    } elseif (!in_array($valeurs['role'], $roles_valides)) {
        $erreur = "Rôle invalide.";
    } elseif (strlen($mot_de_passe) < 8) {
        $erreur = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif ($mot_de_passe !== $mot_de_passe_confirm) {
        $erreur = "Les deux mots de passe ne correspondent pas.";
    } elseif (!$rgpd) {
        $erreur = "Tu dois accepter le traitement de tes données.";
    } else {
        $pdo = getPDO();

        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $valeurs['email']]);
        if ($stmt->fetch()) {
            $erreur = "Un compte existe déjà avec cet email. Essaie de te connecter.";
        } else {
            try {
                $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO utilisateurs (email, mot_de_passe, prenom, nom, telephone, role, email_verifie)
                    VALUES (:email, :mdp, :prenom, :nom, :telephone, :role, 1)
                ");
                $stmt->execute([
                    'email'     => $valeurs['email'],
                    'mdp'       => $hash,
                    'prenom'    => $valeurs['prenom'],
                    'nom'       => $valeurs['nom'],
                    'telephone' => $valeurs['telephone'] ?: null,
                    'role'      => $valeurs['role'],
                ]);
                $nouvel_id = $pdo->lastInsertId();

                // Si elle s'inscrit en tant que bénévole, on crée aussi son profil
                if ($valeurs['role'] === 'benevole') {
                    $pdo->prepare("
                        INSERT INTO profils_benevoles (utilisateur_id, statut_candidature)
                        VALUES (:uid, 'en_attente')
                    ")->execute(['uid' => $nouvel_id]);
                }

                // Connexion automatique après inscription
                $_SESSION['utilisateur_id'] = $nouvel_id;
                $_SESSION['role'] = $valeurs['role'];
                $_SESSION['prenom'] = $valeurs['prenom'];

                $redirection = ($valeurs['role'] === 'benevole') ? 'mon-compte.php?bienvenue=benevole' : 'mon-compte.php?bienvenue=utilisateur';
                header('Location: ' . $redirection);
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
  <title>Créer un compte — Village des Femmes</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="contact.css" />
  <link rel="stylesheet" href="compte.css" />
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <section class="hero-page">
    <span class="section-tag">Rejoignez-nous</span>
    <h1>Créer un <span class="highlight">compte</span></h1>
    <p>Que tu souhaites faire des dons réguliers ou devenir bénévole, crée ton espace personnel.</p>
  </section>

  <section class="contact-section">
    <div class="container" style="max-width:520px;">

      <div class="formulaire-wrap">

        <?php if ($erreur): ?>
          <p class="form-error" style="display:block;">⚠️ <?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="POST">
          <div class="form-group">
            <label>Je m'inscris en tant que *</label>
            <div class="role-choix">
              <label class="role-card">
                <input type="radio" name="role" value="utilisateur" <?= $valeurs['role'] === 'utilisateur' ? 'checked' : '' ?> />
                <span>💛 Utilisateur</span>
              </label>
              <label class="role-card">
                <input type="radio" name="role" value="benevole" <?= $valeurs['role'] === 'benevole' ? 'checked' : '' ?> />
                <span>🤝 Bénévole</span>
              </label>
            </div>
          </div>

          <div class="form-group">
            <label for="prenom">Prénom *</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($valeurs['prenom']) ?>" required />
          </div>
          <div class="form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($valeurs['nom']) ?>" required />
          </div>
          <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($valeurs['email']) ?>" required />
          </div>
          <div class="form-group">
            <label for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($valeurs['telephone']) ?>" />
          </div>
          <div class="form-group">
            <label for="mot_de_passe">Mot de passe * (8 caractères minimum)</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required minlength="8" />
          </div>
          <div class="form-group">
            <label for="mot_de_passe_confirm">Confirmer le mot de passe *</label>
            <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" required minlength="8" />
          </div>
          <div class="form-group form-check">
            <input type="checkbox" id="rgpd" name="rgpd" required />
            <label for="rgpd">J'accepte que mes données soient utilisées pour gérer mon compte.</label>
          </div>

          <button type="submit" class="btn btn-rose btn-submit" style="width:100%;">Créer mon compte</button>
        </form>

        <p style="text-align:center; margin-top:18px; font-size:0.85rem; color:var(--texte-clair);">
          Déjà un compte ? <a href="connexion.php" style="color:var(--rose-vif); font-weight:700;">Se connecter</a>
        </p>
      </div>

    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>

<?php
require_once '../config/config.php';

// Si déjà connecté en admin, redirige vers le dashboard
if (estConnecte() && $_SESSION['role'] === 'admin') {
    header('Location: dashboard.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = nettoyer($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (empty($email) || empty($mot_de_passe)) {
        $erreur = "Merci de remplir tous les champs.";
    } else {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email AND role = 'admin' AND actif = 1 LIMIT 1");
        $stmt->execute(['email' => $email]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            $_SESSION['utilisateur_id'] = $utilisateur['id'];
            $_SESSION['role'] = $utilisateur['role'];
            $_SESSION['prenom'] = $utilisateur['prenom'];

            // Met à jour la dernière connexion
            $pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = :id")
                ->execute(['id' => $utilisateur['id']]);

            header('Location: dashboard.php');
            exit;
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connexion Admin — Village des Femmes</title>
  <link rel="stylesheet" href="../styles.css" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body class="admin-login-body">

  <div class="login-card">
    <div class="login-logo">
      <img src="../images/logo.png" alt="Logo Village des Femmes" />
      <span>Village des Femmes</span>
    </div>
    <h1>Espace Administration</h1>

    <?php if ($erreur): ?>
      <p class="login-erreur">⚠️ <?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required autofocus />
      </div>
      <div class="form-group">
        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required />
      </div>
      <button type="submit" class="btn btn-rose" style="width:100%;">Se connecter</button>
    </form>

    <a href="../index.php" class="login-retour">← Retour au site</a>
  </div>

</body>
</html>

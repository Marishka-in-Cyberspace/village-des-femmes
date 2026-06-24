<?php
require_once 'config/config.php';
$page_active = 'connexion';

if (estConnecte()) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'mon-compte.php'));
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
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email AND actif = 1 LIMIT 1");
        $stmt->execute(['email' => $email]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            $_SESSION['utilisateur_id'] = $utilisateur['id'];
            $_SESSION['role'] = $utilisateur['role'];
            $_SESSION['prenom'] = $utilisateur['prenom'];

            $pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = :id")
                ->execute(['id' => $utilisateur['id']]);

            if ($utilisateur['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: mon-compte.php');
            }
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
  <title>Connexion — Village des Femmes</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="contact.css" />
  <link rel="stylesheet" href="compte.css" />
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <section class="hero-page">
    <span class="section-tag">Bon retour</span>
    <h1>Se <span class="highlight">connecter</span></h1>
    <p>Accède à ton espace personnel pour suivre tes dons ou ton engagement bénévole.</p>
  </section>

  <section class="contact-section">
    <div class="container" style="max-width:440px;">
      <div class="formulaire-wrap">

        <?php if ($erreur): ?>
          <p class="form-error" style="display:block;">⚠️ <?= htmlspecialchars($erreur) ?></p>
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
          <button type="submit" class="btn btn-rose btn-submit" style="width:100%;">Se connecter</button>
        </form>

        <p style="text-align:center; margin-top:18px; font-size:0.85rem; color:var(--texte-clair);">
          Pas encore de compte ? <a href="inscription.php" style="color:var(--rose-vif); font-weight:700;">S'inscrire</a>
        </p>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>

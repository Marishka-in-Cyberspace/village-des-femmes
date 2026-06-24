<?php
require_once '../config/config.php';
exigerAdmin();
$admin_page_active = 'evenements';

$pdo = getPDO();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$evenement = null;
$erreur = '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM evenements WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $evenement = $stmt->fetch();
    if (!$evenement) {
        header('Location: evenements.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre              = nettoyer($_POST['titre'] ?? '');
    $slug               = nettoyer($_POST['slug'] ?? '');
    $type_evenement     = nettoyer($_POST['type_evenement'] ?? '');
    $description_courte = nettoyer($_POST['description_courte'] ?? '');
    $description_longue = trim($_POST['description_longue'] ?? ''); // pas htmlspecialchars ici, on l'échappe à l'affichage
    $date_debut         = $_POST['date_debut'] ?? '';
    $lieu               = nettoyer($_POST['lieu'] ?? '');
    $prix_public        = nettoyer($_POST['prix_public'] ?? '');
    $image_principale   = nettoyer($_POST['image_principale'] ?? '');
    $statut             = nettoyer($_POST['statut'] ?? 'a_venir');
    $chiffre1_nombre    = nettoyer($_POST['chiffre1_nombre'] ?? '');
    $chiffre1_label     = nettoyer($_POST['chiffre1_label'] ?? '');
    $chiffre2_nombre    = nettoyer($_POST['chiffre2_nombre'] ?? '');
    $chiffre2_label     = nettoyer($_POST['chiffre2_label'] ?? '');
    $chiffre3_nombre    = nettoyer($_POST['chiffre3_nombre'] ?? '');
    $chiffre3_label     = nettoyer($_POST['chiffre3_label'] ?? '');

    if (empty($titre) || empty($slug) || empty($date_debut)) {
        $erreur = "Le titre, le slug et la date de début sont obligatoires.";
    } else {
        $params = [
            'titre' => $titre, 'slug' => $slug, 'type_evenement' => $type_evenement,
            'description_courte' => $description_courte, 'description_longue' => $description_longue,
            'date_debut' => $date_debut, 'lieu' => $lieu, 'prix_public' => $prix_public,
            'image_principale' => $image_principale ?: null, 'statut' => $statut,
            'chiffre1_nombre' => $chiffre1_nombre ?: null, 'chiffre1_label' => $chiffre1_label ?: null,
            'chiffre2_nombre' => $chiffre2_nombre ?: null, 'chiffre2_label' => $chiffre2_label ?: null,
            'chiffre3_nombre' => $chiffre3_nombre ?: null, 'chiffre3_label' => $chiffre3_label ?: null,
        ];

        try {
            if ($evenement) {
                $params['id'] = $evenement['id'];
                $pdo->prepare("
                    UPDATE evenements SET
                        titre = :titre, slug = :slug, type_evenement = :type_evenement,
                        description_courte = :description_courte, description_longue = :description_longue,
                        date_debut = :date_debut, lieu = :lieu, prix_public = :prix_public,
                        image_principale = :image_principale, statut = :statut,
                        chiffre1_nombre = :chiffre1_nombre, chiffre1_label = :chiffre1_label,
                        chiffre2_nombre = :chiffre2_nombre, chiffre2_label = :chiffre2_label,
                        chiffre3_nombre = :chiffre3_nombre, chiffre3_label = :chiffre3_label
                    WHERE id = :id
                ")->execute($params);
            } else {
                $params['cree_par'] = $_SESSION['utilisateur_id'];
                $pdo->prepare("
                    INSERT INTO evenements
                        (titre, slug, type_evenement, description_courte, description_longue,
                         date_debut, lieu, prix_public, image_principale, statut,
                         chiffre1_nombre, chiffre1_label, chiffre2_nombre, chiffre2_label,
                         chiffre3_nombre, chiffre3_label, cree_par)
                    VALUES
                        (:titre, :slug, :type_evenement, :description_courte, :description_longue,
                         :date_debut, :lieu, :prix_public, :image_principale, :statut,
                         :chiffre1_nombre, :chiffre1_label, :chiffre2_nombre, :chiffre2_label,
                         :chiffre3_nombre, :chiffre3_label, :cree_par)
                ")->execute($params);
            }
            header('Location: evenements.php?statut=enregistre');
            exit;
        } catch (PDOException $e) {
            $erreur = ($e->getCode() == 23000)
                ? "Ce slug est déjà utilisé par un autre événement. Choisis-en un autre."
                : "Erreur lors de l'enregistrement.";
        }
    }
}

// Valeurs par défaut pour le formulaire (création ou ré-affichage après erreur)
$v = fn($champ, $defaut = '') => htmlspecialchars($_POST[$champ] ?? $evenement[$champ] ?? $defaut);
$date_debut_value = '';
if (!empty($_POST['date_debut'])) {
    $date_debut_value = $_POST['date_debut'];
} elseif (!empty($evenement['date_debut'])) {
    $date_debut_value = date('Y-m-d\TH:i', strtotime($evenement['date_debut']));
}
$statut_value = $_POST['statut'] ?? $evenement['statut'] ?? 'a_venir';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $evenement ? 'Modifier' : 'Ajouter' ?> un événement — Admin</title>
  <link rel="stylesheet" href="../styles.css" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body class="admin-body">

  <?php include 'includes/admin-header.php'; ?>

  <div class="admin-content">
    <h1><?= $evenement ? 'Modifier l\'événement' : 'Ajouter un événement' ?></h1>
    <p class="admin-sous-titre">
      Statut "à venir" pour un événement futur, "passé" pour publier un compte-rendu.
    </p>

    <?php if ($erreur): ?>
      <p class="admin-msg-error">⚠️ <?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form method="POST" class="admin-form-card">

      <div class="admin-form-row">
        <div class="form-group">
          <label for="titre">Titre *</label>
          <input type="text" id="titre" name="titre" value="<?= $v('titre') ?>" required />
        </div>
        <div class="form-group">
          <label for="slug">Identifiant URL (slug) *</label>
          <input type="text" id="slug" name="slug" value="<?= $v('slug') ?>" placeholder="ex: cafe-printemps-2026" required />
        </div>
      </div>

      <div class="admin-form-row">
        <div class="form-group">
          <label for="type_evenement">Type</label>
          <input type="text" id="type_evenement" name="type_evenement" value="<?= $v('type_evenement') ?>" placeholder="Atelier, Permanence, Rencontre…" />
        </div>
        <div class="form-group">
          <label for="statut">Statut *</label>
          <select id="statut" name="statut" required>
            <option value="a_venir" <?= $statut_value === 'a_venir' ? 'selected' : '' ?>>À venir</option>
            <option value="en_cours" <?= $statut_value === 'en_cours' ? 'selected' : '' ?>>En cours</option>
            <option value="passe" <?= $statut_value === 'passe' ? 'selected' : '' ?>>Passé (compte-rendu)</option>
            <option value="annule" <?= $statut_value === 'annule' ? 'selected' : '' ?>>Annulé</option>
          </select>
        </div>
      </div>

      <div class="admin-form-row">
        <div class="form-group">
          <label for="date_debut">Date et heure *</label>
          <input type="datetime-local" id="date_debut" name="date_debut" value="<?= htmlspecialchars($date_debut_value) ?>" required />
        </div>
        <div class="form-group">
          <label for="lieu">Lieu</label>
          <input type="text" id="lieu" name="lieu" value="<?= $v('lieu') ?>" />
        </div>
      </div>

      <div class="admin-form-row">
        <div class="form-group">
          <label for="prix_public">Prix / Public</label>
          <input type="text" id="prix_public" name="prix_public" value="<?= $v('prix_public') ?>" placeholder="ex: Gratuit · Ouvert à toutes" />
        </div>
        <div class="form-group">
          <label for="image_principale">URL de l'image principale</label>
          <input type="text" id="image_principale" name="image_principale" value="<?= $v('image_principale') ?>" placeholder="images/mon-evenement.jpg" />
        </div>
      </div>

      <div class="form-group">
        <label for="description_courte">Description courte (affichée dans les cartes)</label>
        <textarea id="description_courte" name="description_courte" rows="2"><?= $v('description_courte') ?></textarea>
      </div>

      <div class="form-group">
        <label for="description_longue">Description longue (page détail / compte-rendu — un paragraphe par ligne)</label>
        <textarea id="description_longue" name="description_longue" rows="6"><?= htmlspecialchars($_POST['description_longue'] ?? $evenement['description_longue'] ?? '') ?></textarea>
      </div>

      <p style="font-size:0.8rem; color:#888; margin-bottom:10px;">
        Chiffres clés (affichés uniquement sur les comptes-rendus d'événements passés) :
      </p>
      <div class="admin-form-row-3">
        <div class="form-group">
          <label>Chiffre 1</label>
          <input type="text" name="chiffre1_nombre" value="<?= $v('chiffre1_nombre') ?>" placeholder="28" />
          <input type="text" name="chiffre1_label" value="<?= $v('chiffre1_label') ?>" placeholder="Participantes" style="margin-top:8px;" />
        </div>
        <div class="form-group">
          <label>Chiffre 2</label>
          <input type="text" name="chiffre2_nombre" value="<?= $v('chiffre2_nombre') ?>" placeholder="6" />
          <input type="text" name="chiffre2_label" value="<?= $v('chiffre2_label') ?>" placeholder="Bénévoles" style="margin-top:8px;" />
        </div>
        <div class="form-group">
          <label>Chiffre 3</label>
          <input type="text" name="chiffre3_nombre" value="<?= $v('chiffre3_nombre') ?>" placeholder="2h" />
          <input type="text" name="chiffre3_label" value="<?= $v('chiffre3_label') ?>" placeholder="De partage" style="margin-top:8px;" />
        </div>
      </div>

      <div style="display:flex; gap:12px; margin-top:24px;">
        <button type="submit" class="btn btn-rose"><?= $evenement ? 'Enregistrer les modifications' : 'Créer l\'événement' ?></button>
        <a href="evenements.php" class="btn btn-outline-vert">Annuler</a>
      </div>

    </form>
  </div>

</body>
</html>

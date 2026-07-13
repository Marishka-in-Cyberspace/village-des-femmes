<?php
/* ============================================
   VILLAGE DES FEMMES — Inscription à un événement
   ============================================
   Reçoit le formulaire d'inscription (depuis evenements.php)
   et enregistre dans inscriptions_evenements.
*/
require_once 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: evenements.php');
    exit;
}

$evenement_id = (int) ($_POST['evenement_id'] ?? 0);

if (!$evenement_id) {
    header('Location: evenements.php');
    exit;
}

$pdo = getPDO();

// Vérifie que l'événement existe et est bien "à venir"
$stmt = $pdo->prepare("SELECT * FROM evenements WHERE id = :id AND statut = 'a_venir' LIMIT 1");
$stmt->execute(['id' => $evenement_id]);
$evenement = $stmt->fetch();

if (!$evenement) {
    header('Location: evenements.php?erreur=evenement_introuvable');
    exit;
}

// Vérifie les places restantes si places_max est défini
if ($evenement['places_max']) {
    $nb_inscrits = $pdo->prepare("SELECT COUNT(*) FROM inscriptions_evenements WHERE evenement_id = :id AND statut = 'confirme'");
    $nb_inscrits->execute(['id' => $evenement_id]);
    $complet = $nb_inscrits->fetchColumn() >= $evenement['places_max'];
    $statut_inscription = $complet ? 'liste_attente' : 'confirme';
} else {
    $statut_inscription = 'confirme';
}

// Cas 1 : utilisateur connecté
if (estConnecte()) {
    $utilisateur_id = $_SESSION['utilisateur_id'];

    // Vérifie qu'il n'est pas déjà inscrit
    $stmt = $pdo->prepare("SELECT id FROM inscriptions_evenements WHERE evenement_id = :evt AND utilisateur_id = :uid");
    $stmt->execute(['evt' => $evenement_id, 'uid' => $utilisateur_id]);
    if ($stmt->fetch()) {
        header('Location: evenements.php?statut=deja_inscrit#evt' . $evenement_id);
        exit;
    }

    $pdo->prepare("
        INSERT INTO inscriptions_evenements (evenement_id, utilisateur_id, statut)
        VALUES (:evt, :uid, :statut)
    ")->execute(['evt' => $evenement_id, 'uid' => $utilisateur_id, 'statut' => $statut_inscription]);

// Cas 2 : invité sans compte
} else {
    $nom_invite   = nettoyer($_POST['nom_invite'] ?? '');
    $email_invite = nettoyer($_POST['email_invite'] ?? '');

    if (empty($nom_invite) || empty($email_invite) || !filter_var($email_invite, FILTER_VALIDATE_EMAIL)) {
        header('Location: evenements.php?erreur=donnees_manquantes#evt' . $evenement_id);
        exit;
    }

    // Vérifie qu'il n'est pas déjà inscrit avec ce mail
    $stmt = $pdo->prepare("SELECT id FROM inscriptions_evenements WHERE evenement_id = :evt AND email_invite = :email");
    $stmt->execute(['evt' => $evenement_id, 'email' => $email_invite]);
    if ($stmt->fetch()) {
        header('Location: evenements.php?statut=deja_inscrit#evt' . $evenement_id);
        exit;
    }

    $pdo->prepare("
        INSERT INTO inscriptions_evenements (evenement_id, nom_invite, email_invite, statut)
        VALUES (:evt, :nom, :email, :statut)
    ")->execute(['evt' => $evenement_id, 'nom' => $nom_invite, 'email' => $email_invite, 'statut' => $statut_inscription]);
}

$message = $statut_inscription === 'liste_attente' ? 'liste_attente' : 'inscrit';
header("Location: evenements.php?statut=$message#evt$evenement_id");
exit;

<?php
/* ============================================
   VILLAGE DES FEMMES — Traitement candidature bénévole
   ============================================
   Crée (ou retrouve) un utilisateur avec le rôle "benevole"
   et enregistre son profil/candidature.
*/
require_once 'config/config.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: benevole.php");
    exit;
}

$prenom         = nettoyer($_POST['prenom'] ?? '');
$nom            = nettoyer($_POST['nom'] ?? '');
$email          = nettoyer($_POST['email'] ?? '');
$telephone      = nettoyer($_POST['telephone'] ?? '');
$disponibilite  = nettoyer($_POST['disponibilite'] ?? '');
$competences    = nettoyer($_POST['competences'] ?? '');
$rgpd           = isset($_POST['rgpd']);

$dispos_valides = ['quelques_heures_mois', 'un_jour_semaine', 'plusieurs_jours_semaine', 'ponctuel_evenements'];

$erreurs = [];
if (empty($prenom)) $erreurs[] = "Le prénom est requis.";
if (empty($nom))    $erreurs[] = "Le nom est requis.";
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = "Email invalide.";
if (!in_array($disponibilite, $dispos_valides)) $erreurs[] = "Disponibilité invalide.";
if (empty($competences)) $erreurs[] = "Merci de décrire vos compétences.";
if (!$rgpd) $erreurs[] = "Vous devez accepter le traitement de vos données.";

if (!empty($erreurs)) {
    header("Location: benevole.php?statut=erreur&message=" . urlencode(implode(" ", $erreurs)));
    exit;
}

try {
    $pdo = getPDO();

    // Vérifie si un compte existe déjà avec cet email
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur) {
        $utilisateur_id = $utilisateur['id'];
    } else {
        // Crée un compte bénévole avec un mot de passe temporaire aléatoire
        // (la personne pourra le réinitialiser pour se connecter plus tard)
        $mot_de_passe_temp = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO utilisateurs (email, mot_de_passe, prenom, nom, telephone, role)
            VALUES (:email, :mdp, :prenom, :nom, :telephone, 'benevole')
        ");
        $stmt->execute([
            'email'     => $email,
            'mdp'       => $mot_de_passe_temp,
            'prenom'    => $prenom,
            'nom'       => $nom,
            'telephone' => $telephone ?: null,
        ]);
        $utilisateur_id = $pdo->lastInsertId();
    }

    // Enregistre ou met à jour le profil bénévole
    $stmt = $pdo->prepare("SELECT id FROM profils_benevoles WHERE utilisateur_id = :uid LIMIT 1");
    $stmt->execute(['uid' => $utilisateur_id]);
    $profil_existant = $stmt->fetch();

    if ($profil_existant) {
        $stmt = $pdo->prepare("
            UPDATE profils_benevoles
            SET disponibilite = :dispo, competences = :competences, statut_candidature = 'en_attente'
            WHERE utilisateur_id = :uid
        ");
        $stmt->execute(['dispo' => $disponibilite, 'competences' => $competences, 'uid' => $utilisateur_id]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO profils_benevoles (utilisateur_id, disponibilite, competences, statut_candidature)
            VALUES (:uid, :dispo, :competences, 'en_attente')
        ");
        $stmt->execute(['uid' => $utilisateur_id, 'dispo' => $disponibilite, 'competences' => $competences]);
    }

} catch (PDOException $e) {
    header("Location: benevole.php?statut=erreur&message=" . urlencode("Erreur lors de l'enregistrement. Merci de réessayer."));
    exit;
}

// Email de notification à l'association
$sujet_email = "Nouvelle candidature bénévole — $prenom $nom";
$corps_email = "Nouvelle candidature bénévole reçue :\n\n";
$corps_email .= "Nom : $prenom $nom\nEmail : $email\nTéléphone : $telephone\n";
$corps_email .= "Disponibilité : $disponibilite\nCompétences : $competences\n";
@mail(EMAIL_ASSOCIATION, $sujet_email, $corps_email, "Content-Type: text/plain; charset=UTF-8\r\n");

header("Location: benevole.php?statut=succes");
exit;

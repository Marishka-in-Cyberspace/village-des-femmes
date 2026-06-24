<?php
/* ============================================
   VILLAGE DES FEMMES — Traitement formulaire contact
   ============================================
   Enregistre le message dans la table messages_contact
   ET envoie un email à l'association (comme avant).
*/
require_once 'config/config.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact.php");
    exit;
}

$prenom     = isset($_POST['prenom'])    ? nettoyer($_POST['prenom'])    : '';
$nom        = isset($_POST['nom'])       ? nettoyer($_POST['nom'])       : '';
$email      = isset($_POST['email'])     ? nettoyer($_POST['email'])     : '';
$telephone  = isset($_POST['telephone']) ? nettoyer($_POST['telephone']) : '';
$sujet      = isset($_POST['sujet'])     ? nettoyer($_POST['sujet'])     : '';
$message    = isset($_POST['message'])   ? nettoyer($_POST['message'])   : '';
$rgpd       = isset($_POST['rgpd']);

// Validation
$erreurs = [];
if (empty($prenom))  $erreurs[] = "Le prénom est requis.";
if (empty($nom))     $erreurs[] = "Le nom est requis.";
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "Une adresse email valide est requise.";
}
if (empty($sujet))   $erreurs[] = "Le sujet est requis.";
if (empty($message)) $erreurs[] = "Le message est requis.";
if (!$rgpd)          $erreurs[] = "Vous devez accepter la politique de confidentialité.";

if (!empty($erreurs)) {
    $erreur_texte = urlencode(implode(" ", $erreurs));
    header("Location: contact.php?statut=erreur&message=$erreur_texte");
    exit;
}

// On marque "urgent" automatiquement si le sujet concerne une demande d'aide
$urgent = (strpos($sujet, "Demande d'aide") !== false) ? 1 : 0;

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare("
        INSERT INTO messages_contact (prenom, nom, email, telephone, sujet, message, urgent)
        VALUES (:prenom, :nom, :email, :telephone, :sujet, :message, :urgent)
    ");
    $stmt->execute([
        'prenom'    => $prenom,
        'nom'       => $nom,
        'email'     => $email,
        'telephone' => $telephone ?: null,
        'sujet'     => $sujet,
        'message'   => $message,
        'urgent'    => $urgent,
    ]);
} catch (PDOException $e) {
    header("Location: contact.php?statut=erreur&message=" . urlencode("Erreur d'enregistrement. Merci de réessayer."));
    exit;
}

// Envoi de l'email (comme dans l'ancien envoyer-message.php)
$sujet_email = "Nouveau message — $sujet — Village des Femmes";
$corps_email  = "Vous avez reçu un nouveau message via le formulaire de contact du site.\n\n";
$corps_email .= "Prénom : $prenom\n";
$corps_email .= "Nom : $nom\n";
$corps_email .= "Email : $email\n";
$corps_email .= "Téléphone : " . (!empty($telephone) ? $telephone : "Non renseigné") . "\n";
$corps_email .= "Sujet : $sujet\n\n";
$corps_email .= "Message :\n$message\n";

$entetes  = "MIME-Version: 1.0\r\n";
$entetes .= "Content-Type: text/plain; charset=UTF-8\r\n";
$entetes .= "From: Site Village des Femmes <noreply@villagedesfemmes.fr>\r\n";
$entetes .= "Reply-To: $prenom $nom <$email>\r\n";

@mail(EMAIL_ASSOCIATION, $sujet_email, $corps_email, $entetes);
// Note : le message est déjà enregistré en BDD même si l'envoi d'email échoue
// (utile si l'hébergeur ne supporte pas mail(), l'équipe peut le voir dans l'admin).

header("Location: contact.php?statut=succes");
exit;

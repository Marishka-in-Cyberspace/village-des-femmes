<?php
/* ============================================
   VILLAGE DES FEMMES — Traitement formulaire contact
   ============================================
   Ce script reçoit les données du formulaire de contact.html
   et envoie un email à l'adresse de l'association.
   Nécessite un hébergeur supportant PHP avec la fonction mail()
   activée (c'est le cas par défaut chez OVH, o2switch, Hostinger…).
*/

// Adresse de réception des messages
$destinataire = "villagesdesfemmes@gmail.com";

// On n'accepte que les requêtes POST (envoyées par le formulaire)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact.html");
    exit;
}

// Récupération et nettoyage des champs du formulaire
function nettoyer($valeur) {
    $valeur = trim($valeur);
    $valeur = stripslashes($valeur);
    $valeur = htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8');
    return $valeur;
}

$prenom     = isset($_POST['prenom'])     ? nettoyer($_POST['prenom'])     : '';
$nom        = isset($_POST['nom'])        ? nettoyer($_POST['nom'])        : '';
$email      = isset($_POST['email'])      ? nettoyer($_POST['email'])      : '';
$telephone  = isset($_POST['telephone'])  ? nettoyer($_POST['telephone'])  : '';
$sujet      = isset($_POST['sujet'])      ? nettoyer($_POST['sujet'])      : '';
$message    = isset($_POST['message'])    ? nettoyer($_POST['message'])    : '';
$rgpd       = isset($_POST['rgpd'])       ? true : false;

// Vérification des champs obligatoires
$erreurs = [];

if (empty($prenom))  $erreurs[] = "Le prénom est requis.";
if (empty($nom))     $erreurs[] = "Le nom est requis.";
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "Une adresse email valide est requise.";
}
if (empty($sujet))   $erreurs[] = "Le sujet est requis.";
if (empty($message)) $erreurs[] = "Le message est requis.";
if (!$rgpd)           $erreurs[] = "Vous devez accepter la politique de confidentialité.";

// Si des erreurs sont trouvées, on redirige vers le formulaire avec un message d'erreur
if (!empty($erreurs)) {
    $erreur_texte = urlencode(implode(" ", $erreurs));
    header("Location: contact.html?statut=erreur&message=$erreur_texte");
    exit;
}

// Construction du sujet de l'email
$sujet_email = "Nouveau message — $sujet — Village des Femmes";

// Construction du corps de l'email
$corps_email = "Vous avez reçu un nouveau message via le formulaire de contact du site.\n\n";
$corps_email .= "Prénom : $prenom\n";
$corps_email .= "Nom : $nom\n";
$corps_email .= "Email : $email\n";
$corps_email .= "Téléphone : " . (!empty($telephone) ? $telephone : "Non renseigné") . "\n";
$corps_email .= "Sujet : $sujet\n\n";
$corps_email .= "Message :\n$message\n";

// En-têtes de l'email
// "Reply-To" permet de répondre directement à la personne qui a écrit
$entetes  = "MIME-Version: 1.0\r\n";
$entetes .= "Content-Type: text/plain; charset=UTF-8\r\n";
$entetes .= "From: Site Village des Femmes <noreply@villagedesfemmes.fr>\r\n";
$entetes .= "Reply-To: $prenom $nom <$email>\r\n";

// Envoi de l'email
$envoi_reussi = mail($destinataire, $sujet_email, $corps_email, $entetes);

// Redirection selon le résultat de l'envoi
if ($envoi_reussi) {
    header("Location: contact.html?statut=succes");
} else {
    header("Location: contact.html?statut=erreur&message=" . urlencode("L'envoi a échoué. Merci de réessayer ou de nous contacter directement par email."));
}
exit;
?>

<?php
/* ============================================
   VILLAGE DES FEMMES — Enregistrement d'un don
   ============================================
   Enregistre la tentative de don (statut "en_attente")
   puis redirige vers la plateforme de paiement.
   La plateforme (HelloAsso/PayPal/Stripe) devra ensuite
   confirmer le paiement via un webhook qui passera le don
   en statut "valide" (à implémenter selon ta plateforme).
*/
require_once 'config/config.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: nous-soutenir.php");
    exit;
}

$montant = isset($_POST['montant']) ? (float) $_POST['montant'] : 0;

if ($montant <= 0) {
    header("Location: nous-soutenir.php?statut=erreur&message=" . urlencode("Montant invalide."));
    exit;
}

// Si un utilisateur est connecté, on rattache le don à son compte
$utilisateur_id = $_SESSION['utilisateur_id'] ?? null;

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare("
        INSERT INTO dons (utilisateur_id, montant, methode_paiement, statut)
        VALUES (:utilisateur_id, :montant, 'helloasso', 'en_attente')
    ");
    $stmt->execute([
        'utilisateur_id' => $utilisateur_id,
        'montant'        => $montant,
    ]);
    $don_id = $pdo->lastInsertId();
} catch (PDOException $e) {
    header("Location: nous-soutenir.php?statut=erreur&message=" . urlencode("Erreur lors de l'enregistrement du don."));
    exit;
}

// ⚠️ Remplace cette URL par celle de ta vraie plateforme de paiement.
// L'ID du don peut être passé en paramètre pour le retrouver après paiement.
$url_paiement = "https://www.helloasso.com/associations/ton-asso/formulaires/don?montant=" . $montant . "&ref=" . $don_id;

header("Location: " . $url_paiement);
exit;

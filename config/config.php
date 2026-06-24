<?php
/* ============================================
   VILLAGE DES FEMMES — Configuration générale
   ============================================ */

session_start();

require_once __DIR__ . '/database.php';

define('SITE_NAME', 'Village des Femmes');
define('EMAIL_ASSOCIATION', 'villagesdesfemmes@gmail.com');

// Fonction utilitaire de nettoyage des entrées utilisateur
function nettoyer(string $valeur): string {
    $valeur = trim($valeur);
    $valeur = stripslashes($valeur);
    $valeur = htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8');
    return $valeur;
}

// Vérifie si un admin est connecté
function estConnecte(): bool {
    return isset($_SESSION['utilisateur_id']) && isset($_SESSION['role']);
}

// Vérifie si l'utilisateur connecté est admin — sinon redirige
function exigerAdmin(): void {
    if (!estConnecte() || $_SESSION['role'] !== 'admin') {
        header('Location: /admin/login.php');
        exit;
    }
}

// Exige une connexion (n'importe quel rôle) — sinon redirige vers la page de connexion
function exigerConnexion(): void {
    if (!estConnecte()) {
        header('Location: connexion.php');
        exit;
    }
}

// Récupère les infos complètes de l'utilisateur connecté (ou null)
function utilisateurConnecte(): ?array {
    if (!estConnecte()) return null;
    static $utilisateur = null;
    if ($utilisateur === null) {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $_SESSION['utilisateur_id']]);
        $utilisateur = $stmt->fetch() ?: false;
    }
    return $utilisateur ?: null;
}

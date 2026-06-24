<?php
/* ============================================
   VILLAGE DES FEMMES — Connexion base de données
   ============================================
   Modifie les constantes ci-dessous selon ton hébergeur
   (OVH, o2switch, Hostinger… ou environnement local).
*/

define('DB_HOST', 'localhost');
define('DB_NAME', 'village_femmes');
define('DB_USER', 'root');          // à remplacer par ton utilisateur MySQL
define('DB_PASS', '');              // à remplacer par ton mot de passe MySQL
define('DB_CHARSET', 'utf8mb4');

function getPDO(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // En production, ne JAMAIS afficher $e->getMessage() à l'utilisateur
            die("Erreur de connexion à la base de données. Merci de réessayer plus tard.");
        }
    }
    return $pdo;
}

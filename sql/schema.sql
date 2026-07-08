-- ============================================================
--  VILLAGE DES FEMMES — Schéma de base de données
--  Moteur : MySQL / MariaDB (compatible PostgreSQL avec adaptations mineures)
-- ============================================================

-- Si ta base n'existe pas encore (ex: import direct dans phpMyAdmin),
-- décommente les 2 lignes suivantes. Si ton hébergeur a déjà créé la base
-- pour toi (cas fréquent chez OVH/Hostinger), laisse-les commentées.

-- CREATE DATABASE IF NOT EXISTS village_femmes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE village_femmes;

-- ────────────────────────────────────────────────────────────
-- 1. UTILISATEURS (utilisateurs, bénévoles, admin)
-- ────────────────────────────────────────────────────────────
CREATE TABLE utilisateurs (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe    VARCHAR(255) NOT NULL,        -- hash (bcrypt/argon2), jamais en clair
    prenom          VARCHAR(100) NOT NULL,
    nom             VARCHAR(100) NOT NULL,
    telephone       VARCHAR(20),
    role            ENUM('utilisateur', 'benevole', 'admin') NOT NULL DEFAULT 'utilisateur',
    date_creation   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion DATETIME,
    actif           BOOLEAN NOT NULL DEFAULT TRUE,
    email_verifie   BOOLEAN NOT NULL DEFAULT FALSE
);

-- ────────────────────────────────────────────────────────────
-- 2. PROFILS BÉNÉVOLES (infos spécifiques, 1-1 avec utilisateurs)
-- ────────────────────────────────────────────────────────────
CREATE TABLE profils_benevoles (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id      INT NOT NULL UNIQUE,
    disponibilite       ENUM('quelques_heures_mois', 'un_jour_semaine', 'plusieurs_jours_semaine', 'ponctuel_evenements') ,
    competences         TEXT,
    statut_candidature  ENUM('en_attente', 'accepte', 'refuse', 'inactif') NOT NULL DEFAULT 'en_attente',
    date_candidature    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    notes_internes       TEXT,                    -- visibles admin uniquement
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

-- ────────────────────────────────────────────────────────────
-- 3. ÉVÉNEMENTS
-- ────────────────────────────────────────────────────────────
CREATE TABLE evenements (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    titre               VARCHAR(255) NOT NULL,
    slug                VARCHAR(255) NOT NULL UNIQUE,   -- pour l'URL : evenement.php?slug=...
    type_evenement      VARCHAR(100),                   -- ex : "Atelier", "Permanence", "Rencontre"
    description_courte  TEXT,                           -- résumé affiché dans les cartes
    description_longue  TEXT,                           -- texte complet pour la page détail / compte-rendu
    date_debut          DATETIME NOT NULL,
    date_fin            DATETIME,
    lieu                VARCHAR(255),
    prix_public         VARCHAR(150),                   -- ex : "Gratuit · Ouvert à toutes"
    image_principale    VARCHAR(500),
    places_max          INT,
    statut              ENUM('a_venir', 'en_cours', 'passe', 'annule') NOT NULL DEFAULT 'a_venir',
    -- Champs spécifiques aux comptes-rendus (événements passés)
    chiffre1_nombre     VARCHAR(20),
    chiffre1_label      VARCHAR(100),
    chiffre2_nombre     VARCHAR(20),
    chiffre2_label      VARCHAR(100),
    chiffre3_nombre     VARCHAR(20),
    chiffre3_label      VARCHAR(100),
    cree_par            INT,
    date_creation       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cree_par) REFERENCES utilisateurs(id) ON DELETE SET NULL
);

-- ────────────────────────────────────────────────────────────
-- 3bis. GALERIE PHOTOS DES ÉVÉNEMENTS PASSÉS
-- ────────────────────────────────────────────────────────────
CREATE TABLE evenements_galerie (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    evenement_id    INT NOT NULL,
    image_url       VARCHAR(500) NOT NULL,
    ordre_affichage INT NOT NULL DEFAULT 0,
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE
);

-- ────────────────────────────────────────────────────────────
-- 4. INSCRIPTIONS AUX ÉVÉNEMENTS
-- ────────────────────────────────────────────────────────────
CREATE TABLE inscriptions_evenements (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    evenement_id    INT NOT NULL,
    utilisateur_id  INT,                          -- NULL si inscription sans compte
    nom_invite      VARCHAR(150),                 -- utilisé si pas de compte
    email_invite    VARCHAR(255),
    date_inscription DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut          ENUM('confirme', 'liste_attente', 'annule') NOT NULL DEFAULT 'confirme',
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    UNIQUE KEY unique_inscription (evenement_id, utilisateur_id)
);

-- ────────────────────────────────────────────────────────────
-- 5. DONS
-- ────────────────────────────────────────────────────────────
CREATE TABLE dons (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id      INT,                      -- NULL si don anonyme/sans compte
    nom_donateur        VARCHAR(150),             -- utilisé si pas de compte
    email_donateur      VARCHAR(255),
    montant             DECIMAL(10,2) NOT NULL,
    devise              VARCHAR(3) NOT NULL DEFAULT 'EUR',
    methode_paiement    ENUM('helloasso', 'paypal', 'stripe', 'virement', 'cheque', 'especes') NOT NULL,
    reference_transaction VARCHAR(255),           -- ID renvoyé par la plateforme de paiement
    statut              ENUM('en_attente', 'valide', 'echoue', 'rembourse') NOT NULL DEFAULT 'en_attente',
    don_recurrent       BOOLEAN NOT NULL DEFAULT FALSE,
    recu_fiscal_envoye  BOOLEAN NOT NULL DEFAULT FALSE,
    date_don            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);

-- ────────────────────────────────────────────────────────────
-- 6. MESSAGES DE CONTACT
-- ────────────────────────────────────────────────────────────
CREATE TABLE messages_contact (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    prenom          VARCHAR(100) NOT NULL,
    nom             VARCHAR(100) NOT NULL,
    email           VARCHAR(255) NOT NULL,
    telephone       VARCHAR(20),
    sujet           VARCHAR(150) NOT NULL,
    message         TEXT NOT NULL,
    statut          ENUM('non_traite', 'en_cours', 'traite') NOT NULL DEFAULT 'non_traite',
    traite_par      INT,
    date_envoi      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_traitement DATETIME,
    urgent          BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (traite_par) REFERENCES utilisateurs(id) ON DELETE SET NULL
);

-- ────────────────────────────────────────────────────────────
-- 7. ARTICLES / ACTUALITÉS (pour une future page blog/actu)
-- ────────────────────────────────────────────────────────────
CREATE TABLE articles (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    titre           VARCHAR(255) NOT NULL,
    slug            VARCHAR(255) NOT NULL UNIQUE,  -- pour l'URL : /actu/mon-article
    contenu         TEXT NOT NULL,
    image_url       VARCHAR(500),
    auteur_id       INT,
    statut          ENUM('brouillon', 'publie', 'archive') NOT NULL DEFAULT 'brouillon',
    date_publication DATETIME,
    date_creation   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);

-- ────────────────────────────────────────────────────────────
-- 8. ÉQUIPE / MEMBRES AFFICHÉS SUR "NOTRE MISSION"
-- ────────────────────────────────────────────────────────────
CREATE TABLE membres_equipe (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    prenom          VARCHAR(100) NOT NULL,
    nom             VARCHAR(100) NOT NULL,
    role            VARCHAR(150) NOT NULL,         -- ex: "Présidente", "Coordinatrice"
    bio             TEXT,
    photo_url       VARCHAR(500),
    ordre_affichage INT NOT NULL DEFAULT 0,        -- pour contrôler l'ordre sur la page
    visible         BOOLEAN NOT NULL DEFAULT TRUE
);

-- ────────────────────────────────────────────────────────────
-- INDEX utiles pour les requêtes fréquentes
-- ────────────────────────────────────────────────────────────
CREATE INDEX idx_evenements_date    ON evenements(date_debut);
CREATE INDEX idx_evenements_statut  ON evenements(statut);
CREATE INDEX idx_evenements_slug    ON evenements(slug);
CREATE INDEX idx_dons_date          ON dons(date_don);
CREATE INDEX idx_dons_utilisateur   ON dons(utilisateur_id);
CREATE INDEX idx_messages_statut    ON messages_contact(statut);
CREATE INDEX idx_articles_statut    ON articles(statut, date_publication);

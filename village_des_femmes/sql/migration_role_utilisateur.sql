-- ============================================================
--  MIGRATION — Renommage du rôle 'donateur' → 'utilisateur'
-- ============================================================
-- À exécuter UNE SEULE FOIS dans phpMyAdmin si tu avais déjà
-- créé la base avec l'ancienne version du schema.sql.
-- Si tu pars d'une base vierge avec le nouveau schema.sql,
-- ce fichier est inutile.

-- Étape 1 : modifier l'ENUM pour accepter les deux valeurs temporairement
ALTER TABLE utilisateurs
  MODIFY COLUMN role ENUM('donateur', 'utilisateur', 'benevole', 'admin') NOT NULL DEFAULT 'utilisateur';

-- Étape 2 : mettre à jour les données existantes
UPDATE utilisateurs SET role = 'utilisateur' WHERE role = 'donateur';

-- Étape 3 : retirer l'ancienne valeur 'donateur' de l'ENUM
ALTER TABLE utilisateurs
  MODIFY COLUMN role ENUM('utilisateur', 'benevole', 'admin') NOT NULL DEFAULT 'utilisateur';

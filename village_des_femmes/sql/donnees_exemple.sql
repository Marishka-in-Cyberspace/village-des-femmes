-- ============================================================
--  VILLAGE DES FEMMES — Données d'exemple
-- ============================================================

-- Admin (mot de passe : "motdepasse123" hashé avec password_hash() — à régénérer en prod)
INSERT INTO utilisateurs (email, mot_de_passe, prenom, nom, role, email_verifie)
VALUES ('admin@villagedesfemmes.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Claire', 'Dupont', 'admin', TRUE);
-- ⚠️ Hash ci-dessus = "password" à titre d'exemple. Génère le tien avec : password_hash('tonmotdepasse', PASSWORD_DEFAULT)

-- Bénévole
INSERT INTO utilisateurs (email, mot_de_passe, prenom, nom, telephone, role, email_verifie)
VALUES ('marie.benevole@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marie', 'Lambert', '0612345678', 'benevole', TRUE);

INSERT INTO profils_benevoles (utilisateur_id, disponibilite, competences, statut_candidature)
VALUES (2, 'un_jour_semaine', 'Soutien psychologique, écoute active, ancienne assistante sociale', 'accepte');

-- Donateur
INSERT INTO utilisateurs (email, mot_de_passe, prenom, nom, role, email_verifie)
VALUES ('sophie.donatrice@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sophie', 'Martin', 'utilisateur', TRUE);

-- ── Événements À VENIR ──
INSERT INTO evenements (titre, slug, type_evenement, description_courte, description_longue, date_debut, lieu, prix_public, statut, cree_par)
VALUES
('Nom de l''événement 1', 'evenement-1', 'Atelier',
 'Description courte de l''événement 1',
 'Descriptif complet de l''événement 1.',
 '2026-07-19 14:00:00', 'Lieu et adresse', 'Gratuit · Ouvert à toutes', 'a_venir', 1),

('Nom de l''événement 2', 'evenement-2', 'Permanence',
 'Description de l''événement 2',
 'Descriptif complet de l''événement 2.',
 '2026-07-23 10:00:00', 'Lieu et adresse', 'Sur rendez-vous', 'a_venir', 1),

('Nom de l''événement 3', 'evenement-3', 'Rencontre',
 'Description de l''événement 3',
 'Descriptif complet de l''événement 3.',
 '2026-08-02 18:00:00', 'Lieu et adresse', 'Gratuit', 'a_venir', 1);

-- ── Événements PASSÉS (comptes-rendus) ──
INSERT INTO evenements (titre, slug, type_evenement, description_courte, description_longue, date_debut, lieu, image_principale, statut,
                         chiffre1_nombre, chiffre1_label, chiffre2_nombre, chiffre2_label, chiffre3_nombre, chiffre3_label, cree_par)
VALUES
('A la découverte de la culture moldave', 'atelier-couture', 'Atelier',
 'Retour sur notre atelier de découverte de la culture moldave, organisé en collaboration avec la communauté locale.',
 'Lors de cet événement, les participantes ont eu l''occasion de découvrir la richesse de la culture moldave à travers des présentations, des dégustations de spécialités locales et des échanges avec des intervenants passionnés.',
 '2025-12-21 14:00:00', 'Local associatif, Paris', 'images/flyer-moldova.jpg', 'passe',
 '100+', 'Participants', '15', 'Bénévoles artistes', '6h', 'D''événement', 1),

('Café des Femmes — Printemps', 'cafe-printemps', 'Rencontre',
 'Notre rendez-vous mensuel du Café des Femmes, un moment de partage essentiel.',
 'Notre rendez-vous mensuel du Café des Femmes a réuni de nombreuses participantes autour d''un café et d''une ambiance chaleureuse. Un moment simple mais essentiel pour se retrouver, échanger et tisser des liens.',
 '2026-07-04 15:00:00', 'Local associatif, Paris', NULL, 'passe',
 '28', 'Participantes', '6', 'Bénévoles mobilisées', '1h30', 'De partage', 1),

('Permanence juridique — Janvier', 'permanence-janvier', 'Permanence',
 'Permanence mensuelle de notre juriste bénévole, dans un cadre confidentiel et bienveillant.',
 'Comme chaque mois, notre juriste bénévole a tenu une permanence pour répondre aux questions des femmes accompagnées par l''association, dans un cadre confidentiel et bienveillant.',
 '2026-06-25 09:00:00', 'Local associatif, Paris', NULL, 'passe',
 '9', 'Rendez-vous tenus', '1', 'Juriste bénévole', '2h', 'De permanence', 1);

-- Don
INSERT INTO dons (utilisateur_id, montant, methode_paiement, reference_transaction, statut, recu_fiscal_envoye)
VALUES (3, 50.00, 'helloasso', 'HA-2026-00123', 'valide', TRUE);

-- Don anonyme
INSERT INTO dons (nom_donateur, email_donateur, montant, methode_paiement, statut)
VALUES ('Anonyme', 'don.anonyme@email.fr', 20.00, 'paypal', 'valide');

-- Message de contact
INSERT INTO messages_contact (prenom, nom, email, sujet, message, statut, urgent)
VALUES ('Julie', 'Petit', 'julie.petit@email.fr', 'Demande d''aide ou d''accompagnement', 'Bonjour, j''aurais besoin d''un accompagnement, merci de me recontacter.', 'non_traite', TRUE);

-- Membre de l'équipe
INSERT INTO membres_equipe (prenom, nom, role, bio, ordre_affichage)
VALUES ('Claire', 'Dupont', 'Présidente', 'Fondatrice de l''association, engagée depuis 10 ans.', 1);

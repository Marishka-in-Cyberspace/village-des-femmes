# Village des Femmes — Site Web (PHP + MySQL)

## 🗂️ Structure du projet

```
vdf-final/
├── index.php                    ← Accueil (événements à venir dynamiques)
├── notre-mission.php
├── aide-accompagnement.php
├── evenements.php                ← Liste événements à venir + passés (dynamique)
├── evenement-passe.php           ← Page compte-rendu générique (?slug=...)
├── nous-soutenir.php             ← Formulaire don + lien bénévolat
├── benevole.php                  ← Page info bénévolat (renvoie vers inscription.php)
├── contact.php                   ← Formulaire de contact
│
├── inscription.php               ← Création de compte (donateur OU bénévole, au choix)
├── connexion.php                 ← Connexion (donateurs/bénévoles — différent de l'admin)
├── deconnexion.php               ← Déconnexion
├── mon-compte.php                ← Espace personnel : mes dons + mon profil bénévole
│
├── envoyer-message.php           ← Traite le formulaire de contact (BDD + email)
├── enregistrer-don.php           ← Enregistre un don avant redirection paiement
│
├── styles.css / *.css            ← Feuilles de style (identiques à avant)
├── compte.css                    ← Styles pour inscription/connexion/espace personnel
├── main.js                       ← JS commun (menu, dons, scroll, formulaires)
│
├── config/
│   ├── database.php              ← Connexion PDO à MySQL
│   ├── config.php                ← Session, constantes, fonctions utilitaires
│   └── .htaccess                 ← Bloque l'accès direct au dossier
│
├── includes/
│   ├── header.php                ← Header commun à toutes les pages publiques
│   └── footer.php                ← Footer + molette scroll + script JS
│
├── sql/
│   ├── schema.sql                ← Toutes les tables (à exécuter en 1er)
│   ├── donnees_exemple.sql       ← Données de test (à exécuter en 2nd, optionnel)
│   └── .htaccess
│
├── images/                       ← Dossier à remplir avec tes vraies images
│
└── admin/                        ← Back-office (réservé aux comptes "admin")
    ├── login.php / logout.php
    ├── dashboard.php              ← Statistiques générales
    ├── evenements.php             ← Liste + suppression d'événements
    ├── evenement-form.php         ← Ajout / modification d'un événement
    ├── messages.php               ← Messages de contact reçus (changer statut)
    ├── dons.php                   ← Suivi des dons (valider, marquer reçu fiscal)
    ├── benevoles.php               ← Candidatures bénévoles (accepter/refuser)
    ├── admin.css
    └── includes/admin-header.php
```

## 🚀 Installation

### 1. Base de données
Crée une base MySQL nommée `village_femmes`, puis exécute dans l'ordre :
```bash
mysql -u root -p village_femmes < sql/schema.sql
mysql -u root -p village_femmes < sql/donnees_exemple.sql   # optionnel, données de test
```

### 2. Connexion à la base
Modifie `config/database.php` avec tes identifiants MySQL :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'village_femmes');
define('DB_USER', 'ton_utilisateur');
define('DB_PASS', 'ton_mot_de_passe');
```

### 3. Créer ton premier vrai compte admin
Le compte admin d'exemple dans `donnees_exemple.sql` utilise un mot de passe factice.
Génère ton propre hash avec ce petit script PHP (à exécuter une fois puis supprimer) :
```php
<?php
echo password_hash('TonMotDePasseSolide', PASSWORD_DEFAULT);
```
Colle le résultat dans la colonne `mot_de_passe` de la table `utilisateurs` pour ton compte admin
(via phpMyAdmin ou une requête `UPDATE utilisateurs SET mot_de_passe = '...' WHERE email = '...'`).

### 4. Lancer le site en local (PHP intégré)
```bash
php -S localhost:8000
```
Puis ouvre `http://localhost:8000` dans ton navigateur, et `http://localhost:8000/admin/login.php`
pour l'espace d'administration.

### 5. Ajouter tes images
Place tes fichiers dans `images/` : `logo.png`, `hero-accueil.jpeg`, `mission.jpg`,
les icônes (`icone-accueil.png`, etc.), les photos d'événements…
Les chemins sont déjà prévus dans le code, il suffit de déposer les bons fichiers.

## 👤 Comptes publics (donateurs / bénévoles)

Tout le monde peut créer un compte via `inscription.php`, en choisissant son rôle :
- **Donateur** : pourra suivre l'historique de ses dons depuis `mon-compte.php`.
- **Bénévole** : un profil bénévole est créé automatiquement (statut "en attente"),
  modifiable ensuite depuis `mon-compte.php` (disponibilités, compétences).

La connexion se fait via `connexion.php` (différente de `/admin/login.php`, réservée aux admins).
Après connexion, le lien **"Mon compte"** apparaît dans le menu à la place de "Connexion".

⚠️ Si tu avais testé l'ancienne version du formulaire bénévole (qui créait un compte sans
mot de passe utilisable), ce comportement a été retiré. Le seul moyen de créer un compte
bénévole est maintenant `inscription.php`, avec un vrai mot de passe choisi par la personne.

## ⚙️ Ce qui est maintenant dynamique

| Fonctionnalité | Avant | Maintenant |
|---|---|---|
| Événements à venir (accueil + page événements) | Codé en dur dans le HTML | Lu depuis la table `evenements` |
| Comptes-rendus d'événements passés | 3 fichiers HTML dupliqués | 1 seule page `evenement-passe.php?slug=...` |
| Formulaire de contact | Envoi email uniquement | Enregistré en BDD (`messages_contact`) **+** email |
| Formulaire de don | Simple `alert()` JS | Enregistré en BDD (`dons`, statut "en_attente") puis redirection paiement |
| Bénévolat | Pas de formulaire dédié | `benevole.php` → crée un compte + profil bénévole |
| Année d'existence ("3 ans") | Texte fixe à changer chaque année | Calculée automatiquement (`date('Y') - 2023`) |
| Gestion du contenu | Modifier le code à chaque changement | Back-office `/admin` avec login |

## 🔐 Espace admin

Accessible sur `/admin/login.php`, réservé aux comptes ayant le rôle `admin` dans la table `utilisateurs`.
Permet de :
- Voir les statistiques clés (dons, messages non traités, candidatures en attente…)
- Ajouter / modifier / supprimer des événements (à venir et comptes-rendus passés)
- Traiter les messages de contact (changer leur statut, repérer les urgents 🆘)
- Suivre les dons et marquer les reçus fiscaux comme envoyés
- Accepter ou refuser les candidatures bénévoles

## 💳 Lien de paiement (HelloAsso / PayPal / Stripe)

Dans `enregistrer-don.php`, remplace l'URL d'exemple par celle de ta vraie plateforme :
```php
$url_paiement = "https://www.helloasso.com/associations/ton-asso/formulaires/don?montant=" . $montant . "&ref=" . $don_id;
```
Pour confirmer automatiquement les dons (passer de "en_attente" à "valide"), il faudra
configurer un **webhook** côté plateforme de paiement qui appelle un script PHP dédié
(différent selon HelloAsso / PayPal / Stripe — à voir une fois la plateforme choisie).
En attendant, l'admin peut valider manuellement les dons reçus depuis `/admin/dons.php`.

## 🔒 Sécurité — points importants

- Les mots de passe sont hashés avec `password_hash()` / vérifiés avec `password_verify()` — jamais stockés en clair.
- Toutes les requêtes SQL utilisent des requêtes préparées (PDO) — pas d'injection SQL possible.
- Les entrées utilisateur sont systématiquement nettoyées via `nettoyer()` avant affichage.
- Les dossiers `config/` et `sql/` sont protégés par `.htaccess` (accès direct refusé).
- Penser à activer HTTPS en production pour protéger les mots de passe en transit.

## 📝 Pages encore à connecter si besoin

- Un compte "espace personnel" pour les donateurs/bénévoles (historique de leurs dons,
  modification de leurs disponibilités) — la table `utilisateurs` est déjà prête pour ça.
- Inscriptions aux événements avec gestion des places (`inscriptions_evenements` existe déjà en BDD).
- Articles / actualités (`articles` existe déjà en BDD, page front à créer si besoin).

## Polices
Le site utilise Google Fonts :
- **Playfair Display** — titres
- **Lato** — corps de texte

---
Dernière mise à jour : connexion complète du site à une base MySQL avec espace admin.

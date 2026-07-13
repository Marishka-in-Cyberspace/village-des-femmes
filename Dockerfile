# =============================================================
# DOCKERFILE — Recette pour construire le conteneur "serveur web"
#
# Un Dockerfile est une liste d'instructions exécutées de haut en bas
# pour créer une "image" Docker (= une boîte prête à l'emploi).
# =============================================================


# --- ÉTAPE 1 : Choisir la base ---
# On part d'une image officielle qui contient déjà PHP 8.2 + Apache.
# Apache est le logiciel serveur web qui reçoit les requêtes HTTP
# et sert les fichiers PHP au navigateur.
# "FROM" = "commence à partir de cette base déjà existante"
FROM php:8.2-apache


# --- ÉTAPE 2 : Installer les extensions PHP nécessaires ---
# Par défaut, PHP ne sait pas parler à MySQL.
# On installe deux extensions :
#   - pdo        : couche d'abstraction pour les bases de données
#   - pdo_mysql  : le "traducteur" spécifique pour MySQL
# C'est ce qui permet au code PHP (config/database.php) de se connecter à la BDD.
RUN docker-php-ext-install pdo pdo_mysql


# --- ÉTAPE 3 : Activer le module "rewrite" d'Apache ---
# mod_rewrite permet à Apache de lire les fichiers .htaccess.
# Les .htaccess servent ici à bloquer l'accès direct aux dossiers
# sensibles comme /config/ et /sql/ (ils contiennent les mots de passe !).
RUN a2enmod rewrite


# --- ÉTAPE 4 : Autoriser les fichiers .htaccess ---
# Par défaut, Apache ignore les .htaccess pour des raisons de performance.
# Cette commande modifie la configuration d'Apache pour les activer.
# "sed -i" = chercher/remplacer dans un fichier
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf


# --- ÉTAPE 5 : Copier les fichiers du site dans le conteneur ---
# "COPY . /var/www/html/" signifie :
#   - "." = tout ce qu'il y a dans le dossier du projet sur ton PC
#   - "/var/www/html/" = l'endroit où Apache cherche les fichiers à servir
#                        (c'est le dossier racine du site web dans le conteneur)
COPY ./village_des_femmes/ /var/www/html/


# --- ÉTAPE 6 : Corriger les permissions des fichiers ---
# Dans Linux (le système à l'intérieur du conteneur), chaque fichier
# appartient à un utilisateur. Apache tourne sous l'utilisateur "www-data".
# Cette commande lui donne les droits de lire tous les fichiers du site.
RUN chown -R www-data:www-data /var/www/html/

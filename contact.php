<?php
require_once 'config/config.php';
$page_active = 'contact';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact — Village des Femmes</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="contact.css" />
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <!-- ══ HERO ══ -->
  <section class="hero-page">
    <span class="section-tag">Nous sommes là</span>
    <h1>Nous <span class="highlight">contacter</span></h1>
    <p>
      Vous avez une question, besoin d'aide ou souhaitez nous rejoindre ?
      Écrivez-nous, nous vous répondons dans les plus brefs délais.
    </p>
  </section>

  <!-- ══ CONTENU CONTACT ══ -->
  <section class="contact-section">
    <div class="container contact-inner">

      <!-- Formulaire -->
      <div class="formulaire-wrap">
        <h2>Envoyez-nous un message</h2>
        <p class="form-note">Toutes les demandes sont traitées avec confidentialité.</p>

        <form action="envoyer-message.php" method="POST" id="contact-form">

          <div class="form-group">
            <label for="prenom">Prénom *</label>
            <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" required />
          </div>
          <div class="form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" placeholder="Votre nom" required />
          </div>
          <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" placeholder="votre@email.fr" required />
          </div>
          <div class="form-group">
            <label for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" placeholder="06 XX XX XX XX" />
          </div>
          <div class="form-group">
            <label for="sujet">Sujet *</label>
            <select id="sujet" name="sujet" required>
              <option value="">Choisissez un sujet…</option>
              <option>Demande d'aide ou d'accompagnement</option>
              <option>Devenir bénévole</option>
              <option>Faire un don</option>
              <option>Partenariat / Presse</option>
              <option>Autre question</option>
            </select>
          </div>
          <div class="form-group form-group-full">
            <label for="message">Message *</label>
            <textarea id="message" name="message" rows="5" placeholder="Votre message…" required></textarea>
          </div>
          <div class="form-group form-group-full form-check">
            <input type="checkbox" id="rgpd" name="rgpd" required />
            <label for="rgpd">
              J'accepte que mes données soient utilisées pour traiter ma demande,
              conformément à la <a href="#">politique de confidentialité</a>.
            </label>
          </div>
          <button type="submit" class="btn btn-rose btn-submit" id="btn-submit">
            Envoyer le message
          </button>
          <p class="form-success" id="form-success" hidden>
            ✅ Votre message a bien été envoyé ! Nous vous répondrons rapidement.
          </p>
          <p class="form-error" id="form-error" hidden></p>

        </form>
      </div>

      <!-- Infos de contact -->
      <div class="contact-infos">
        <h2>Nos coordonnées</h2>

        <div class="info-bloc">
          <div class="info-icone rose">📍</div>
          <div>
            <h3>Adresse</h3>
            <p>13 rue François Couperin <br> 93110 Rosny-sous-Bois<br>75015 Paris</p>
          </div>
        </div>

        <div class="info-bloc">
          <div class="info-icone vert">📞</div>
          <div>
            <h3>Téléphone</h3>
            <p><a href="tel:+33100000000">01 XX XX XX XX</a></p>
            <p class="info-small">disponibilité d'appel</p>
          </div>
        </div>

        <div class="info-bloc">
          <div class="info-icone rose">✉️</div>
          <div>
            <h3>Email</h3>
            <p><a href="mailto:contact@villagefemmes.fr">villagesdesfemmes@gmail.com</a></p>
          </div>
        </div>

        <div class="reseaux">
          <h3>Suivez-nous</h3>
          <div class="reseaux-liens">
            <a href="#" class="reseau-btn">Facebook</a>
            <a href="#" class="reseau-btn">Instagram</a>
            <a href="#" class="reseau-btn">LinkedIn</a>
          </div>
        </div>

        <div class="urgence-rappel">
          <p>🆘 En cas de danger immédiat, appelez le <strong>17</strong> (Police) ou le <strong>3919</strong> (Violences Femmes Info)</p>
        </div>
      </div>

    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>

<?php
require_once 'config/config.php';
$page_active = 'nous-soutenir';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Devenir Bénévole — Village des Femmes</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="contact.css" />
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <section class="hero-page">
    <span class="section-tag">Rejoignez l'équipe</span>
    <h1>Devenir <span class="highlight">Bénévole</span></h1>
    <p>
      Votre temps et vos compétences sont précieux. Rejoignez nos bénévoles
      et agissez concrètement aux côtés des femmes que nous accompagnons.
    </p>
    <p style="font-size:0.85rem; color:var(--texte-clair); max-width:480px; margin:0 auto 8px;">
      Pour candidater, crée d'abord ton compte bénévole (rapide, 1 minute) :
      tu pourras ensuite renseigner tes disponibilités et compétences depuis ton espace personnel.
    </p>
    <a href="inscription.php" class="btn btn-rose" style="margin-top:8px;">Créer mon compte bénévole →</a>
  </section>

  <section class="contact-section">
    <div class="container" style="max-width:760px;">
      <div class="contact-infos" style="display:grid; grid-template-columns:repeat(3,1fr); gap:24px;">
        <div class="info-bloc">
          <div class="info-icone rose">💛</div>
          <div>
            <h3>Donner de son temps</h3>
            <p>Quelques heures par mois peuvent changer la vie de quelqu'un.</p>
          </div>
        </div>
        <div class="info-bloc">
          <div class="info-icone vert">🤝</div>
          <div>
            <h3>Partager ses compétences</h3>
            <p>Juridique, administratif, soutien psychologique, logistique… toutes les compétences sont utiles.</p>
          </div>
        </div>
        <div class="info-bloc">
          <div class="info-icone rose">🌱</div>
          <div>
            <h3>Faire partie d'une communauté</h3>
            <p>Rejoignez un réseau de personnes engagées et bienveillantes.</p>
          </div>
        </div>
      </div>

      <div style="text-align:center; margin-top:40px;">
        <a href="inscription.php" class="btn btn-rose">Créer mon compte bénévole</a>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>

<?php
require_once 'config/config.php';
$page_active = 'nous-soutenir';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nous Soutenir — Village des Femmes</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="nous-soutenir.css" />
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <!-- ══ HERO ══ -->
  <section class="hero-soutenir">
    <div class="hero-content">
      <span class="section-tag">Agir ensemble</span>
      <h1>Ensemble,<br><span class="highlight">faisons la différence</span></h1>
      <p>
        Grâce à votre soutien, nous pouvons accueillir, écouter, accompagner
        et protéger chaque femme vers un avenir plus serein et libre.
      </p>
      <div class="hero-btns">
        <a href="#faire-un-don" class="btn btn-noir">Faire un don</a>
        <a href="#benevole" class="btn btn-outline-vert">Devenir bénévole</a>
      </div>
    </div>
    <div class="hero-photo">
        <div class="hero-soutenir-placeholder">
        <img src="images/nous-soutenir.jpg" alt="Nous soutenir" />
        </div>
    </div>
  </section>

  <!-- ══ POURQUOI VOTRE SOUTIEN ══ -->
  <section class="pourquoi">
    <div class="container">
      <h2>Pourquoi votre soutien est <span class="highlight">essentiel</span></h2>
      <div class="piliers-grid">

        <div class="pilier">
          <div class="pilier-icon vert-clair"><img src="images/icone-accueil.png" alt="accueillir"></div>
          <h3>Accueillir</h3>
          <p>Offrir un lieu sûr et bienveillant d'écoute et d'accompagnement</p>
        </div>

        <div class="pilier">
          <div class="pilier-icon rose-outline"><img src="images/icone-ecoute.png" alt="écouter"></div>
          <h3>Écouter et accompagner</h3>
          <p>Être présentes à chaque étape du parcours de chaque femme</p>
        </div>

        <div class="pilier">
          <div class="pilier-icon vert-clair"><img src="images/icone-proteger.png" alt="protéger"></div>
          <h3>Protéger</h3>
          <p>Défendre les droits et la sécurité des femmes accompagnées</p>
        </div>

        <div class="pilier">
          <div class="pilier-icon rose-pale"><img src="images/icone-lien.png" alt="créer du lien"></div>
          <h3>Créer du lien</h3>
          <p>Briser l'isolement et tisser une communauté solidaire et chaleureuse</p>
        </div>

      </div>
    </div>
  </section>

  <!-- ══ FAIRE UN DON ══ -->
  <section class="faire-un-don" id="faire-un-don">
    <div class="container">
      <div class="don-card">
        <h2>Faire un don</h2>
        <p>
          Votre don aide à poursuivre et développer nos actions.
          Chaque contribution soutient concrètement les femmes accompagnées par l'association.
        </p>

        <form action="enregistrer-don.php" method="POST" id="don-form">
          <div class="montants">
            <button type="button" class="montant-btn" data-montant="10">10€</button>
            <button type="button" class="montant-btn active" data-montant="20">20€</button>
            <button type="button" class="montant-btn" data-montant="50">50€</button>
            <button type="button" class="montant-btn autre" id="btn-autre">Autre montant</button>
          </div>
          <div class="autre-montant-wrap" id="autre-wrap" hidden>
            <input type="number" id="montant-libre" placeholder="Votre montant (€)" min="1" />
          </div>
          <input type="hidden" name="montant" id="montant-hidden" value="20" />

          <button type="submit" class="btn btn-rose btn-don" id="btn-don">Je fais un don (HelloAsso)</button>
        </form>

        <?php if (LIEN_PAYPAL !== '#'): ?>
          <a href="<?= htmlspecialchars(LIEN_PAYPAL) ?>" target="_blank" rel="noopener" class="btn btn-outline-vert btn-don" style="margin-top:10px;">
            Faire un don via PayPal
          </a>
        <?php endif; ?>

        <p class="don-note">🔒 Paiement sécurisé · Reçu fiscal disponible</p>
      </div>
    </div>
  </section>

  <!-- ══ DEVENIR BÉNÉVOLE ══ -->
  <section class="benevole-section" id="benevole">
    <div class="container benevole-inner">
      <div class="benevole-text">
        <span class="section-tag">Engagement</span>
        <h2>Devenir bénévole</h2>
        <p>
          Vous souhaitez donner de votre temps pour soutenir les femmes accompagnées
          par l'association ? Rejoignez notre équipe de bénévoles engagés et formés.
        </p>
        <ul class="benevole-liste">
          <li>✦ Accompagnement administratif et social</li>
          <li>✦ Animations d'ateliers et sorties</li>
          <li>✦ Soutien juridique ou psychologique</li>
          <li>✦ Communication et événements</li>
        </ul>
        <a href="benevole.php" class="btn btn-vert">Je veux m'engager</a>
      </div>
      <div class="benevole-photo">
        <div class="benevole-placeholder">
          <img src="images/histoire-photo.jpg" alt="benevoles">
        </div>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>

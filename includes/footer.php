<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <img src="images/logo.png" alt="Logo Village des Femmes" />
      <span class="logo-text">Village des Femmes</span>
      <p class="footer-bio">
        Association d'aide, d'écoute et d'accompagnement des femmes en situation
        de vulnérabilité, vers un avenir plus serein et libre.
      </p>
    </div>
    <div class="footer-col">
      <h4>Navigation</h4>
      <ul>
        <li><a href="index.php">Accueil</a></li>
        <li><a href="notre-mission.php">Notre Mission</a></li>
        <li><a href="aide-accompagnement.php">Aide &amp; Accompagnement</a></li>
        <li><a href="evenements.php">Événements</a></li>
        <li><a href="nous-soutenir.php">Nous Soutenir</a></li>
        <li><a href="contact.php">Contact</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Aide &amp; Ressources</h4>
      <ul>
        <li><a href="https://solidaritefemmes.org/appeler-le-3919/">3919 — Violences Femmes</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Nous Contacter</h4>
      <ul>
        <li><a href="contact.php">Formulaire de contact</a></li>
        <li><a href="mailto:villagesdesfemmes@gmail.com">villagesdesfemmes@gmail.com</a></li>
        <li><a href="#">01 XX XX XX XX</a></li>
        <li><a href="<?= htmlspecialchars(LIEN_FACEBOOK) ?>" target="_blank" rel="noopener">Facebook</a></li>
        <li><a href="<?= htmlspecialchars(LIEN_INSTAGRAM) ?>" target="_blank" rel="noopener">Instagram</a></li>
        <li><a href="<?= htmlspecialchars(LIEN_LINKEDIN) ?>" target="_blank" rel="noopener">LinkedIn</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    &copy; <?= date('Y') ?> Village des Femmes — Tous droits réservés
  </div>
</footer>

<div class="scroll-nav">
  <button class="scroll-btn scroll-top" id="scroll-top" aria-label="Remonter en haut de la page">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
  </button>
  <button class="scroll-btn scroll-bottom" id="scroll-bottom" aria-label="Aller en bas de la page">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
  </button>
</div>

<script src="main.js"></script>

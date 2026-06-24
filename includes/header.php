<?php
/* ============================================
   HEADER — inclus sur toutes les pages publiques
   Variable attendue (optionnelle) : $page_active = 'index' | 'notre-mission' | etc.
   ============================================ */
$page_active = $page_active ?? '';
function navActive(string $page, string $current): string {
    return $page === $current ? ' class="active"' : '';
}
?>
<header class="site-header">
  <div class="header-top">Village des Femmes — Association d'aide et d'accompagnement</div>
  <nav class="nav-wrapper">
    <a href="index.php" class="logo">
      <img src="images/logo.png" alt="Logo Village des Femmes" />
      <span class="logo-text">Village<br>des Femmes</span>
    </a>
    <button class="burger" id="burger" aria-label="Menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
    <ul class="main-nav" id="main-nav">
      <li><a href="index.php"<?= navActive('index', $page_active) ?>>Accueil</a></li>
      <li><a href="notre-mission.php"<?= navActive('notre-mission', $page_active) ?>>Notre Mission</a></li>
      <li><a href="aide-accompagnement.php"<?= navActive('aide-accompagnement', $page_active) ?>>Aide &amp; Accompagnement</a></li>
      <li><a href="evenements.php"<?= navActive('evenements', $page_active) ?>>Événements</a></li>
      <li class="nav-btn"><a href="nous-soutenir.php"<?= navActive('nous-soutenir', $page_active) ?>>Nous Soutenir</a></li>
      <li><a href="contact.php"<?= navActive('contact', $page_active) ?>>Contact</a></li>
      <?php if (estConnecte() && $_SESSION['role'] !== 'admin'): ?>
        <li><a href="mon-compte.php"<?= navActive('mon-compte', $page_active) ?>>Mon compte</a></li>
      <?php elseif (!estConnecte()): ?>
        <li><a href="connexion.php"<?= navActive('connexion', $page_active) ?>>Connexion</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>

<?php
// Variable attendue : $admin_page_active = 'dashboard' | 'evenements' | 'messages' | 'dons' | 'benevoles' | 'utilisateurs' | 'inscriptions'
$admin_page_active = $admin_page_active ?? '';
function adminNavActive(string $page, string $current): string {
    return $page === $current ? ' class="active"' : '';
}
?>
<div class="admin-topbar">
  <div class="admin-topbar-left">
    <img src="../images/logo.png" alt="Logo" />
    <span>Village des Femmes — Administration</span>
  </div>
  <div class="admin-topbar-right">
    <span>Bonjour, <?= htmlspecialchars($_SESSION['prenom'] ?? '') ?></span>
    <a href="../index.php" target="_blank">Voir le site ↗</a>
    <a href="logout.php">Déconnexion</a>
  </div>
</div>
<nav class="admin-nav">
  <a href="dashboard.php"<?= adminNavActive('dashboard', $admin_page_active) ?>>Tableau de bord</a>
  <a href="evenements.php"<?= adminNavActive('evenements', $admin_page_active) ?>>Événements</a>
  <a href="inscriptions.php"<?= adminNavActive('inscriptions', $admin_page_active) ?>>Participants</a>
  <a href="messages.php"<?= adminNavActive('messages', $admin_page_active) ?>>Messages</a>
  <a href="dons.php"<?= adminNavActive('dons', $admin_page_active) ?>>Dons</a>
  <a href="benevoles.php"<?= adminNavActive('benevoles', $admin_page_active) ?>>Bénévoles</a>
  <a href="utilisateurs.php"<?= adminNavActive('utilisateurs', $admin_page_active) ?>>Comptes utilisateurs</a>
</nav>

/* ============================================
   VILLAGE DES FEMMES — js commun à toutes les pages 
   ============================================ */

// ── Burger menu ──
const burger = document.getElementById('burger');
const mainNav = document.getElementById('main-nav');

if (burger && mainNav) {
  burger.addEventListener('click', () => {
    const isOpen = mainNav.classList.toggle('open');
    burger.setAttribute('aria-expanded', isOpen);
  });

  // Ferme le menu si on clique en dehors
  document.addEventListener('click', (e) => {
    if (!burger.contains(e.target) && !mainNav.contains(e.target)) {
      mainNav.classList.remove('open');
      burger.setAttribute('aria-expanded', 'false');
    }
  });
}

// ── Boutons montants (page Nous Soutenir) ──
const montantBtns = document.querySelectorAll('.montant-btn:not(.autre)');
const btnAutre = document.getElementById('btn-autre');
const autreWrap = document.getElementById('autre-wrap');
const montantLibre = document.getElementById('montant-libre');
const btnDon = document.getElementById('btn-don');

let montantSelectionne = 20; // défaut

montantBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    montantBtns.forEach(b => b.classList.remove('active'));
    if (btnAutre) btnAutre.classList.remove('active');
    btn.classList.add('active');
    montantSelectionne = parseInt(btn.dataset.montant);
    if (autreWrap) autreWrap.hidden = true;
    updateLienDon();
  });
});

if (btnAutre && autreWrap) {
  btnAutre.addEventListener('click', () => {
    montantBtns.forEach(b => b.classList.remove('active'));
    btnAutre.classList.add('active');
    autreWrap.hidden = false;
    montantLibre.focus();
  });

  montantLibre.addEventListener('input', () => {
    montantSelectionne = parseInt(montantLibre.value) || 0;
    updateLienDon();
  });
}

function updateLienDon() {
  // Adapte l'URL à ton système de paiement (HelloAsso, PayPal, Stripe…)
  if (btnDon && montantSelectionne > 0) {
    btnDon.href = `#don-${montantSelectionne}`;
  }
}

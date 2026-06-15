/* ============================================
   VILLAGE DES FEMMES — JavaScript commun
   ============================================ */

// ── Burger menu ──
const burger = document.getElementById('burger');
const mainNav = document.getElementById('main-nav');

if (burger && mainNav) {
  burger.addEventListener('click', () => {
    const isOpen = mainNav.classList.toggle('open');
    burger.setAttribute('aria-expanded', isOpen);
  });
  document.addEventListener('click', (e) => {
    if (!burger.contains(e.target) && !mainNav.contains(e.target)) {
      mainNav.classList.remove('open');
      burger.setAttribute('aria-expanded', 'false');
    }
  });
}

// ── Boutons montants (page Nous Soutenir) ──

const montantBtns = document.querySelectorAll('.montant-btn:not(.autre)');
const btnAutre    = document.getElementById('btn-autre');
const autreWrap   = document.getElementById('autre-wrap');
const montantLibre = document.getElementById('montant-libre');
const btnDon      = document.getElementById('btn-don');

let montantSelectionne = 20;

montantBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    montantBtns.forEach(b => b.classList.remove('active'));
    if (btnAutre) btnAutre.classList.remove('active');
    btn.classList.add('active');
    montantSelectionne = parseInt(btn.dataset.montant);
    if (autreWrap) autreWrap.hidden = true;
  });
});

if (btnAutre && autreWrap) {
  btnAutre.addEventListener('click', () => {
    montantBtns.forEach(b => b.classList.remove('active'));
    btnAutre.classList.add('active');
    autreWrap.hidden = false;
    if (montantLibre) montantLibre.focus();
  });
  if (montantLibre) {
    montantLibre.addEventListener('input', () => {
      montantSelectionne = parseInt(montantLibre.value) || 0;
    });
  }
}

// Lien don — remplace l'URL par celle de ta plateforme (HelloAsso, PayPal, Stripe…)
if (btnDon) {
  btnDon.addEventListener('click', (e) => {
    e.preventDefault();
    // Exemple : window.location.href = `https://www.helloasso.com/…?montant=${montantSelectionne}`;
    alert(`Redirection vers le paiement pour ${montantSelectionne}€\n(À configurer avec votre plateforme de don)`);
  });
}

// ── Formulaire contact (simulation) ──

const btnSubmit  = document.getElementById('btn-submit');
const formSuccess = document.getElementById('form-success');

if (btnSubmit && formSuccess) {
  btnSubmit.addEventListener('click', () => {
    const email = document.getElementById('email');
    const message = document.getElementById('message');
    const rgpd = document.getElementById('rgpd');

    if (!email || !email.value || !message || !message.value) {
      alert('Merci de remplir tous les champs obligatoires (*).');
      return;
    }
    if (rgpd && !rgpd.checked) {
      alert('Veuillez accepter la politique de confidentialité.');
      return;
    }
    // Ici tu brancheras ton backend ou service email (Formspree, EmailJS…)
    formSuccess.hidden = false;
    btnSubmit.disabled = true;
    btnSubmit.textContent = 'Message envoyé ✓';
    btnSubmit.style.background = '#4A8A75';
  });
}

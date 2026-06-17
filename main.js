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

// ── Formulaire contact (envoi réel via envoyer-message.php) ──
// Le formulaire envoie désormais les données directement au script PHP
// (voir l'attribut action="envoyer-message.php" dans contact.html).
// Ce script lit juste le résultat renvoyé dans l'URL après l'envoi,
// pour afficher le bon message de succès ou d'erreur.

const formSuccess = document.getElementById('form-success');
const formError   = document.getElementById('form-error');

if (formSuccess || formError) {
  const params = new URLSearchParams(window.location.search);
  const statut = params.get('statut');

  if (statut === 'succes' && formSuccess) {
    formSuccess.hidden = false;
    const btn = document.getElementById('btn-submit');
    if (btn) {
      btn.disabled = true;
      btn.textContent = 'Message envoyé ✓';
      btn.style.background = '#4A8A75';
    }
    // Nettoie l'URL pour éviter de réafficher le message si on rafraîchit la page
    window.history.replaceState({}, document.title, window.location.pathname);
  }

  if (statut === 'erreur' && formError) {
    const messageErreur = params.get('message');
    formError.hidden = false;
    formError.textContent = '⚠️ ' + (messageErreur || "Une erreur est survenue. Merci de réessayer.");
    window.history.replaceState({}, document.title, window.location.pathname);
  }
}

  // ── Molette de navigation (scroll haut / bas) ──
const scrollTopBtn    = document.getElementById('scroll-top');
const scrollBottomBtn = document.getElementById('scroll-bottom');

if (scrollTopBtn && scrollBottomBtn) {
  const toggleScrollBtns = () => {
    const scrollY = window.scrollY;
    const maxScroll = document.documentElement.scrollHeight - window.innerHeight;

    // Affiche les boutons seulement après un petit scroll
    if (scrollY > 200) {
      scrollTopBtn.classList.add('visible');
      scrollBottomBtn.classList.add('visible');
    } else {
      scrollTopBtn.classList.remove('visible');
      scrollBottomBtn.classList.remove('visible');
    }

    // Cache le bouton "bas" si on est déjà en bas de page
    if (scrollY >= maxScroll - 10) {
      scrollBottomBtn.classList.add('at-bottom');
    } else {
      scrollBottomBtn.classList.remove('at-bottom');
    }
  };

  window.addEventListener('scroll', toggleScrollBtns);
  toggleScrollBtns();

  scrollTopBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
  scrollBottomBtn.addEventListener('click', () => {
    window.scrollTo({ top: document.documentElement.scrollHeight, behavior: 'smooth' });
  });
}

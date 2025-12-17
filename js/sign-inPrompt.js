/**
 * Book Template Modal Handler
 * Manages sign-in modal interactions and form validation
 */

function openSigninModal(customMessage) {
  const overlay = document.getElementById('signin-overlay');
  const msgElem = document.getElementById('signin-overlay-message');
  if (customMessage) msgElem.textContent = customMessage;
  overlay.classList.add('active');
  overlay.setAttribute('aria-hidden', 'false');
  // Set focus for keyboard users
  document.getElementById('signin-yes').focus();
}

function closeSigninModal() {
  const overlay = document.getElementById('signin-overlay');
  overlay.classList.remove('active');
  overlay.setAttribute('aria-hidden', 'true');
}

(function() {
  const overlay = document.getElementById('signin-overlay');
  const yesBtn = document.getElementById('signin-yes');
  const noBtn = document.getElementById('signin-no');

  // Intercept favourite form submits when user is not logged in
  document.querySelectorAll('.fav-form').forEach(form => {
    form.addEventListener('submit', (e) => {
      if (!window.isLoggedIn) {
        e.preventDefault();
        openSigninModal(window.signinModalMessage || 'You must be signed in to add favourites.');
      }
    });
  });

  // Intercept comment form submits when user is not logged in
  document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', (e) => {
      if (!window.isLoggedIn) {
        e.preventDefault();
        openSigninModal(window.signinModalMessage || 'You must be signed in to post a comment.');
      }
    });
  });

  // OK -> go to sign-in with redirect back to the same page
  yesBtn.addEventListener('click', () => {
    const redirectUrl = encodeURIComponent(window.location.href);
    window.location.href = '/PanayTales/sign-in.php?redirect=' + redirectUrl;
  });

  // No thanks -> close overlay
  noBtn.addEventListener('click', () => closeSigninModal());

  // Clicking backdrop closes overlay
  overlay.addEventListener('click', (ev) => {
    if (ev.target === overlay) closeSigninModal();
  });

  // Esc closes overlay
  document.addEventListener('keydown', (ev) => {
    if (ev.key === 'Escape') closeSigninModal();
  });

  // If server set the flag (post_error), show modal automatically
  if (window.showSigninModalOnLoad) {
    openSigninModal(window.signinModalMessage);
  }
})();

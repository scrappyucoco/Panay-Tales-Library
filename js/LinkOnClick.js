document.querySelectorAll('.card').forEach(card => {
  card.addEventListener('click', () => {
    if (card.classList.contains('active')) {
      const link = card.getAttribute('data-link');
      if (link) window.location.href = link;
    } else {
      centerOnCard(card); // from carousel.js
    }
  });
});

function setActiveMiddle() {
  const children = Array.from(carousel.children);
  children.forEach((card, i) => card.classList.toggle('active', i === 2));

  // Find the active card
  const activeCard = children[2];
  if (activeCard) {
    const bgImage = activeCard.getAttribute('data-bg');
    const page = document.querySelector('.about-page1');
    page.style.backgroundImage = `url(${bgImage})`;
    page.style.backgroundSize = 'cover';
    page.style.backgroundPosition = 'center';
    page.style.transition = 'background-image 0.5s ease';
  }
}
function setActiveMiddle() {
  const children = Array.from(carousel.children);
  const middleIndex = Math.floor(children.length / 2); // dynamic middle

  children.forEach((card, i) => card.classList.toggle('active', i === middleIndex));

  const activeCard = children[middleIndex];
  if (activeCard) {
    const bgImage = activeCard.getAttribute('data-bg');
    const page = document.querySelector('.about-page1');
    if (bgImage) {
      page.style.backgroundImage = `url(${bgImage})`;
      page.style.backgroundSize = 'cover';
      page.style.backgroundPosition = 'center';
      page.style.transition = 'background-image 0.5s ease';
    }
  }
}

// Run after DOM is ready
window.addEventListener("DOMContentLoaded", () => {
  setActiveMiddle();
});
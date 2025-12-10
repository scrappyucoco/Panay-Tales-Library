const carousel = document.querySelector('.carousel');

function getCardWidth() {
  return carousel.parentElement.offsetWidth / 5;
}

function setActiveMiddle() {
  const children = Array.from(carousel.children);
  children.forEach((card, i) => card.classList.toggle('active', i === 2));
}

// Smooth step left
function stepLeft() {
  return new Promise(resolve => {
    const w = getCardWidth();
    const first = carousel.firstElementChild;

    first.classList.add('fade-out'); // leaving animation

    carousel.style.transition = 'transform 0.3s ease-in-out';
    carousel.style.transform = `translateX(-${w}px)`;

    const onEnd = () => {
      carousel.removeEventListener('transitionend', onEnd);

      // move first card to end
      carousel.appendChild(first);

      // cleanup exit
      first.classList.remove('fade-out');

      // trigger entrance animation
      first.classList.add('fade-in');
      setTimeout(() => first.classList.remove('fade-in'), 300);

      // reset transform
      carousel.style.transition = 'none';
      carousel.style.transform = 'translateX(0)';
      void carousel.offsetHeight;
      carousel.style.transition = 'transform 0.3s ease-in-out';

      setActiveMiddle();
      resolve();
    };
    carousel.addEventListener('transitionend', onEnd, { once: true });
  });
}

function stepRight() {
  return new Promise(resolve => {
    const w = getCardWidth();
    const last = carousel.lastElementChild;

    last.classList.add('fade-out'); // leaving animation

    carousel.style.transition = 'transform 0.3s ease-in-out';
    carousel.style.transform = `translateX(${w}px)`;

    const onEnd = () => {
      carousel.removeEventListener('transitionend', onEnd);

      // move last card to front
      carousel.insertBefore(last, carousel.firstElementChild);

      // cleanup exit
      last.classList.remove('fade-out');

      // trigger entrance animation
      last.classList.add('fade-in');
      setTimeout(() => last.classList.remove('fade-in'), 300);

      // reset transform
      carousel.style.transition = 'none';
      carousel.style.transform = 'translateX(0)';
      void carousel.offsetHeight;
      carousel.style.transition = 'transform 0.3s ease-in-out';

      setActiveMiddle();
      resolve();
    };
    carousel.addEventListener('transitionend', onEnd, { once: true });
  });
}

// Center clicked card
function centerOnCard(target) {
  const children = Array.from(carousel.children);
  let idx = children.indexOf(target);
  let delta = idx - 2;

  const steps = [];
  if (delta > 0) {
    for (let i = 0; i < delta; i++) steps.push(stepLeft);
  } else if (delta < 0) {
    for (let i = 0; i < -delta; i++) steps.push(stepRight);
  }

  let chain = Promise.resolve();
  steps.forEach(fn => { chain = chain.then(fn); });
  return chain;
}

// Attach click handlers
Array.from(carousel.children).forEach(card => {
  card.addEventListener('click', () => centerOnCard(card));
});

// Initial active state
setActiveMiddle();

// Handle resize
window.addEventListener('resize', setActiveMiddle);
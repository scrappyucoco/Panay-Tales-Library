// Attach click listeners to all book cards
document.querySelectorAll('.book').forEach(book => {
  book.addEventListener('click', (e) => {
    e.preventDefault(); // prevent navigation if you kept <a> tags

    // Remove active class from all books
    document.querySelectorAll('.book').forEach(b => b.classList.remove('active'));
    // Mark this one active
    book.classList.add('active');

    // Get data attributes (cover + title)
    const cover = book.dataset.cover;
    const title = book.dataset.title;

    // Update right-side preview
    const display = document.getElementById('book-display');
    display.innerHTML = `
      <div class="preview">
        <img src="${cover}" alt="${title}">
        <h2>${title}</h2>
      </div>
    `;
  });
});

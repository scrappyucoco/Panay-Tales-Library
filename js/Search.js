const searchInput = document.querySelector('.searchBar input');
const searchResultsContainer = document.getElementById('search-results');
const genresSection = document.getElementById('genres-section');

searchInput.addEventListener('input', function() {
  const query = this.value.toLowerCase().trim();
  searchResultsContainer.innerHTML = '';

  if (query === '') {
    genresSection.style.display = 'block'; // show genres again
     searchResultsContainer.style.display = 'none'; // <-- hide results
    return
  }

  genresSection.style.display = 'none'; // hide genres while searching
  searchResultsContainer.style.display = 'block';

  const filtered = books.filter(book =>
    book.title.toLowerCase().includes(query) ||
    book.genres.some(g => g.toLowerCase().includes(query))
  );

  if (filtered.length === 0) {
    searchResultsContainer.innerHTML = '<h2>No tales found…</h2>';
    return;
  }

  const resultsHTML = `
    <div class="searchResults">Search Results</div>
    <div class="boook">
      ${filtered.map(book => `
        <div class="book">
          <a href="${book.link}">
            <img src="${book.cover_image}" alt="${book.title}">
            <div class="book-title">${highlightMatch(book.title, query)}</div>
          </a>
        </div>
      `).join('')}
    </div>
  `;
  searchResultsContainer.innerHTML = resultsHTML;
});

// helper to highlight matches
function highlightMatch(text, query) {
  const regex = new RegExp(`(${query})`, 'gi');
  return text.replace(regex, '<mark>$1</mark>');
}

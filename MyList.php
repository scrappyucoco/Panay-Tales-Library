<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Favourites</title>
  <link rel="icon" type="image/png" href="/panay-tales-library/images/book-button.png">
  <link rel="stylesheet" href="css/MyList.css">
  <link rel="stylesheet" href="css/navbar.css">
  <link rel="stylesheet" href="css/Books-navbar3.css">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

  <?php include 'navbar.php'; ?>
  <?php include 'booksData.php'; ?>

  <?php
    // show user's favourites
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    require_once __DIR__ . '/db.php';
    $user_favourites = [];
    if (!empty($_SESSION['user_id'])) {
      $uid = (int) $_SESSION['user_id'];
// READ: Take the current user's favourites.
$favStmt = $connection->prepare(
  "SELECT f.book_id, b.title, b.cover_image, b.link FROM favourites f JOIN books b ON f.book_id = b.books_id WHERE f.user_id = ? ORDER BY f.created_at DESC"
);
      if ($favStmt) {
        $favStmt->bind_param('i', $uid);
        $favStmt->execute();
        $favRes = $favStmt->get_result();
        while ($fr = $favRes->fetch_assoc()) {
          $user_favourites[] = $fr;
        }
        $favStmt->close();
      }
    }
  ?>

       <script>
    const books = <?php echo json_encode(array_values($books)); ?>;
  </script>

  <main>
    <div class="searchBar">
      
      <input type="text" placeholder="Search for tales">
      <button type="submit">Search</button>
    </div>

   <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

      <div class="navbar3">
        <div class="nav3-links">
           <a href="Books.php" class="Featured">Featured</a>
            <a href="MoreTales.php" class="More-Tales">More Tales</a>
             <a href="MyList.php" class="My-List">My List</a>
        </div>
      </div>


    <div class="searchContainer">
      <div id="search-results"></div>

      <?php if (!empty($user_favourites)): ?>
        <section class="my-favourites">
          <h2>Your Favourites</h2>
          <div class="favs-row">
            <?php foreach ($user_favourites as $f): ?>
              <a class="fav-item" href="<?php echo htmlspecialchars($f['link']); ?>">
                <div class="fav-cover" style="background-image: url('<?php echo htmlspecialchars($f['cover_image']); ?>')"></div>
                <div class="fav-title"><?php echo htmlspecialchars($f['title']); ?></div>
              </a>
            <?php endforeach; ?>
          </div>
        </section>
      <?php else: ?>
        <section class="my-favourites">
          <h2>Your Favourites</h2>
          <p>No favourites yet.</p>
        </section>
      <?php endif; ?>

    </div>  

</main>

</body>

<script src="js/Search.js"></script>
</html>

<style>

.my-favourites {
  width: 80vw;
  margin: 28px auto;
  background: transparent;
  border-radius: 10px;
  padding: 28px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  align-items: center;
  justify-content: center;
  text-align: center;
}
.my-favourites h2 {
  margin: 0;
  font-size: 2rem;
  font-weight: 700;
  color: #333;
  text-align: center;
}

/* Favourites */
.favs-row {
  display: flex;
  gap: 0.75rem;
  margin-top: 1rem;
  width: 100%;
  overflow: hidden;
  align-items: center;
  justify-content: center;
  align-content: center;
  flex-wrap: wrap;
  flex-direction: row;
}

/* Favourite item */
.fav-item {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  gap: 0.4rem;
  padding: 1.2rem;
  padding-bottom: 0;
  background-color: white;
  border-radius: 15px;
  text-decoration: none;
  color: inherit;
  width: clamp(120px, 18vw, 180px);
  min-width: 120px;
  position: relative;
  cursor: pointer;
  transition: transform 0.25s ease;
  transform-origin: center;
}

.fav-item:hover {
  transform: translateY(-6px);
}

/* Cover image */
.fav-cover {
  width: 100%;
  height: 288px;
  background-size: cover;
  background-position: center;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  transition: transform 0.35s ease, box-shadow 0.35s ease;
  transform-origin: center;
  overflow: hidden;
  position: relative;
}

.fav-cover::after {
  content: '❤';
  position: absolute;
  top: 0;
  right: 0.4rem;
  font-size: 1.4rem;
  color: #ffffffff;
  z-index: 10;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

/* Book Title */
.fav-title {
  left: 50%;
  bottom: 10px;
  transform: translateX(-50%);
  width: 75%;
  background: rgb(0 0 0 / 45%);
  color: #fff;
  text-shadow: 2px 2px 4px black;
  font-size: 1.05rem;
  font-weight: 700;
  text-align: center;
  padding: 6px 8px;
  border-radius: 6px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  transition: transform 0.25s ease, background 0.2s ease, font-size 0.25s ease;
  pointer-events: none;
  position: absolute;
}

.fav-item:hover .fav-cover {
  transform: scale(1.12);
  box-shadow: 0 8px 22px rgba(0,0,0,0.22);
}
.fav-item:hover .fav-title {
  transform: translateX(-50%) scale(1.06);
  font-size: 1.12rem;
  background: rgba(0,0,0,0.62);
}

.fav-item:focus-visible {
  outline: 3px solid rgba(221,63,0,0.8);
  outline-offset: 2px;
}
</style>
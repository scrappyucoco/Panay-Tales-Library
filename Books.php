<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Featured</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Intel+One+Mono:wght@300;400;600;700&display=swap" rel="stylesheet">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="css/Books.css">
  <link rel="stylesheet" href="css/navbar.css">
  <link rel="stylesheet" href="css/BooksPage2.css">
  <link rel="stylesheet" href="css/Books-navbar.css">
</head>
<body>
  <!-- Main Navbar -->
  <?php include 'navbar.php'; ?>

  <div class="about-container">

    <div class="about-page1">

      <!-- Carousel -->
      <?php include 'booksData.php'; ?>

<div class="carousel-container" data-logged-in="<?php echo $isLoggedIn ? 'true' : 'false'; ?>">
  <div class="carousel">
    <?php foreach (array_slice($books, 0, 5) as $book): ?>
      <div class="card" 
           data-bg="<?php echo $book['cover_image']; ?>" 
           data-link="<?php echo $book['link']; ?>" 
           data-title="<?php echo $book['title']; ?>">
        <img src="<?php echo $book['cover_image']; ?>" alt="<?php echo $book['title']; ?>">
        <div class="card-title"><?php echo $book['title']; ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

      <!-- Secondary Navbar -->
       <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

      <div class="navbar2">
        <div class="nav2-links">
           <a href="Books.php" class="Featured">Featured</a>
            <a href="MoreTales.php" class="More-Tales">More Tales</a>
             <a href="MyList.php" class="My-List">My List</a>
        </div>
      </div>

      <div class="bottom-bar"></div>

    </div>
  </div>

  <!-- Scripts -->
  <script src="js/carousel.js"></script>
  <script src="js/Responsive.js"></script>
  <script src="js/LinkOnClick.js"></script>
</body>
</html>

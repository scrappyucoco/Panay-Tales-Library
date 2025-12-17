<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>More Tales</title>
  
  <link rel="icon" type="image/png" href="/panay-tales-library/images/book-button.png">
  <link rel="stylesheet" href="css/MoreTales.css">
  <link rel="stylesheet" href="css/navbar.css">
  <link rel="stylesheet" href="css/Books-navbar2.css">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
  <script src="transition.js"defer></script>
  </head>

<body>

  <?php include 'navbar.php'; ?>
      <?php include 'booksData.php'; ?>

       <script>
    const books = <?php echo json_encode(array_values($books)); ?>;
  </script>

  <main>

    <div class="left-side">

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

  

<div id="genres-section" class="content">
  <?php 
    $genres = ["Legend", "Adventure", "Mystery"];
    foreach ($genres as $genre): 
      $filtered = array_filter($books, fn($book) => in_array($genre, $book['genres']));
  ?>
  <div class="legend"><?php echo $genre; ?></div>
    <div class="genre">
      
      <div class="boook">
        <?php foreach ($filtered as $book): ?>
          <div class="book">
            <a href="<?php echo $book['link']; ?>">
              <img src="<?php echo $book['cover_image']; ?>" alt="<?php echo $book['title']; ?>">
              <div class="book-title"><?php echo $book['title']; ?></div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

</div>



    </div>
    
  </main>

</body>

<script src="js/Search.js"></script>
</html>
<?php
session_start();
require_once __DIR__ . '/../db.php';

// Use a flag & message to display sign-in modal overlay when server rejects because user is not signed in
$post_error = '';
$show_signin_modal = false;
$signin_modal_message = '';

// CREATE: Ensure the 'favourites' table exists
// This table stores user favourites; the unique constraint ensures a user can't favourite the same book twice.// Ensure favourites table exists (user-side favourites)
$createFavSQL = "CREATE TABLE IF NOT EXISTS favourites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  book_id INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY ux_user_book (user_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$connection->query($createFavSQL);

// Determine an identifier for this book. Prefer numeric id if available.
$book_identifier = (int) ($book['id'] ?? 0);

// Check whether comments table has a user_id column 
$has_user_id = false;
$colCheck = $connection->query("SHOW COLUMNS FROM comments LIKE 'user_id'");
if ($colCheck && $colCheck->num_rows > 0) {
  $has_user_id = true;
}

// Handle favourite submission 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['favourite'])) {
  $book_id_post = (int) ($_POST['book_id'] ?? $book_identifier);
  if (empty($_SESSION['user_id'])) {
    $post_error = 'You must be signed in to add favourites.';
    $show_signin_modal = true;
    $signin_modal_message = $post_error;
  } elseif ($book_id_post <= 0) {
    $post_error = 'Invalid book ID.';
  } else {
    $user_id = (int) $_SESSION['user_id'];

    // READ: Check if the logged-in user has already favourited this book
    // Returns 1 row if there is an existing favourite
    $chk = $connection->prepare("SELECT id FROM favourites WHERE user_id = ? AND book_id = ? LIMIT 1");
    if ($chk) {
      $chk->bind_param('ii', $user_id, $book_id_post);
      $chk->execute();
      $chkRes = $chk->get_result();
      if ($chkRes && $chkRes->num_rows > 0) {

        // DELETE: Remove an existing favourite (toggle off)
        // Uses a prepared statement to delete by user_id & book_id
        $del = $connection->prepare("DELETE FROM favourites WHERE user_id = ? AND book_id = ?");
        if ($del) {
          $del->bind_param('ii', $user_id, $book_id_post);
          $del->execute();
          $del->close();
        }
      } else {
        // CREATE: Insert a new favourite (toggle on)
        // Uses NOW() to set created_at; prepared statement protects from SQL injection
        $ins = $connection->prepare("INSERT INTO favourites (user_id, book_id, created_at) VALUES (?, ?, NOW())");
        if ($ins) {
          $ins->bind_param('ii', $user_id, $book_id_post);
          $ins->execute();
          $ins->close();
        }
      }
      $chk->close();
    }
  }
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
  $comment_text = trim($_POST['comment']);
  $book_id_post = (int) ($_POST['book_id'] ?? $book_identifier);

  if ($comment_text === '') {
    $post_error = '';
  } elseif (empty($_SESSION['user_id'])) {
    $post_error = 'You must be signed in to post a comment.';
    $show_signin_modal = true;
    $signin_modal_message = $post_error;
  } else {
    $user_id = (int) ($_SESSION['user_id'] ?? 0);
        if ($has_user_id) {
          // CREATE: Insert a new comment with user_id
            $stmt = $connection->prepare("INSERT INTO comments (book_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
            if ($stmt) {
                $stmt->bind_param('iis', $book_id_post, $user_id, $comment_text);
                if (!$stmt->execute()) {
                    $post_error = 'Database error: ' . htmlspecialchars($stmt->error);
                }
                $stmt->close();
            } else {
                $post_error = 'Database error: ' . htmlspecialchars($connection->error);
            }
        } else {
            // CREATE: Insert a new comment without user_id (older schema fallback)
            $stmt = $connection->prepare("INSERT INTO comments (book_id, comment, created_at) VALUES (?, ?, NOW())");
            if ($stmt) {
                $stmt->bind_param('is', $book_id_post, $comment_text);
                if (!$stmt->execute()) {
                    $post_error = 'Database error: ' . htmlspecialchars($stmt->error);
                }
                $stmt->close();
            } else {
                $post_error = 'Database error: ' . htmlspecialchars($connection->error);
            }
        }
  }
  // After handling POST, reload comments below (no redirect to keep simple)
}

// Fetch comments for this book (latest first). Left-join users to get commenter email.
$comments = [];
if ($has_user_id) {
  // READ: Fetch comments for this book (latest first). 
  // If user_id is present in the comments table, LEFT JOIN to users to obtain the user's email.
  $stmt = $connection->prepare(
    "SELECT c.comment, c.created_at, u.email FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.book_id = ? ORDER BY c.created_at DESC"
  );
  if ($stmt) {
    $stmt->bind_param('s', $book_identifier);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
      $comments[] = $r;
    }
    $stmt->close();
  }
} else {
  // READ: Fallback read for comments if user_id column is not present (simpler table).
  $stmt = $connection->prepare(
    "SELECT comment, created_at FROM comments WHERE book_id = ? ORDER BY created_at DESC"
  );
  if ($stmt) {
    $stmt->bind_param('s', $book_identifier);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
      $comments[] = $r;
    }
    $stmt->close();
  }
}

// After handling POSTs above, recompute whether this specific book is favourited
$is_favourite = false;
if (!empty($_SESSION['user_id']) && $book_identifier > 0) {
  // READ: Determine whether the current book is favourited by the current user.
  // Returns a single row if favourite exists
  $chk = $connection->prepare("SELECT 1 FROM favourites WHERE user_id = ? AND book_id = ? LIMIT 1");
  if ($chk) {
    $uid = (int) $_SESSION['user_id'];
    $chk->bind_param('ii', $uid, $book_identifier);
    $chk->execute();
    $chkRes = $chk->get_result();
    if ($chkRes && $chkRes->num_rows > 0) {
      $is_favourite = true;
    }
    $chk->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($book['title']); ?></title>

  <link rel="stylesheet" href="../css/bookTemplateStyle.css">
  <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>

  <?php include __DIR__ . '/../navbar.php'; ?>

  <div class="book-hero" style="--bg-image: url('../<?php echo $book['cover_image']; ?>');">

    <a href="/panay-tales-library/MoreTales.php" class="back-button"><h4>⟪</h1></a>
    
    <!-- Gradient overlay layer -->
    <div class="gradient-layer"></div>
    
    <!-- Content layer split into two parts -->
    <div class="content-layer">
      <div class="about-book">
        <div class="bookcover">
          <div class="book-img-cover" style="background-image: url('../<?php echo $book['cover_image']; ?>');"></div>
          <div class="book-meta">
            <h1 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h1>
            <?php if (!empty($book['author'])): ?>
              <p class="book-author"><?php echo htmlspecialchars($book['author']); ?></p>
            <?php endif; ?>
            <?php foreach ($book['genres'] as $genre): ?>
              <span class="genre-badge"><?php echo htmlspecialchars($genre); ?></span>
            <?php endforeach; ?>
            <!-- favourite -->
            <form method="POST" action="" class="fav-form">
              <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book_identifier); ?>">
              <button type="submit" name="favourite" class="fav-btn <?php echo $is_favourite ? 'active' : ''; ?>" title="Toggle favourite">❤</button>
            </form>
            
          </div>
        </div>
          
        <div class="book-comments">
          <?php if (!empty($post_error)): ?>
            
          <?php endif; ?>

          <form method="POST" action="" class="comment-form">
            <textarea id="comments" name="comment" placeholder="Comment"></textarea>
            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book_identifier); ?>">
            <button id="combtn" type="submit">Submit</button>
          </form>

        </div>

        <!-- Comments display section -->
        <div class="comSec">
          <?php if (empty($comments)): ?>
            <p>No comments yet. Be the first to comment.</p>
          <?php else: ?>
            <?php foreach ($comments as $c): ?>
              <div class="single-comment">
                <h4 class="comment-user"><?php echo htmlspecialchars($c['email'] ?? 'User'); ?></h4>
                <p class="comment-text"><?php echo nl2br(htmlspecialchars($c['comment'])); ?></p>
                <small class="comment-time"><?php echo htmlspecialchars($c['created_at']); ?></small>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <!-- User favourites row moved to MyList.php -->
      </div>

      <div class="book-content">
        <!-- Fixed 50% viewport width section -->
         <?php echo $content; ?>
      </div>
    </div>

  </div>


<!-- No client-side comment injection: comments are posted to server and loaded from DB -->

<!-- Sign-in overlay (hidden by default) -->
<div id="signin-overlay" class="signin-overlay" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="signin-overlay-card">
    <p id="signin-overlay-message" class="signin-overlay-message"><?php echo htmlspecialchars($signin_modal_message ?: 'You must be signed in to continue.'); ?></p>
    <div class="signin-overlay-actions">
      <button id="signin-yes" class="overlay-btn overlay-btn-primary">OK</button>
      <button id="signin-no" class="overlay-btn overlay-btn-secondary">No thanks</button>
    </div>
  </div>
</div>

<script>
  // Export server-side flags & messages to JS
  const isLoggedIn = <?php echo !empty($_SESSION['user_id']) ? 'true' : 'false'; ?>;
  const showSigninModalOnLoad = <?php echo ($show_signin_modal ? 'true' : 'false'); ?>;
  const signinModalMessage = <?php echo json_encode($signin_modal_message ?: 'You must be signed in to proceed'); ?>;

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
        if (!isLoggedIn) {
          e.preventDefault();
          openSigninModal(signinModalMessage || 'You must be signed in to add favourites.');
        }
      });
    });

    // Intercept comment form submits when user is not logged in
    document.querySelectorAll('.comment-form').forEach(form => {
      form.addEventListener('submit', (e) => {
        if (!isLoggedIn) {
          e.preventDefault();
          openSigninModal(signinModalMessage || 'You must be signed in to post a comment.');
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
    if (showSigninModalOnLoad) {
      openSigninModal(signinModalMessage);
    }
  })();
</script>

</body>
</html>
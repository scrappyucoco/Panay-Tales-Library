<?php
session_start();
require_once __DIR__ . '/../db.php';

$post_error = '';
$show_signin_modal = false;
$signin_modal_message = '';
$book_identifier = (int) ($book['id'] ?? 0);

// Check whether comments table has a user_id column 
$has_user_id = false;
$colCheck = $connection->query("SHOW COLUMNS FROM comments LIKE 'user_id'");
if ($colCheck && $colCheck->num_rows > 0) {
  $has_user_id = true;
}

// ==================== COMMENTS ====================

// CREATE: Handle comment submission (new comment)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && !isset($_POST['update_comment'])) {
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
}

// UPDATE: Handle comment update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_comment'])) {
  $comment_id = (int) ($_POST['comment_id'] ?? 0);
  $comment_text = trim($_POST['comment'] ?? '');
  $book_id_post = (int) ($_POST['book_id'] ?? $book_identifier);

  if (empty($_SESSION['user_id'])) {
    $post_error = 'You must be signed in to update a comment.';
    $show_signin_modal = true;
    $signin_modal_message = $post_error;
  } elseif ($comment_id <= 0) {
    $post_error = 'Invalid comment ID.';
  } elseif ($comment_text === '') {
    $post_error = 'Comment cannot be empty.';
  } else {
    $user_id = (int) $_SESSION['user_id'];
    
    $verify = $connection->prepare("SELECT id FROM comments WHERE id = ? AND user_id = ?");
    if ($verify) {
      $verify->bind_param('ii', $comment_id, $user_id);
      $verify->execute();
      $verifyRes = $verify->get_result();
      if ($verifyRes && $verifyRes->num_rows > 0) {
        $update = $connection->prepare("UPDATE comments SET comment = ? WHERE id = ? AND user_id = ?");
        if ($update) {
          $update->bind_param('sii', $comment_text, $comment_id, $user_id);
          if (!$update->execute()) {
            $post_error = 'Database error: ' . htmlspecialchars($update->error);
          }
          $update->close();
        } else {
          $post_error = 'Database error: ' . htmlspecialchars($connection->error);
        }
      } else {
        $post_error = 'You can only update your own comments.';
      }
      $verify->close();
    }
  }
}

// DELETE: Handle comment deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
  $comment_id = (int) ($_POST['comment_id'] ?? 0);
  $book_id_post = (int) ($_POST['book_id'] ?? $book_identifier);

  if (empty($_SESSION['user_id'])) {
    $post_error = 'You must be signed in to delete a comment.';
    $show_signin_modal = true;
    $signin_modal_message = $post_error;
  } elseif ($comment_id <= 0) {
    $post_error = 'Invalid comment ID.';
  } else {
    $user_id = (int) $_SESSION['user_id'];
    
    $verify = $connection->prepare("SELECT id FROM comments WHERE id = ? AND user_id = ?");
    if ($verify) {
      $verify->bind_param('ii', $comment_id, $user_id);
      $verify->execute();
      $verifyRes = $verify->get_result();
      if ($verifyRes && $verifyRes->num_rows > 0) {
        $delete = $connection->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
        if ($delete) {
          $delete->bind_param('ii', $comment_id, $user_id);
          if (!$delete->execute()) {
            $post_error = 'Database error: ' . htmlspecialchars($delete->error);
          }
          $delete->close();
        } else {
          $post_error = 'Database error: ' . htmlspecialchars($connection->error);
        }
      } else {
        $post_error = 'You can only delete your own comments.';
      }
      $verify->close();
    }
  }
}

// READ: Fetch all comments for this book (latest first)
$comments = [];
if ($has_user_id) {
  $stmt = $connection->prepare(
    "SELECT c.id, c.comment, c.created_at, c.user_id, u.email FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.book_id = ? ORDER BY c.created_at DESC"
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
  $stmt = $connection->prepare(
    "SELECT id, comment, created_at FROM comments WHERE book_id = ? ORDER BY created_at DESC"
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

// ==================== FAVOURITES ====================

// CREATE/DELETE: Handle favourite toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['favourite'])) {
  $book_id_post = (int) ($_POST['book_id'] ?? $book_identifier);
  if (empty($_SESSION['user_id'])) {
    $post_error = 'You must be signed in to modify favourites.';
    $show_signin_modal = true;
    $signin_modal_message = $post_error;
  } elseif ($book_id_post <= 0) {
    $post_error = 'Invalid book ID.';
  } else {
    $user_id = (int) $_SESSION['user_id'];
    
    $chk = $connection->prepare("SELECT id FROM favourites WHERE user_id = ? AND book_id = ? LIMIT 1");
    if ($chk) {
      $chk->bind_param('ii', $user_id, $book_id_post);
      $chk->execute();
      $chkRes = $chk->get_result();
      if ($chkRes && $chkRes->num_rows > 0) {
        // if already favourited, so REMOVE
        $chk->close();
        $del = $connection->prepare("DELETE FROM favourites WHERE user_id = ? AND book_id = ?");
        if ($del) {
          $del->bind_param('ii', $user_id, $book_id_post);
          $del->execute();
          $del->close();
        }
      } else {
        // if not yet favourited, so ADD
        $chk->close();
        $ins = $connection->prepare("INSERT INTO favourites (user_id, book_id, created_at) VALUES (?, ?, NOW())");
        if ($ins) {
          $ins->bind_param('ii', $user_id, $book_id_post);
          $ins->execute();
          $ins->close();
        }
      }
    }
  }
}

// READ: Check if the current user has favourited this book
$is_favourite = false;
if (!empty($_SESSION['user_id']) && $book_identifier > 0) {
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

  <link rel="icon" type="image/png" href="../<?php echo $book['cover_image']; ?>">
  <link rel="stylesheet" href="../css/bookTemplateStyle.css">
  <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>

  <?php include __DIR__ . '/../navbar.php'; ?>

  <div class="book-hero" style="--bg-image: url('../<?php echo $book['cover_image']; ?>');">
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

            <div class="share-wrapper" aria-label="Share this book">
              <button type="button" class="share-toggle"><h1>⮫</h1> Share</button>
              <div class="share-menu" hidden>
                <button type="button" class="share-option" data-action="copy">🔗 Copy link</button>
                <button type="button" class="share-option" data-action="email">📧 Send via email</button>
                <button type="button" class="share-option" data-action="facebook">🌐 Share to Facebook</button>
              </div>
            </div>

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
            <textarea id="comments" name="comment" placeholder="Write a comment..." rows="1" maxlength="500"></textarea>
            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book_identifier); ?>">
            <button id="combtn" type="submit">Post</button>
          </form>

        </div>

        <!-- Comments display section -->
        <div class="comSec">
          <?php if (empty($comments)): ?>
            <p id="no-comments-message">No comments yet. Be the first to comment.</p>
          <?php else: ?>
            <?php foreach ($comments as $c): ?>
              <?php 
                $is_own_comment = !empty($_SESSION['user_id']) && !empty($c['user_id']) && (int)$_SESSION['user_id'] === (int)$c['user_id'];
              ?>
              <div class="single-comment" data-comment-id="<?php echo htmlspecialchars($c['id']); ?>">
                <div class="comment-header">
                  <h4 class="comment-user"><?php echo htmlspecialchars($c['email'] ?? 'User'); ?></h4>
                  <?php if ($is_own_comment): ?>
                    <div class="comment-actions">
                      <button type="button" class="comment-edit-btn" title="Edit comment">✏️</button>
                      <button type="button" class="comment-delete-btn" title="Delete comment">🗑️</button>
                    </div>
                  <?php endif; ?>
                </div>
                <p class="comment-text"><?php echo nl2br(htmlspecialchars($c['comment'])); ?></p>
                <small class="comment-time"><?php echo htmlspecialchars($c['created_at']); ?></small>
                
                <?php if ($is_own_comment): ?>
                  <form class="comment-edit-form" method="POST" action="" hidden>
                    <textarea name="comment" class="edit-textarea" maxlength="500"><?php echo htmlspecialchars($c['comment']); ?></textarea>
                    <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($c['id']); ?>">
                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book_identifier); ?>">
                    <div class="edit-actions">
                      <button type="submit" name="update_comment" class="edit-save-btn">Save</button>
                      <button type="button" class="edit-cancel-btn">Cancel</button>
                    </div>
                  </form>
                  <form class="comment-delete-form" method="POST" action="" hidden>
                    <p>Are you sure you want to delete this comment?</p>
                    <div class="delete-actions">
                      <button type="submit" name="delete_comment" class="delete-confirm-btn">Yes, delete</button>
                      <button type="button" class="delete-cancel-btn">No, keep it</button>
                    </div>
                    <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($c['id']); ?>">
                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book_identifier); ?>">
                  </form>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <div class="book-content">
        <a href="/panay-tales-library/MoreTales.php" class="back-button" aria-label="Back to More Tales">
          <span aria-hidden="true">⮪</span>     
        </a>
         <?php echo $content; ?>
      </div>
    </div>

  </div>

<!-- Error prompt for sign-in-->
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
  window.isLoggedIn = <?php echo !empty($_SESSION['user_id']) ? 'true' : 'false'; ?>;
  window.currentUserId = <?php echo !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : '0'; ?>;
  window.showSigninModalOnLoad = <?php echo ($show_signin_modal ? 'true' : 'false'); ?>;
  window.signinModalMessage = <?php echo json_encode($signin_modal_message ?: 'You must be signed in to proceed'); ?>;
</script>
<script src="../js/bookTemplate.js"></script>
<script src="../js/sign-inPrompt.js"></script>

</body>
</html>

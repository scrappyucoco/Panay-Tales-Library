<?php
// AJAX API for bookTemplate.php
session_start();
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'data' => null];

// Check if user is logged in
$user_id = (int) ($_SESSION['user_id'] ?? 0);
$action = $_POST['action'] ?? '';
$book_id = (int) ($_POST['book_id'] ?? 0);

// Check whether comments table has a user_id column 
$has_user_id = false;
$colCheck = $connection->query("SHOW COLUMNS FROM comments LIKE 'user_id'");
if ($colCheck && $colCheck->num_rows > 0) {
  $has_user_id = true;
}

if ($action === 'comment_create') {
  if (empty($user_id)) {
    $response['message'] = 'You must be signed in to post a comment.';
  } else {
    $comment_text = trim($_POST['comment'] ?? '');
    if ($comment_text === '') {
      $response['message'] = 'Comment cannot be empty.';
    } elseif ($book_id <= 0) {
      $response['message'] = 'Invalid book ID.';
    } else {
      if ($has_user_id) {
        $stmt = $connection->prepare("INSERT INTO comments (book_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param('iis', $book_id, $user_id, $comment_text);
      } else {
        $stmt = $connection->prepare("INSERT INTO comments (book_id, comment, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param('is', $book_id, $comment_text);
      }
      
      if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Comment posted successfully.';
        // Fetch the newly created comment to return
        $new_comment_id = $connection->insert_id;
        $fetch = $connection->prepare(
          "SELECT c.id, c.comment, c.created_at, c.user_id, u.email FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.id = ?"
        );
        $fetch->bind_param('i', $new_comment_id);
        $fetch->execute();
        $res = $fetch->get_result();
        if ($row = $res->fetch_assoc()) {
          $response['data'] = $row;
        }
        $fetch->close();
      } else {
        $response['message'] = 'Database error: ' . htmlspecialchars($stmt->error);
      }
      $stmt->close();
    }
  }
}


else if ($action === 'comment_update') {
  if (empty($user_id)) {
    $response['message'] = 'You must be signed in to update a comment.';
  } else {
    $comment_id = (int) ($_POST['comment_id'] ?? 0);
    $comment_text = trim($_POST['comment'] ?? '');
    
    if ($comment_id <= 0) {
      $response['message'] = 'Invalid comment ID.';
    } elseif ($comment_text === '') {
      $response['message'] = 'Comment cannot be empty.';
    } else {
      // Verify ownership
      $verify = $connection->prepare("SELECT id FROM comments WHERE id = ? AND user_id = ?");
      $verify->bind_param('ii', $comment_id, $user_id);
      $verify->execute();
      $verifyRes = $verify->get_result();
      
      if ($verifyRes->num_rows > 0) {
        $update = $connection->prepare("UPDATE comments SET comment = ? WHERE id = ? AND user_id = ?");
        $update->bind_param('sii', $comment_text, $comment_id, $user_id);
        
        if ($update->execute()) {
          $response['success'] = true;
          $response['message'] = 'Comment updated successfully.';
          $response['data'] = ['id' => $comment_id, 'comment' => $comment_text];
        } else {
          $response['message'] = 'Database error: ' . htmlspecialchars($update->error);
        }
        $update->close();
      } else {
        $response['message'] = 'You can only update your own comments.';
      }
      $verify->close();
    }
  }
}

else if ($action === 'comment_delete') {
  if (empty($user_id)) {
    $response['message'] = 'You must be signed in to delete a comment.';
  } else {
    $comment_id = (int) ($_POST['comment_id'] ?? 0);
    
    if ($comment_id <= 0) {
      $response['message'] = 'Invalid comment ID.';
    } else {
      // Verify ownership
      $verify = $connection->prepare("SELECT id FROM comments WHERE id = ? AND user_id = ?");
      $verify->bind_param('ii', $comment_id, $user_id);
      $verify->execute();
      $verifyRes = $verify->get_result();
      
      if ($verifyRes->num_rows > 0) {
        $delete = $connection->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
        $delete->bind_param('ii', $comment_id, $user_id);
        
        if ($delete->execute()) {
          $response['success'] = true;
          $response['message'] = 'Comment deleted successfully.';
          $response['data'] = ['id' => $comment_id];
        } else {
          $response['message'] = 'Database error: ' . htmlspecialchars($delete->error);
        }
        $delete->close();
      } else {
        $response['message'] = 'You can only delete your own comments.';
      }
      $verify->close();
    }
  }
}

// ============================================================================
// FAVOURITE: TOGGLE (Add if missing, remove if exists)
// ============================================================================
else if ($action === 'favourite_toggle') {
  if (empty($user_id)) {
    $response['message'] = 'You must be signed in to modify favourites.';
  } elseif ($book_id <= 0) {
    $response['message'] = 'Invalid book ID.';
  } else {
    $chk = $connection->prepare("SELECT id FROM favourites WHERE user_id = ? AND book_id = ? LIMIT 1");
    $chk->bind_param('ii', $user_id, $book_id);
    $chk->execute();
    $chkRes = $chk->get_result();
    
    if ($chkRes->num_rows > 0) {
      // Already favourited, so REMOVE
      $chk->close();
      $del = $connection->prepare("DELETE FROM favourites WHERE user_id = ? AND book_id = ?");
      $del->bind_param('ii', $user_id, $book_id);
      
      if ($del->execute()) {
        $response['success'] = true;
        $response['message'] = 'Removed from favourites.';
        $response['data'] = ['is_favourite' => false];
      } else {
        $response['message'] = 'Database error: ' . htmlspecialchars($del->error);
      }
      $del->close();
    } else {
      // Not yet favourited, so ADD
      $chk->close();
      $ins = $connection->prepare("INSERT INTO favourites (user_id, book_id, created_at) VALUES (?, ?, NOW())");
      $ins->bind_param('ii', $user_id, $book_id);
      
      if ($ins->execute()) {
        $response['success'] = true;
        $response['message'] = 'Added to favourites.';
        $response['data'] = ['is_favourite' => true];
      } else {
        $response['message'] = 'Database error: ' . htmlspecialchars($ins->error);
      }
      $ins->close();
    }
  }
}

// ============================================================================
// FETCH: Get all comments for book
// ============================================================================
else if ($action === 'comments_fetch') {
  if ($book_id <= 0) {
    $response['message'] = 'Invalid book ID.';
  } else {
    $comments = [];
    if ($has_user_id) {
      $stmt = $connection->prepare(
        "SELECT c.id, c.comment, c.created_at, c.user_id, u.email FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.book_id = ? ORDER BY c.created_at DESC"
      );
      $stmt->bind_param('i', $book_id);
    } else {
      $stmt = $connection->prepare(
        "SELECT id, comment, created_at FROM comments WHERE book_id = ? ORDER BY created_at DESC"
      );
      $stmt->bind_param('i', $book_id);
    }
    
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
      $comments[] = $r;
    }
    $stmt->close();
    
    $response['success'] = true;
    $response['data'] = $comments;
  }
}

echo json_encode($response);
exit;

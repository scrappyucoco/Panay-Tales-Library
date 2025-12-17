document.addEventListener('DOMContentLoaded', function () {

  var bookIdInput = document.querySelector('input[name="book_id"]');
  var bookId = bookIdInput ? parseInt(bookIdInput.value) : 0;
  var apiUrl = './Book_AJAX_API.php';
  

  var commentsBox = document.getElementById('comments');
  if (!commentsBox) return;
  var resize = function () {
    commentsBox.style.height = 'auto';
    commentsBox.style.height = commentsBox.scrollHeight + 'px';
  };
  commentsBox.addEventListener('input', resize);
  
  var shareWrapper = document.querySelector('.share-wrapper');
  if (shareWrapper) {
    var shareToggle = shareWrapper.querySelector('.share-toggle');
    var shareMenu = shareWrapper.querySelector('.share-menu');
    var shareOptions = shareWrapper.querySelectorAll('.share-option');
    if (shareMenu) { shareMenu.hidden = true; }
    var pageUrlRaw = window.location.href;
    var pageUrl = encodeURIComponent(pageUrlRaw);
    var titleEl = document.querySelector('.book-title');
    var shareText = titleEl ? titleEl.textContent.trim() : 'Check this book out';
    var encodedText = encodeURIComponent(shareText);

    var closeMenu = function () { shareMenu.hidden = true; };

    shareToggle.addEventListener('click', function () {
      shareMenu.hidden = !shareMenu.hidden;
    });

    document.addEventListener('click', function (e) {
      if (!shareWrapper.contains(e.target)) {
        closeMenu();
      }
    });

    shareOptions.forEach(function (opt) {
      opt.addEventListener('click', function () {
        var action = opt.getAttribute('data-action');
        if (action === 'copy') {
          if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(pageUrlRaw).catch(function () {});
          } else {
            var temp = document.createElement('input');
            temp.value = pageUrlRaw;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
          }
        } else if (action === 'email') {
          var mailto = 'mailto:?subject=' + encodeURIComponent('Check this book out') + '&body=' + encodeURIComponent(pageUrlRaw);
          window.location.href = mailto;
        } else if (action === 'facebook') {
          var shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + pageUrl + '&quote=' + encodedText;
          window.open(shareUrl, '_blank', 'noopener');
        }
        closeMenu();
      });
    });
  }
  resize();

  // ========== COMMENT FORM SUBMISSION (AJAX) ==========
  var commentForm = document.querySelector('.comment-form');
  if (commentForm) {
    commentForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var textarea = this.querySelector('textarea[name="comment"]');
      var commentText = textarea.value.trim();
      
      if (commentText === '') return;
      
      var formData = new FormData();
      formData.append('action', 'comment_create');
      formData.append('book_id', bookId);
      formData.append('comment', commentText);
      
      fetch(apiUrl, {
        method: 'POST',
        body: formData
      })
      .then(function(response) { return response.json(); })
      .then(function(result) {
        if (result.success) {

          var comSec = document.querySelector('.comSec');
          if (comSec && result.data) {
            var newCommentDiv = createCommentElement(result.data, true);
            comSec.insertBefore(newCommentDiv, comSec.firstChild);
            attachCommentHandlers(newCommentDiv);

            var noCommentsMsg = document.getElementById('no-comments-message');
            if (noCommentsMsg) {
              noCommentsMsg.style.display = 'none';
            }
          }
          textarea.value = '';
          textarea.style.height = 'auto';
        } else {

          if (result.message.includes('signed in')) {
            showSigninModal(result.message);
          }
        }
      })
      .catch(function(error) { console.error('Error:', error); });
    });
  }

  // ========== FAVOURITE FORM SUBMISSION (AJAX) ==========
  var favForm = document.querySelector('.fav-form');
  if (favForm) {
    favForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var favBtn = this.querySelector('.fav-btn');
      
      var formData = new FormData();
      formData.append('action', 'favourite_toggle');
      formData.append('book_id', bookId);
      
      fetch(apiUrl, {
        method: 'POST',
        body: formData
      })
      .then(function(response) { return response.json(); })
      .then(function(result) {
        if (result.success) {
          var isFav = result.data.is_favourite;
          if (isFav) {
            favBtn.classList.add('active');
          } else {
            favBtn.classList.remove('active');
          }
        } else {
          if (result.message.includes('signed in')) {
            showSigninModal(result.message);
          }
        }
      })
      .catch(function(error) { console.error('Error:', error); });
    });
  }

  // ========== HELPER FUNCTIONS ==========
  function createCommentElement(comment, isOwn) {
    var div = document.createElement('div');
    div.className = 'single-comment';
    div.setAttribute('data-comment-id', comment.id);
    
    var headerDiv = document.createElement('div');
    headerDiv.className = 'comment-header';
    
    var userH4 = document.createElement('h4');
    userH4.className = 'comment-user';
    userH4.textContent = comment.email || 'User';
    headerDiv.appendChild(userH4);
    
    if (isOwn) {
      var actionsDiv = document.createElement('div');
      actionsDiv.className = 'comment-actions';
      
      var editBtn = document.createElement('button');
      editBtn.type = 'button';
      editBtn.className = 'comment-edit-btn';
      editBtn.title = 'Edit comment';
      editBtn.textContent = '✏️';
      
      var deleteBtn = document.createElement('button');
      deleteBtn.type = 'button';
      deleteBtn.className = 'comment-delete-btn';
      deleteBtn.title = 'Delete comment';
      deleteBtn.textContent = '🗑️';
      
      actionsDiv.appendChild(editBtn);
      actionsDiv.appendChild(deleteBtn);
      headerDiv.appendChild(actionsDiv);
    }
    div.appendChild(headerDiv);
    
    var textP = document.createElement('p');
    textP.className = 'comment-text';
    textP.textContent = comment.comment;
    div.appendChild(textP);
    
    var timeSmall = document.createElement('small');
    timeSmall.className = 'comment-time';
    timeSmall.textContent = comment.created_at;
    div.appendChild(timeSmall);
    
    if (isOwn) {
      var editForm = document.createElement('form');
      editForm.className = 'comment-edit-form';
      editForm.hidden = true;
      
      var textarea = document.createElement('textarea');
      textarea.className = 'edit-textarea';
      textarea.value = comment.comment;
      textarea.maxLength = 500;
      editForm.appendChild(textarea);
      
      var editActionsDiv = document.createElement('div');
      editActionsDiv.className = 'edit-actions';
      
      var saveBtn = document.createElement('button');
      saveBtn.type = 'button';
      saveBtn.className = 'edit-save-btn';
      saveBtn.textContent = 'Save';
      
      var cancelBtn = document.createElement('button');
      cancelBtn.type = 'button';
      cancelBtn.className = 'edit-cancel-btn';
      cancelBtn.textContent = 'Cancel';
      
      editActionsDiv.appendChild(saveBtn);
      editActionsDiv.appendChild(cancelBtn);
      editForm.appendChild(editActionsDiv);
      
      var commentIdInput = document.createElement('input');
      commentIdInput.type = 'hidden';
      commentIdInput.name = 'comment_id';
      commentIdInput.value = comment.id;
      editForm.appendChild(commentIdInput);
      
      var bookIdInput = document.createElement('input');
      bookIdInput.type = 'hidden';
      bookIdInput.name = 'book_id';
      bookIdInput.value = bookId;
      editForm.appendChild(bookIdInput);
      
      div.appendChild(editForm);
      
      var deleteForm = document.createElement('form');
      deleteForm.className = 'comment-delete-form';
      deleteForm.hidden = true;
      
      var confirmP = document.createElement('p');
      confirmP.textContent = 'Are you sure you want to delete this comment?';
      deleteForm.appendChild(confirmP);
      
      var deleteActionsDiv = document.createElement('div');
      deleteActionsDiv.className = 'delete-actions';
      
      var confirmBtn = document.createElement('button');
      confirmBtn.type = 'button';
      confirmBtn.className = 'delete-confirm-btn';
      confirmBtn.textContent = 'Yes, delete';
      
      var cancelDelBtn = document.createElement('button');
      cancelDelBtn.type = 'button';
      cancelDelBtn.className = 'delete-cancel-btn';
      cancelDelBtn.textContent = 'No, keep it';
      
      deleteActionsDiv.appendChild(confirmBtn);
      deleteActionsDiv.appendChild(cancelDelBtn);
      deleteForm.appendChild(deleteActionsDiv);
      
      var delCommentIdInput = document.createElement('input');
      delCommentIdInput.type = 'hidden';
      delCommentIdInput.name = 'comment_id';
      delCommentIdInput.value = comment.id;
      deleteForm.appendChild(delCommentIdInput);
      
      var delBookIdInput = document.createElement('input');
      delBookIdInput.type = 'hidden';
      delBookIdInput.name = 'book_id';
      delBookIdInput.value = bookId;
      deleteForm.appendChild(delBookIdInput);
      
      div.appendChild(deleteForm);
    }
    
    return div;
  }

  function attachCommentHandlers(parentElement) {
    var target = parentElement || document;
    var comments = target.classList ? 
      (target.classList.contains('single-comment') ? [target] : target.querySelectorAll('.single-comment')) :
      target.querySelectorAll('.single-comment');
    
    comments.forEach(function(commentDiv) {
      if (commentDiv.dataset.handlersAttached) return;
      commentDiv.dataset.handlersAttached = 'true';

      var editBtn = commentDiv.querySelector('.comment-edit-btn');
      var deleteBtn = commentDiv.querySelector('.comment-delete-btn');
      var editForm = commentDiv.querySelector('.comment-edit-form');
      var deleteForm = commentDiv.querySelector('.comment-delete-form');
      var commentText = commentDiv.querySelector('.comment-text');
      var cancelEditBtn = commentDiv.querySelector('.edit-cancel-btn');
      var cancelDeleteBtn = commentDiv.querySelector('.delete-cancel-btn');
      var saveEditBtn = editForm ? editForm.querySelector('.edit-save-btn') : null;
      var confirmDeleteBtn = deleteForm ? deleteForm.querySelector('.delete-confirm-btn') : null;

      if (editBtn && editForm) {
        editBtn.addEventListener('click', function (e) {
          e.preventDefault();
          commentText.hidden = true;
          editForm.hidden = false;
          var ta = editForm.querySelector('textarea');
          if (ta) ta.focus();
        });
      }

      if (cancelEditBtn && editForm) {
        cancelEditBtn.addEventListener('click', function (e) {
          e.preventDefault();
          commentText.hidden = false;
          editForm.hidden = true;
        });
      }

      if (saveEditBtn && editForm) {
        saveEditBtn.addEventListener('click', function (e) {
          e.preventDefault();
          var ta = editForm.querySelector('textarea');
          var newText = ta.value.trim();
          var commentId = parseInt(editForm.querySelector('input[name="comment_id"]').value);
          
          if (newText === '') return;
          
          var formData = new FormData();
          formData.append('action', 'comment_update');
          formData.append('comment_id', commentId);
          formData.append('book_id', bookId);
          formData.append('comment', newText);
          
          fetch(apiUrl, {
            method: 'POST',
            body: formData
          })
          .then(function(response) { return response.json(); })
          .then(function(result) {
            if (result.success) {
              commentText.textContent = newText;
              commentText.hidden = false;
              editForm.hidden = true;
            }
          })
          .catch(function(error) { console.error('Error:', error); });
        });
      }

      if (deleteBtn && deleteForm) {
        deleteBtn.addEventListener('click', function (e) {
          e.preventDefault();
          editForm.hidden = true;
          deleteForm.hidden = false;
        });
      }

      if (cancelDeleteBtn && deleteForm) {
        cancelDeleteBtn.addEventListener('click', function (e) {
          e.preventDefault();
          deleteForm.hidden = true;
        });
      }

      if (confirmDeleteBtn && deleteForm) {
        confirmDeleteBtn.addEventListener('click', function (e) {
          e.preventDefault();
          var commentId = parseInt(deleteForm.querySelector('input[name="comment_id"]').value);
          
          var formData = new FormData();
          formData.append('action', 'comment_delete');
          formData.append('comment_id', commentId);
          formData.append('book_id', bookId);
          
          fetch(apiUrl, {
            method: 'POST',
            body: formData
          })
          .then(function(response) { return response.json(); })
          .then(function(result) {
            if (result.success) {
              commentDiv.remove();
              var comSec = document.querySelector('.comSec');
              var remainingComments = comSec.querySelectorAll('.single-comment');
              if (remainingComments.length === 0) {
                var noCommentsMsg = document.getElementById('no-comments-message');
                if (!noCommentsMsg) {
                  noCommentsMsg = document.createElement('p');
                  noCommentsMsg.id = 'no-comments-message';
                  noCommentsMsg.textContent = 'No comments yet. Be the first to comment.';
                  comSec.appendChild(noCommentsMsg);
                } else {
                  noCommentsMsg.style.display = '';
                }
              }
            }
          })
          .catch(function(error) { console.error('Error:', error); });
        });
      }
    });
  }

  function showSigninModal(message) {
    var overlay = document.getElementById('signin-overlay');
    var msgEl = document.getElementById('signin-overlay-message');
    if (overlay && msgEl) {
      msgEl.textContent = message;
      overlay.classList.add('active');
    }
  }

  attachCommentHandlers();
});

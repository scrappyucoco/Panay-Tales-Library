document.addEventListener('DOMContentLoaded', function () {
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

  // Comment edit/delete functionality
  var singleComments = document.querySelectorAll('.single-comment');
  singleComments.forEach(function (commentDiv) {
    var editBtn = commentDiv.querySelector('.comment-edit-btn');
    var deleteBtn = commentDiv.querySelector('.comment-delete-btn');
    var editForm = commentDiv.querySelector('.comment-edit-form');
    var deleteForm = commentDiv.querySelector('.comment-delete-form');
    var commentText = commentDiv.querySelector('.comment-text');
    var cancelEditBtn = commentDiv.querySelector('.edit-cancel-btn');
    var cancelDeleteBtn = commentDiv.querySelector('.delete-cancel-btn');

    if (editBtn && editForm) {
      editBtn.addEventListener('click', function (e) {
        e.preventDefault();
        commentText.hidden = true;
        editForm.hidden = false;
        var textarea = editForm.querySelector('textarea');
        if (textarea) textarea.focus();
      });
    }

    if (cancelEditBtn && editForm) {
      cancelEditBtn.addEventListener('click', function (e) {
        e.preventDefault();
        commentText.hidden = false;
        editForm.hidden = true;
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
  });
});

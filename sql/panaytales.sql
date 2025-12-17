-- init_panaytales.sql
-- Creates/repairs tables for PanayTales: users, books, comments
-- Safe to run after backing up existing data. Adjust if you already have production data.

CREATE DATABASE IF NOT EXISTS `panaytalesdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `panaytalesdb`;

-- Drop in correct order (comments first because of FKs)
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `books`;
DROP TABLE IF EXISTS `users`;

-- Users table
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Books table
CREATE TABLE `books` (
  `books_id` INT NOT NULL PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `link` VARCHAR(255) DEFAULT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comments table
CREATE TABLE `comments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `book_id` INT NOT NULL,
  `user_id` INT NULL,
  `comment` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_comments_book` (`book_id`),
  KEY `idx_comments_user` (`user_id`),
  CONSTRAINT `fk_comments_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`books_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Favourites Table
CREATE TABLE IF NOT EXISTS favourites (
  'id' INT AUTO_INCREMENT PRIMARY KEY,
  'user_id' INT NOT NULL,
  'book_id' INT NOT NULL,
  'created_at' DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY ux_user_book (user_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert books 1-5
INSERT INTO `books` (`books_id`, `title`, `link`, `cover_image`) VALUES
(1, 'Hinilawod', 'bookslink/book1.php', 'books-asset/Hinilawod.png'),
(2, 'Alunsina', 'bookslink/book2.php', 'books-asset/Alunsina.jpg'),
(3, 'Bakunawa', 'bookslink/book3.php', 'books-asset/Bakunawa.jpg'),
(4, 'Tikbalang', 'bookslink/book4.php', 'books-asset/Tikbalang.jpg'),
(5, 'Tumao', 'bookslink/book5.php', 'books-asset/Tumao.jpg'),
(6, 'Bulalakaw', 'bookslink/book6.php', 'books-asset/Bulalakaw.jpg'),
(7, 'Gimo', 'bookslink/book7.php', 'books-asset/Gimo.jpg'),
(8, 'Mansion', 'bookslink/book8.php', 'books-asset/Mansion.jpg'),
(9, 'Maragtas', 'bookslink/book9.php', 'books-asset/Maragtas.jpg'),
(10, 'Polobulac', 'bookslink/book10.php', 'books-asset/Polobulac.jpg')
ON DUPLICATE KEY UPDATE title = VALUES(title), link = VALUES(link), cover_image = VALUES(cover_image);

-- Optional: create a test user (uncomment and run separately if you want)
-- To insert a user securely, generate a password hash in PHP using password_hash() and paste it here.
-- Example (run in PHP):
-- <?php echo password_hash('yourpassword', PASSWORD_DEFAULT); ?>
-- Then use the resulting hash in the INSERT below.

-- INSERT INTO `users` (`email`, `password`) VALUES ('test@example.com', '<PASTE_HASH_HERE>');

-- End of file

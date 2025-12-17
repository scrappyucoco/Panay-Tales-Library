<?php 
session_start();
require_once 'db.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
            // READ: Load the user record by email.
            $stmt = $connection->prepare('SELECT id, email, password FROM users WHERE email = ?');
            if (!$stmt) {
                $error = 'Database error: ' . htmlspecialchars($connection->error);
            } else {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                header('Location: HomePage.php');
                exit();
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
        $stmt->close();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - PanayTales</title>
    <link rel="icon" type="image/png" href="/panay-tales-library/images/book-button.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sign-in.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="signin-wrapper">
        <div class="signin-container">
            <div class="signin-card">
                <div class="signin-header">
                    <h1 class="signin-title">Welcome Back</h1>
                    <p class="signin-subtitle">Sign in to your PanayTales account</p>
                </div>
                
                <form class="signin-form" action="sign-in.php" method="POST">
                    <?php if ($error): ?>
                        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            <span class="toggle-password" onclick="togglePassword()">Show</span>
                        </div>
                    </div>
                    <button type="submit" class="signin-btn">Sign In</button>
                </form>
                
                <div class="signin-divider"><span>New to PanayTales?</span></div>
                
                <button type="button" class="create-account-btn" onclick="window.location.href='create_user.php'">
                    Create Account
                </button>
            </div>
        </div>
    </div>
    
    <div class="signin-footer">
        <p>By signing in, you agree to our <a href="">Terms of Service</a> and <a href="">Privacy Policy</a></p>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const btn = document.querySelector('.toggle-password');
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = 'Hide';
            } else {
                input.type = 'password';
                btn.textContent = 'Show';
            }
        }
    </script>
</body>
</html>
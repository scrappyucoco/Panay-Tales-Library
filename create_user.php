<?php 
session_start();
require_once 'db.php';

$error = '';
$success = '';

// Handle account creation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    
    // Validation
    if (empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $checkStmt = $connection->prepare('SELECT id FROM users WHERE email = ?');
        // READ: Check if the email is already registered to avoid duplicate accounts
        if (!$checkStmt) {
            $error = 'Database error: ' . htmlspecialchars($connection->error);
        } else {
            $checkStmt->bind_param('s', $email);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = 'This email is already registered. Please sign in or use a different email.';
            } else {
                // Hash the password and insert new user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $insertStmt = $connection->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
                
                // CREATE: Insert the new user with a hashed password if the email does not exist
                if (!$insertStmt) {
                    $error = 'Database error: ' . htmlspecialchars($connection->error);
                } else {
                    $insertStmt->bind_param('ss', $email, $hashedPassword);
                    
                    if ($insertStmt->execute()) {
                        $success = 'Account created successfully! Redirecting to sign in...';
                        header('refresh:2;url=sign-in.php');
                    } else {
                        $error = 'Error creating account: ' . htmlspecialchars($insertStmt->error);
                    }
                    $insertStmt->close();
                }
            }
            $checkStmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - PanayTales</title>
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
                    <h1 class="signin-title">Create Account</h1>
                    <p class="signin-subtitle">Join PanayTales and start your journey</p>
                </div>
                
                <form class="signin-form" action="create_user.php" method="POST">
                    <?php if ($error): ?>
                        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            <span class="toggle-password" onclick="togglePassword('password')">Show</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                            <span class="toggle-password" onclick="togglePassword('confirm_password')">Show</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="signin-btn">Create Account</button>
                </form>
                
                <div class="signin-divider"><span>Already have an account?</span></div>
                
                <button type="button" class="create-account-btn" onclick="window.location.href='sign-in.php'">
                    Sign In
                </button>
            </div>
        </div>
    </div>
    
    <div class="signin-footer">
        <p>By creating an account, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></p>
    </div>

    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const btn = input.nextElementSibling;
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

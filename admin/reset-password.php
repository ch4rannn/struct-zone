<?php
/**
 * StructZone Admin - Reset Password
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$token = $_GET['token'] ?? '';
$error = '';
$success = false;

if (empty($token)) {
    header('Location: login.php');
    exit;
}

// Validate token
$pdo = getDB();
$stmt = $pdo->prepare("SELECT id FROM sz_users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    $error = 'Invalid or expired reset link. Please request a new one.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    checkCSRF();
    
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($password)) {
        $error = 'Please enter a new password.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        if (resetPassword($token, $password)) {
            $success = true;
        } else {
            $error = 'Failed to reset password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - StructZone Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">SZ</div>
            <h1>Reset Password</h1>
            <p>Enter your new password</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            Password reset successfully! You can now <a href="login.php">login</a> with your new password.
        </div>
        <?php elseif ($user): ?>
        <form method="POST" class="login-form">
            <?php echo csrfField(); ?>
            
            <div class="form-group">
                <label class="form-label" for="password">New Password</label>
                <input type="password" id="password" name="password" class="form-input" 
                       placeholder="Minimum 8 characters" required minlength="8">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-input" 
                       placeholder="Confirm your password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg">Reset Password</button>
        </form>
        <?php endif; ?>
        
        <div class="login-footer">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>

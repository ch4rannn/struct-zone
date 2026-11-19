<?php
/**
 * StructZone Admin - Forgot Password
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } else {
        $token = generateResetToken($email);
        
        if ($token) {
            // In production, send an email with the reset link
            // For now, we'll show the token (remove in production!)
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset-password.php?token=" . $token;
            
            // TODO: Send email with reset link
            // mail($email, "Password Reset - StructZone", "Click here to reset: $resetLink");
            
            $message = "If an account exists with that email, a password reset link has been sent.";
            
            // Development only - show the link
            if ($_SERVER['SERVER_NAME'] === 'localhost') {
                $message .= "<br><br><small>Dev mode: <a href='$resetLink'>Reset Link</a></small>";
            }
        } else {
            // Don't reveal if email exists or not
            $message = "If an account exists with that email, a password reset link has been sent.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - StructZone Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">SZ</div>
            <h1>Forgot Password</h1>
            <p>Enter your email to reset your password</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="login-form">
            <?php echo csrfField(); ?>
            
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" 
                       placeholder="admin@structzone.com" required autofocus>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg">Send Reset Link</button>
        </form>
        
        <div class="login-footer">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>

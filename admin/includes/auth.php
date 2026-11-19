<?php
/**
 * StructZone Admin - Authentication Functions
 */

require_once __DIR__ . '/db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require login - redirect to login page if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Require admin role
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['error'] = 'You do not have permission to access this page.';
        header('Location: dashboard.php');
        exit;
    }
}

/**
 * Check if current user is admin
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Check if current user is editor
 */
function isEditor() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'editor';
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, email, name, role FROM sz_users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Attempt to login user
 */
function loginUser($email, $password) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM sz_users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        
        // Log activity
        logActivity($user['id'], 'login', 'user', $user['id'], 'User logged in');
        
        return true;
    }
    
    return false;
}

/**
 * Logout user
 */
function logoutUser() {
    if (isset($_SESSION['user_id'])) {
        logActivity($_SESSION['user_id'], 'logout', 'user', $_SESSION['user_id'], 'User logged out');
    }
    
    // Clear all session data
    $_SESSION = [];
    
    // Destroy session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

/**
 * Create password hash
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Generate password reset token
 */
function generateResetToken($email) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id FROM sz_users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return false;
    }
    
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    $stmt = $pdo->prepare("UPDATE sz_users SET reset_token = ?, reset_expires = ? WHERE id = ?");
    $stmt->execute([$token, $expires, $user['id']]);
    
    return $token;
}

/**
 * Reset password with token
 */
function resetPassword($token, $newPassword) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id FROM sz_users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return false;
    }
    
    $hashedPassword = hashPassword($newPassword);
    $stmt = $pdo->prepare("UPDATE sz_users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
    $stmt->execute([$hashedPassword, $user['id']]);
    
    return true;
}

/**
 * Log activity
 */
function logActivity($userId, $action, $entityType = null, $entityId = null, $details = null) {
    try {
        $pdo = getDB();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $stmt = $pdo->prepare("INSERT INTO sz_activity_log (user_id, action, entity_type, entity_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $action, $entityType, $entityId, $details, $ip]);
    } catch (Exception $e) {
        // Silently fail - activity logging should not break the app
        error_log("Activity log error: " . $e->getMessage());
    }
}

/**
 * Create new user (admin only)
 */
function createUser($email, $password, $name, $role = 'editor') {
    $pdo = getDB();
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM sz_users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Email already exists'];
    }
    
    $hashedPassword = hashPassword($password);
    $stmt = $pdo->prepare("INSERT INTO sz_users (email, password, name, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$email, $hashedPassword, $name, $role]);
    
    return ['success' => true, 'id' => $pdo->lastInsertId()];
}

<?php
/**
 * StructZone Admin - Index Redirect
 * Redirects to login page (or dashboard if already logged in)
 */

require_once 'includes/auth.php';

// If already logged in, go to dashboard
if (isLoggedIn()) {
    header('Location: /admin/dashboard.php');
    exit;
}

// Otherwise, redirect to login page
header('Location: /admin/login.php');
exit;

<?php
/**
 * StructZone Admin - Logout
 */

require_once 'includes/auth.php';

logoutUser();
header('Location: login.php');
exit;

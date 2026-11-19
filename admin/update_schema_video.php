<?php
/**
 * Update Schema - add video_url
 * Run this from browser while logged in as admin
 */
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

if (!isAdmin()) {
    die("Access denied");
}

$pdo = getDB();

try {
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM sz_projects LIKE 'video_url'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $pdo->exec("ALTER TABLE sz_projects ADD COLUMN video_url VARCHAR(255) NULL AFTER featured_image");
        echo "<h2 style='color: green;'>✓ Successfully added 'video_url' column to 'sz_projects' table.</h2>";
    } else {
        echo "<h2 style='color: blue;'>ℹ Column 'video_url' already exists.</h2>";
    }
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</h2>";
}
?>
<br><br>
<a href="projects.php" class="btn btn-primary">← Back to Projects</a>

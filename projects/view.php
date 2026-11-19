<?php
/**
 * Dynamic Project View Page
 * Handles all project detail views via query string
 * URL: /projects/view.php?slug=project-slug
 */

$project_slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($project_slug)) {
    // Redirect to projects list if no slug
    header('Location: ./');
    exit;
}

require_once '../includes/project-template.php';
?>

<?php
/**
 * StructZone Configuration
 * Site-wide settings and constants
 */

// Site Info
define('SITE_NAME', 'StructZone');

// Detect Protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Detect Domain & Port
$domainName = $_SERVER['HTTP_HOST'];

// Detect Base URL
// If running on localhost/structzone (classic XAMPP), keep path. 
// If running on php -S localhost:8081 (root served), remove path.
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])); // e.g., /structzone/includes or /includes
$baseDir = dirname($scriptDir); // Parent of includes, so /structzone or /

// Normalize baseDir to remove trailing slash if it's just root
$baseDir = ($baseDir == '/' || $baseDir == '\\') ? '' : $baseDir;

// Site URL Configuration (Auto-detect or Environment Variable)
if (getenv('SITE_URL')) {
    define('SITE_URL', rtrim(getenv('SITE_URL'), '/'));
} else {
    // Local fallback
    define('SITE_URL', 'http://localhost:8000');
}
define('SITE_TAGLINE', 'Engineering Stability. Designing the Future.');

// Contact Info
define('CONTACT_EMAIL', 'info@structzone.com');
define('CONTACT_PHONE', '+91 6201418611');
define('CONTACT_WHATSAPP', '916201418611');
define('CONTACT_ADDRESS', 'Life Style Residency, Kharar - Landran Rd, Sector 116, Sahibzada Ajit Singh Nagar, Punjab 140307');
define('WORKING_HOURS', '09:00 AM - 07:00 PM');

// Social Links
define('SOCIAL_LINKEDIN', 'https://linkedin.com/company/structzone');

// Meta Defaults
define('META_TITLE_SUFFIX', ' | StructZone');
define('META_DESCRIPTION_DEFAULT', 'Expert structural design, BIM modeling, and engineering analysis for developers, architects, and contractors. Reliable consultancy across India.');

// Paths
define('ASSETS_PATH', SITE_URL . '/assets');
define('INCLUDES_PATH', __DIR__);

// Helper function for asset URLs
function asset($path) {
    return ASSETS_PATH . '/' . ltrim($path, '/');
}

// Helper function for page URLs
function url($path = '') {
    return SITE_URL . '/' . ltrim($path, '/');
}
?>

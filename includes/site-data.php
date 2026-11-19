<?php
/**
 * StructZone Frontend - Database Helper
 * 
 * Include this file in frontend pages to access dynamic content from the admin panel.
 * Usage: require_once 'includes/site-data.php';
 */

// Database connection (same as admin)
function getSiteDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        // Database Configuration - same as admin/includes/db.php
        $host = 'localhost';
        $dbname = 'structzone_db';  // Change to your database name
        $user = 'root';             // Change to your database username
        $pass = '';                 // Change to your database password
        
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            // Fallback to static content if database unavailable
            return null;
        }
    }
    
    return $pdo;
}

/**
 * Get site settings
 */
function getSiteSettings() {
    static $settings = null;
    
    if ($settings === null) {
        $pdo = getSiteDB();
        if ($pdo) {
            try {
                $stmt = $pdo->query("SELECT * FROM sz_settings WHERE id = 1");
                $settings = $stmt->fetch() ?: [];
            } catch (Exception $e) {
                $settings = [];
            }
        } else {
            $settings = [];
        }
    }
    
    return $settings;
}

/**
 * Get homepage settings
 */
function getHomepageSettings() {
    $pdo = getSiteDB();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->query("SELECT * FROM sz_homepage WHERE id = 1");
        return $stmt->fetch() ?: [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get all active services
 */
function getSiteServices() {
    $pdo = getSiteDB();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->query("SELECT * FROM sz_services WHERE is_active = 1 ORDER BY display_order ASC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get a single service by slug
 */
function getSiteService($slug) {
    $pdo = getSiteDB();
    if (!$pdo) return null;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM sz_services WHERE slug = ? AND is_active = 1");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Get featured projects
 */
function getFeaturedProjects($limit = 6) {
    $pdo = getSiteDB();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM sz_projects WHERE is_featured = 1 ORDER BY display_order ASC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get all projects (optionally by category)
 */
function getSiteProjects($category = null) {
    $pdo = getSiteDB();
    if (!$pdo) return [];
    
    try {
        if ($category) {
            $stmt = $pdo->prepare("SELECT * FROM sz_projects WHERE category = ? ORDER BY display_order ASC");
            $stmt->execute([$category]);
        } else {
            $stmt = $pdo->query("SELECT * FROM sz_projects ORDER BY display_order ASC");
        }
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get a single project by slug
 */
function getSiteProject($slug) {
    $pdo = getSiteDB();
    if (!$pdo) return null;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM sz_projects WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Get published insights
 */
function getSiteInsights($limit = 10) {
    $pdo = getSiteDB();
    if (!$pdo) return [];
    
    try {
        $sql = "SELECT * FROM sz_insights WHERE status = 'published' ORDER BY published_at DESC";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get a single insight by slug
 */
function getSiteInsight($slug) {
    $pdo = getSiteDB();
    if (!$pdo) return null;
    
    try {
        $stmt = $pdo->prepare("SELECT i.*, u.name as author_name FROM sz_insights i 
                               LEFT JOIN sz_users u ON i.author_id = u.id 
                               WHERE i.slug = ? AND i.status = 'published'");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Get about page content
 */
function getAboutContent() {
    $pdo = getSiteDB();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->query("SELECT * FROM sz_about WHERE id = 1");
        return $stmt->fetch() ?: [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get team/founders
 */
function getSiteFounders() {
    $pdo = getSiteDB();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->query("SELECT * FROM sz_founders WHERE is_active = 1 ORDER BY display_order ASC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Save contact form enquiry
 */
function saveEnquiry($data) {
    $pdo = getSiteDB();
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO sz_enquiries (name, email, phone, company, service, message) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['phone'] ?? '',
            $data['company'] ?? '',
            $data['service'] ?? '',
            $data['message'] ?? ''
        ]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get active client testimonials
 */
function getClients() {
    $pdo = getSiteDB();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->query("SELECT * FROM sz_clients WHERE is_active = 1 ORDER BY display_order ASC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get setting value with fallback
 */
function siteSetting($key, $default = '') {
    $settings = getSiteSettings();
    return $settings[$key] ?? $default;
}

/**
 * Get SVG icon by key
 */
function getIconSvg($key) {
    $icons = [
        'building' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"></path></svg>',
        'blueprint' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>',
        'foundation' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>',
        'inspection' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
        'drafting' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>',
        'analysis' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
        'default' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>'
    ];
    
    return $icons[$key] ?? $icons['default'];
}

/**
 * Get Video Embed HTML from YouTube/Vimeo URL
 */
function getVideoEmbed($url) {
    if (empty($url)) return '';
    
    // YouTube patterns
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        $videoId = $matches[1];
        return '<div class="video-embed-container"><iframe src="https://www.youtube.com/embed/' . htmlspecialchars($videoId) . '?rel=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
    }
    
    // Vimeo patterns
    if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $url, $matches)) {
        $videoId = $matches[1];
        return '<div class="video-embed-container"><iframe src="https://player.vimeo.com/video/' . htmlspecialchars($videoId) . '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe></div>';
    }
    
    return ''; // Unsupported URL
}

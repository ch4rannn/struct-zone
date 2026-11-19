<?php
/**
 * StructZone Admin - Helper Functions
 */

require_once __DIR__ . '/db.php';

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output CSRF hidden input field
 */
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCSRFToken()) . '">';
}

/**
 * Check CSRF on POST requests
 */
function checkCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            die('Invalid CSRF token. Please refresh the page and try again.');
        }
    }
}

/**
 * Sanitize input - only trims whitespace
 * NOTE: Use htmlspecialchars() when DISPLAYING data, not when saving
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return trim($input);
}

/**
 * Generate slug from string
 */
function generateSlug($string) {
    $slug = strtolower($string);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

/**
 * Make slug unique
 */
function makeUniqueSlug($table, $slug, $excludeId = null) {
    $pdo = getDB();
    $originalSlug = $slug;
    $counter = 1;
    
    while (true) {
        $sql = "SELECT id FROM $table WHERE slug = ?";
        $params = [$slug];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        if (!$stmt->fetch()) {
            break;
        }
        
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}

/**
 * Handle file upload
 */
function uploadFile($file, $directory = 'uploads', $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp']) {
    $uploadDir = __DIR__ . '/../' . $directory . '/';
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload failed with error code: ' . $file['error']];
    }
    
    // Validate file type
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes)];
    }
    
    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'error' => 'File too large. Maximum size is 5MB.'];
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $filepath = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => '/admin/' . $directory . '/' . $filename, 'filename' => $filename];
    }
    
    return ['success' => false, 'error' => 'Failed to move uploaded file.'];
}

/**
 * Delete file
 */
function deleteFile($path) {
    $fullPath = __DIR__ . '/..' . $path;
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Format date time for display
 */
function formatDateTime($datetime, $format = 'M d, Y H:i') {
    return date($format, strtotime($datetime));
}

/**
 * Truncate text
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Get setting value
 */
function getSetting($key, $default = null) {
    static $settings = null;
    
    if ($settings === null) {
        $pdo = getDB();
        $stmt = $pdo->query("SELECT * FROM sz_settings WHERE id = 1");
        $settings = $stmt->fetch() ?: [];
    }
    
    return $settings[$key] ?? $default;
}

/**
 * Update setting
 */
function updateSetting($key, $value) {
    $pdo = getDB();
    $stmt = $pdo->prepare("UPDATE sz_settings SET $key = ? WHERE id = 1");
    return $stmt->execute([$value]);
}

/**
 * Get count from table
 */
function getCount($table, $where = '') {
    $pdo = getDB();
    $sql = "SELECT COUNT(*) as count FROM $table";
    if ($where) {
        $sql .= " WHERE $where";
    }
    $stmt = $pdo->query($sql);
    $result = $stmt->fetch();
    return $result['count'] ?? 0;
}

/**
 * Set flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message HTML
 */
function displayFlash() {
    $flash = getFlash();
    if ($flash) {
        $class = $flash['type'] === 'success' ? 'alert-success' : ($flash['type'] === 'error' ? 'alert-error' : 'alert-info');
        echo '<div class="alert ' . $class . '">' . htmlspecialchars($flash['message']) . '</div>';
    }
}

/**
 * Redirect with message
 */
function redirectWithMessage($url, $type, $message) {
    setFlash($type, $message);
    header("Location: $url");
    exit;
}

/**
 * Get paginated results
 */
function paginate($table, $page = 1, $perPage = 10, $where = '', $orderBy = 'id DESC') {
    $pdo = getDB();
    $offset = ($page - 1) * $perPage;
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM $table";
    if ($where) {
        $countSql .= " WHERE $where";
    }
    $stmt = $pdo->query($countSql);
    $total = $stmt->fetch()['total'];
    
    // Get results
    $sql = "SELECT * FROM $table";
    if ($where) {
        $sql .= " WHERE $where";
    }
    $sql .= " ORDER BY $orderBy LIMIT $perPage OFFSET $offset";
    
    $stmt = $pdo->query($sql);
    $items = $stmt->fetchAll();
    
    return [
        'items' => $items,
        'total' => $total,
        'page' => $page,
        'perPage' => $perPage,
        'totalPages' => ceil($total / $perPage)
    ];
}

/**
 * Available icons for services
 */
function getAvailableIcons() {
    return [
        'building' => 'Building',
        'blueprint' => 'Blueprint',
        'foundation' => 'Foundation',
        'beam' => 'Beam',
        'column' => 'Column',
        'bridge' => 'Bridge',
        'inspection' => 'Inspection',
        'analysis' => 'Analysis',
        'drafting' => 'Drafting',
        'consulting' => 'Consulting',
        'renovation' => 'Renovation',
        'seismic' => 'Seismic'
    ];
}

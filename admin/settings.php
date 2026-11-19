<?php
/**
 * StructZone Admin - Global Settings
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Only admins can access settings
requireAdmin();

$pdo = getDB();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    $data = [
        'site_name' => sanitize($_POST['site_name'] ?? ''),
        'tagline' => sanitize($_POST['tagline'] ?? ''),
        'footer_text' => sanitize($_POST['footer_text'] ?? ''),
        'facebook' => sanitize($_POST['facebook'] ?? ''),
        'twitter' => sanitize($_POST['twitter'] ?? ''),
        'linkedin' => sanitize($_POST['linkedin'] ?? ''),
        'instagram' => sanitize($_POST['instagram'] ?? ''),
        'youtube' => sanitize($_POST['youtube'] ?? ''),
        'whatsapp' => sanitize($_POST['whatsapp'] ?? ''),
        'contact_email' => sanitize($_POST['contact_email'] ?? ''),
        'contact_phone' => sanitize($_POST['contact_phone'] ?? ''),
        'contact_address' => sanitize($_POST['contact_address'] ?? ''),
    ];
    
    // Handle logo upload
    $logo = '';
    $current = $pdo->query("SELECT logo FROM sz_settings WHERE id = 1")->fetch();
    $logo = $current['logo'] ?? '';
    
    if (!empty($_FILES['logo']['name'])) {
        $result = uploadFile($_FILES['logo'], 'uploads');
        if ($result['success']) {
            $logo = $result['path'];
        } else {
            setFlash('error', $result['error']);
        }
    }
    
    $stmt = $pdo->prepare("UPDATE sz_settings SET 
        site_name = ?, tagline = ?, logo = ?, footer_text = ?,
        facebook = ?, twitter = ?, linkedin = ?, instagram = ?, youtube = ?,
        whatsapp = ?, contact_email = ?, contact_phone = ?, contact_address = ?
        WHERE id = 1");
    
    $stmt->execute([
        $data['site_name'], $data['tagline'], $logo, $data['footer_text'],
        $data['facebook'], $data['twitter'], $data['linkedin'], $data['instagram'], $data['youtube'],
        $data['whatsapp'], $data['contact_email'], $data['contact_phone'], $data['contact_address']
    ]);
    
    logActivity($_SESSION['user_id'], 'update', 'settings', 1, 'Updated site settings');
    redirectWithMessage('settings.php', 'success', 'Settings saved successfully!');
}

$pageTitle = 'Settings';
require_once 'includes/header.php';

// Get settings
$settings = $pdo->query("SELECT * FROM sz_settings WHERE id = 1")->fetch();
if (!$settings) {
    $pdo->exec("INSERT INTO sz_settings (id) VALUES (1)");
    $settings = $pdo->query("SELECT * FROM sz_settings WHERE id = 1")->fetch();
}
?>

<form method="POST" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- General Settings -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">General</h2>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label" for="site_name">Site Name</label>
                    <input type="text" id="site_name" name="site_name" class="form-input"
                           value="<?php echo htmlspecialchars($settings['site_name'] ?? 'StructZone'); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="tagline">Tagline</label>
                    <input type="text" id="tagline" name="tagline" class="form-input"
                           value="<?php echo htmlspecialchars($settings['tagline'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="logo">Logo</label>
                    <input type="file" id="logo" name="logo" class="form-input" accept="image/*" data-preview="logo_preview">
                    <?php if (!empty($settings['logo'])): ?>
                    <img id="logo_preview" src="<?php echo htmlspecialchars($settings['logo']); ?>" 
                         alt="Logo" style="max-width: 200px; margin-top: 12px;">
                    <?php else: ?>
                    <img id="logo_preview" alt="" style="display: none; max-width: 200px; margin-top: 12px;">
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="footer_text">Footer Text</label>
                    <textarea id="footer_text" name="footer_text" class="form-textarea" rows="3"
                              placeholder="Footer copyright text..."><?php echo htmlspecialchars($settings['footer_text'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
        
        <!-- Contact Info -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Contact Information</h2>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label" for="contact_email">Contact Email</label>
                    <input type="email" id="contact_email" name="contact_email" class="form-input"
                           value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="contact_phone">Contact Phone</label>
                    <input type="tel" id="contact_phone" name="contact_phone" class="form-input"
                           value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="whatsapp">WhatsApp Number</label>
                    <input type="tel" id="whatsapp" name="whatsapp" class="form-input"
                           value="<?php echo htmlspecialchars($settings['whatsapp'] ?? ''); ?>"
                           placeholder="+91 1234567890">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="contact_address">Address</label>
                    <textarea id="contact_address" name="contact_address" class="form-textarea" rows="2"><?php echo htmlspecialchars($settings['contact_address'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
        
        <!-- Social Media -->
        <div class="card" style="grid-column: 1 / -1;">
            <div class="card-header">
                <h2 class="card-title">Social Media Links</h2>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label" for="linkedin">LinkedIn</label>
                    <input type="url" id="linkedin" name="linkedin" class="form-input"
                           value="<?php echo htmlspecialchars($settings['linkedin'] ?? ''); ?>"
                           placeholder="https://linkedin.com/company/...">
                </div>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 24px;">
        <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>

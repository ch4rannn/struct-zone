<?php
/**
 * StructZone Admin - Homepage Editor
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

$pdo = getDB();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    $data = [
        'hero_heading' => sanitize($_POST['hero_heading'] ?? ''),
        'hero_subheading' => sanitize($_POST['hero_subheading'] ?? ''),
        'cta_text' => sanitize($_POST['cta_text'] ?? ''),
        'cta_link' => sanitize($_POST['cta_link'] ?? ''),
        'hero_background' => sanitize($_POST['hero_background'] ?? ''),
        'show_services' => isset($_POST['show_services']) ? 1 : 0,
        'show_projects' => isset($_POST['show_projects']) ? 1 : 0,
        'show_testimonials' => isset($_POST['show_testimonials']) ? 1 : 0,
    ];
    
    // Handle file upload (images or videos)
    if (!empty($_FILES['hero_media']['name'])) {
        $result = uploadFile($_FILES['hero_media'], 'uploads', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'mp4', 'webm', 'mov', 'avi']);
        if ($result['success']) {
            $data['hero_background'] = $result['path'];
        } else {
            setFlash('error', $result['error']);
        }
    }
    
    $stmt = $pdo->prepare("UPDATE sz_homepage SET 
        hero_heading = ?, hero_subheading = ?, cta_text = ?, cta_link = ?,
        hero_background = ?, show_services = ?, show_projects = ?, show_testimonials = ?
        WHERE id = 1");
    
    $stmt->execute([
        $data['hero_heading'],
        $data['hero_subheading'],
        $data['cta_text'],
        $data['cta_link'],
        $data['hero_background'],
        $data['show_services'],
        $data['show_projects'],
        $data['show_testimonials']
    ]);
    
    logActivity($_SESSION['user_id'], 'update', 'homepage', 1, 'Updated homepage settings');
    redirectWithMessage('homepage.php', 'success', 'Homepage settings saved successfully!');
}

$pageTitle = 'Homepage';
require_once 'includes/header.php';

// Get current homepage data
$homepage = $pdo->query("SELECT * FROM sz_homepage WHERE id = 1")->fetch();
if (!$homepage) {
    $pdo->exec("INSERT INTO sz_homepage (id) VALUES (1)");
    $homepage = $pdo->query("SELECT * FROM sz_homepage WHERE id = 1")->fetch();
}
?>

<form method="POST" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Hero Content -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Hero Section</h2>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label" for="hero_heading">Hero Heading</label>
                    <input type="text" id="hero_heading" name="hero_heading" class="form-input" 
                           value="<?php echo htmlspecialchars($homepage['hero_heading'] ?? ''); ?>"
                           placeholder="Engineering Excellence">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="hero_subheading">Hero Subheading</label>
                    <textarea id="hero_subheading" name="hero_subheading" class="form-textarea" rows="3"
                              placeholder="Enter the subheading text..."><?php echo htmlspecialchars($homepage['hero_subheading'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="cta_text">CTA Button Text</label>
                        <input type="text" id="cta_text" name="cta_text" class="form-input" 
                               value="<?php echo htmlspecialchars($homepage['cta_text'] ?? 'Get a Quote'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="cta_link">CTA Button Link</label>
                        <input type="text" id="cta_link" name="cta_link" class="form-input" 
                               value="<?php echo htmlspecialchars($homepage['cta_link'] ?? '/contact.php'); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="hero_background">Hero Background (Image/Video URL)</label>
                    <input type="text" id="hero_background" name="hero_background" class="form-input" 
                           value="<?php echo htmlspecialchars($homepage['hero_background'] ?? ''); ?>"
                           placeholder="Enter URL or upload image below">
                    <p class="form-help">Enter a URL or upload an image below</p>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="hero_media">Upload Hero Image or Video</label>
                    <input type="file" id="hero_media" name="hero_media" class="form-input" 
                           accept="image/*,video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov">
                    <p class="form-help" style="margin-top: 8px; font-size: 0.85rem; color: #666;">Supports: JPG, PNG, GIF, WebP, SVG (images) and MP4, WebM, MOV (videos)</p>
                    <?php 
                    $bgPath = $homepage['hero_background'] ?? '';
                    $isVideo = preg_match('/\.(mp4|webm|mov|avi)$/i', $bgPath);
                    $isYouTube = (strpos($bgPath, 'youtube.com') !== false || strpos($bgPath, 'youtu.be') !== false);
                    ?>
                    <?php if (!empty($bgPath) && !$isVideo && !$isYouTube): ?>
                    <img id="hero_preview" src="<?php echo htmlspecialchars($bgPath); ?>" 
                         alt="Hero Preview" style="max-width: 300px; margin-top: 10px; border-radius: 8px;">
                    <?php elseif (!empty($bgPath) && $isVideo): ?>
                    <video id="hero_preview" src="<?php echo htmlspecialchars($bgPath); ?>" 
                           style="max-width: 300px; margin-top: 10px; border-radius: 8px;" controls muted></video>
                    <?php elseif (!empty($bgPath) && $isYouTube): ?>
                    <p style="margin-top: 10px; color: var(--color-accent);">📺 YouTube video configured</p>
                    <?php else: ?>
                    <img id="hero_preview" alt="" style="display: none; max-width: 300px; margin-top: 10px; border-radius: 8px;">
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Section Visibility -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Section Visibility</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="show_services" value="1" 
                                   <?php echo ($homepage['show_services'] ?? 1) ? 'checked' : ''; ?>>
                            <span>Show Services Section</span>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="show_projects" value="1"
                                   <?php echo ($homepage['show_projects'] ?? 1) ? 'checked' : ''; ?>>
                            <span>Show Featured Projects</span>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="show_testimonials" value="1"
                                   <?php echo ($homepage['show_testimonials'] ?? 1) ? 'checked' : ''; ?>>
                            <span>Show Testimonials</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="card" style="margin-top: 24px;">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>

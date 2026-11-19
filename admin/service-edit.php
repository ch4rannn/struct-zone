<?php
/**
 * StructZone Admin - Service Edit
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

$pdo = getDB();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$service = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM sz_services WHERE id = ?");
    $stmt->execute([$id]);
    $service = $stmt->fetch();
    if (!$service) {
        redirectWithMessage('services.php', 'error', 'Service not found.');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    $data = [
        'title' => sanitize($_POST['title'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
        'content' => $_POST['content'] ?? '', // Don't strict sanitize HTML content
        'icon' => sanitize($_POST['icon'] ?? 'building'),
        'display_order' => (int)($_POST['display_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
    
    // Generate slug
    $slug = generateSlug($data['title']);
    $slug = makeUniqueSlug('sz_services', $slug, $id);
    
    // Handle image upload
    $imagePath = $service['image'] ?? '';
    if (!empty($_FILES['image']['name'])) {
        $result = uploadFile($_FILES['image'], 'uploads');
        if ($result['success']) {
            $imagePath = $result['path'];
        } else {
            setFlash('error', $result['error']);
        }
    }
    
    if (empty($data['title'])) {
        setFlash('error', 'Service title is required.');
    } else {
            if ($id) {
                // Get old slug for cleanup
                $oldSlug = $service['slug'];
                
                $stmt = $pdo->prepare("UPDATE sz_services SET 
                    title = ?, slug = ?, description = ?, content = ?, image = ?, icon = ?, display_order = ?, is_active = ?
                    WHERE id = ?");
                $stmt->execute([
                    $data['title'], $slug, $data['description'], $data['content'], 
                    $imagePath, $data['icon'], 
                    $data['display_order'], $data['is_active'], $id
                ]);
                logActivity($_SESSION['user_id'], 'update', 'service', $id, 'Updated service: ' . $data['title']);
                
                // Cleanup old file if slug changed
                if ($oldSlug !== $slug) {
                    $oldFile = __DIR__ . '/../services/' . $oldSlug . '.php';
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
            } else {
                $stmt = $pdo->prepare("INSERT INTO sz_services 
                    (title, slug, description, content, image, icon, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data['title'], $slug, $data['description'], $data['content'], 
                    $imagePath, $data['icon'], 
                    $data['display_order'], $data['is_active']
                ]);
                logActivity($_SESSION['user_id'], 'create', 'service', $pdo->lastInsertId(), 'Created service: ' . $data['title']);
            }
            
            // Create/Update the service file
            $servicesDir = __DIR__ . '/../services/';
            if (!is_dir($servicesDir)) {
                mkdir($servicesDir, 0755, true);
            }
            
            $phpContent = "<?php\n\$service_slug = '{$slug}';\nrequire_once '../includes/service-template.php';\n?>";
            file_put_contents($servicesDir . $slug . '.php', $phpContent);
            
            redirectWithMessage('services.php', 'success', 'Service saved successfully!');
    }
}

$pageTitle = $id ? 'Edit Service' : 'Add Service';
require_once 'includes/header.php';

$icons = getAvailableIcons();
?>

<form method="POST" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Service Details</h2>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label" for="title">Service Title *</label>
                    <input type="text" id="title" name="title" class="form-input" required
                           value="<?php echo htmlspecialchars($service['title'] ?? ''); ?>"
                           placeholder="e.g., Structural Design">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="description">Short Description (for lists)</label>
                    <textarea id="description" name="description" class="form-textarea" rows="3"
                              placeholder="Brief summary used on homepage cards..."><?php echo htmlspecialchars($service['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="content">Full Content</label>
                    <textarea id="content" name="content" class="form-textarea" rows="10"
                              placeholder="Detailed service description..."><?php echo htmlspecialchars($service['content'] ?? ''); ?></textarea>
                    <p class="form-help">You can use basic HTML tags for formatting.</p>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="icon">Icon</label>
                        <select id="icon" name="icon" class="form-select">
                            <?php foreach ($icons as $key => $label): ?>
                            <option value="<?php echo $key; ?>" 
                                    <?php echo ($service['icon'] ?? 'building') === $key ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Service Image</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="image">Upload Image</label>
                        <input type="file" id="image" name="image" class="form-input" accept="image/*">
                        <?php if (!empty($service['image'])): ?>
                        <div style="margin-top: 12px; border-radius: 4px; overflow: hidden; border: 1px solid #e2e8f0;">
                            <img src="<?php echo htmlspecialchars($service['image']); ?>" 
                                 alt="Current" 
                                 style="width: 100%; height: auto; display: block;">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h2 class="card-title">Settings</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="display_order">Display Order</label>
                        <input type="number" id="display_order" name="display_order" class="form-input" 
                               value="<?php echo htmlspecialchars($service['display_order'] ?? 0); ?>"
                               min="0">
                        <p class="form-help">Lower numbers appear first</p>
                    </div>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="is_active" value="1" 
                                   <?php echo ($service['is_active'] ?? 1) ? 'checked' : ''; ?>>
                            <span>Active (visible on website)</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="card" style="margin-top: 24px;">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        <?php echo $id ? 'Update Service' : 'Create Service'; ?>
                    </button>
                    <a href="services.php" class="btn btn-secondary" style="width: 100%; margin-top: 12px;">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>

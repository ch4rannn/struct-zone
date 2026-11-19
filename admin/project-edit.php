<?php
/**
 * StructZone Admin - Project Edit
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

$pdo = getDB();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$project = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM sz_projects WHERE id = ?");
    $stmt->execute([$id]);
    $project = $stmt->fetch();
    if (!$project) {
        redirectWithMessage('projects.php', 'error', 'Project not found.');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    $data = [
        'title' => sanitize($_POST['title'] ?? ''),
        'category' => sanitize($_POST['category'] ?? 'Commercial'),
        'location' => sanitize($_POST['location'] ?? ''),
        'short_desc' => sanitize($_POST['short_desc'] ?? ''),
        'case_study' => $_POST['case_study'] ?? '', // Allow HTML for rich text
        'video_url' => sanitize($_POST['video_url'] ?? ''),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'display_order' => (int)($_POST['display_order'] ?? 0),
    ];
    
    // Handle featured image upload
    $featuredImage = $project['featured_image'] ?? '';
    if (!empty($_FILES['featured_image']['name'])) {
        $result = uploadFile($_FILES['featured_image'], 'uploads/projects');
        if ($result['success']) {
            $featuredImage = $result['path'];
        } else {
            setFlash('error', $result['error']);
        }
    }
    
    // Handle video file upload (takes priority over URL)
    $videoUrl = $project['video_url'] ?? '';
    if (!empty($_FILES['video_file']['name'])) {
        $allowedVideoTypes = ['video/mp4', 'video/webm', 'video/ogg'];
        $file = $_FILES['video_file'];
        if (in_array($file['type'], $allowedVideoTypes)) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'video_' . time() . '_' . uniqid() . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/projects/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $targetPath = $uploadDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $videoUrl = 'uploads/projects/' . $filename;
            } else {
                setFlash('error', 'Failed to upload video file.');
            }
        } else {
            setFlash('error', 'Invalid video type. Allowed: MP4, WebM, OGG.');
        }
    } elseif (!empty($_POST['video_url'])) {
        // Use URL if no file uploaded
        $videoUrl = sanitize($_POST['video_url']);
    }
    $data['video_url'] = $videoUrl;
    
    // Generate slug
    $slug = generateSlug($data['title']);
    $slug = makeUniqueSlug('sz_projects', $slug, $id);
    
    if (empty($data['title'])) {
        setFlash('error', 'Project title is required.');
    } else {
        if ($id) {
            // Get old slug for cleanup
            $oldSlug = $project['slug'];
            
            $stmt = $pdo->prepare("UPDATE sz_projects SET 
                title = ?, slug = ?, category = ?, location = ?, short_desc = ?, 
                case_study = ?, featured_image = ?, video_url = ?, is_featured = ?, display_order = ?
                WHERE id = ?");
            $stmt->execute([
                $data['title'], $slug, $data['category'], $data['location'], $data['short_desc'],
                $data['case_study'], $featuredImage, $data['video_url'], $data['is_featured'], $data['display_order'], $id
            ]);
            logActivity($_SESSION['user_id'], 'update', 'project', $id, 'Updated project: ' . $data['title']);
            
            // Cleanup old file if slug changed
            if ($oldSlug !== $slug) {
                $oldFile = __DIR__ . '/../projects/' . $oldSlug . '.php';
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO sz_projects 
                (title, slug, category, location, short_desc, case_study, featured_image, video_url, is_featured, display_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['title'], $slug, $data['category'], $data['location'], $data['short_desc'],
                $data['case_study'], $featuredImage, $data['video_url'], $data['is_featured'], $data['display_order']
            ]);
            $id = $pdo->lastInsertId();
            logActivity($_SESSION['user_id'], 'create', 'project', $id, 'Created project: ' . $data['title']);
        }
        
        // No longer creating individual PHP files - using dynamic routing via view.php
        
        redirectWithMessage('projects.php', 'success', 'Project saved successfully!');
    }
}

$pageTitle = $id ? 'Edit Project' : 'Add Project';
require_once 'includes/header.php';

$categories = ['Commercial', 'Residential', 'Industrial', 'Infrastructure', 'Institutional'];
?>

<form method="POST" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Project Details</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="title">Project Title <span class="required">*</span></label>
                        <input type="text" id="title" name="title" class="form-input" required
                               value="<?php echo htmlspecialchars($project['title'] ?? ''); ?>"
                               placeholder="e.g., Skyline Commercial Tower">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="category">Category</label>
                            <select id="category" name="category" class="form-select">
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat; ?>"
                                        <?php echo ($project['category'] ?? 'Commercial') === $cat ? 'selected' : ''; ?>>
                                    <?php echo $cat; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="location">Location</label>
                            <input type="text" id="location" name="location" class="form-input"
                                   value="<?php echo htmlspecialchars($project['location'] ?? ''); ?>"
                                   placeholder="e.g., Mumbai, India">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="short_desc">Short Description</label>
                        <textarea id="short_desc" name="short_desc" class="form-textarea" rows="3"
                                  placeholder="Brief description for cards and listings..."><?php echo htmlspecialchars($project['short_desc'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="case_study">Detailed Case Study</label>
                        <textarea id="case_study" name="case_study" class="form-textarea" rows="10"
                                  placeholder="Full project case study with details..."><?php echo htmlspecialchars($project['case_study'] ?? ''); ?></textarea>
                        <p class="form-help">You can use basic HTML for formatting.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Featured Image</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <input type="file" name="featured_image" class="form-input" accept="image/*" data-preview="image_preview">
                        <?php if (!empty($project['featured_image'])): ?>
                        <img id="image_preview" src="<?php echo htmlspecialchars($project['featured_image']); ?>" 
                             alt="Preview" style="max-width: 100%; margin-top: 12px; border-radius: 8px;">
                        <?php else: ?>
                        <img id="image_preview" alt="" style="display: none; max-width: 100%; margin-top: 12px; border-radius: 8px;">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h2 class="card-title">Project Video</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Upload Video File</label>
                        <input type="file" name="video_file" class="form-input" accept="video/mp4,video/webm,video/ogg">
                        <p class="form-help">Upload MP4, WebM, or OGG video (max 50MB recommended).</p>
                        <?php 
                        $currentVideo = $project['video_url'] ?? '';
                        $isLocalVideo = !empty($currentVideo) && strpos($currentVideo, 'http') !== 0;
                        if ($isLocalVideo): 
                        ?>
                        <video controls style="max-width: 100%; margin-top: 12px; border-radius: 8px;">
                            <source src="<?php echo htmlspecialchars($currentVideo); ?>" type="video/mp4">
                        </video>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group" style="margin-top: 16px;">
                        <label class="form-label" for="video_url">Or Paste Video URL</label>
                        <input type="url" id="video_url" name="video_url" class="form-input"
                               value="<?php echo htmlspecialchars(strpos($project['video_url'] ?? '', 'http') === 0 ? $project['video_url'] : ''); ?>"
                               placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                        <p class="form-help">YouTube/Vimeo URL (used if no file uploaded).</p>
                    </div>
                </div>
            </div>
            
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h2 class="card-title">Settings</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="is_featured" value="1" 
                                   <?php echo ($project['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                            <span>Featured Project</span>
                        </label>
                        <p class="form-help">Featured projects appear on homepage</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="display_order">Display Order</label>
                        <input type="number" id="display_order" name="display_order" class="form-input" 
                               value="<?php echo htmlspecialchars($project['display_order'] ?? 0); ?>"
                               min="0">
                    </div>
                </div>
            </div>
            
            <div class="card" style="margin-top: 24px;">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        <?php echo $id ? 'Update Project' : 'Create Project'; ?>
                    </button>
                    <a href="projects.php" class="btn btn-secondary" style="width: 100%; margin-top: 12px;">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>

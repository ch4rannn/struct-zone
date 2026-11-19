<?php
/**
 * StructZone Admin - Insight Edit
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

$pdo = getDB();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$insight = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM sz_insights WHERE id = ?");
    $stmt->execute([$id]);
    $insight = $stmt->fetch();
    if (!$insight) {
        redirectWithMessage('insights.php', 'error', 'Insight not found.');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    $data = [
        'title' => sanitize($_POST['title'] ?? ''),
        'content' => $_POST['content'] ?? '', // Allow HTML
        'excerpt' => sanitize($_POST['excerpt'] ?? ''),
        'meta_title' => sanitize($_POST['meta_title'] ?? ''),
        'meta_description' => sanitize($_POST['meta_description'] ?? ''),
        'status' => $_POST['status'] ?? 'draft',
    ];
    
    // Handle featured image upload
    $featuredImage = $insight['featured_image'] ?? '';
    if (!empty($_FILES['featured_image']['name'])) {
        $result = uploadFile($_FILES['featured_image'], 'uploads/insights');
        if ($result['success']) {
            $featuredImage = $result['path'];
        } else {
            setFlash('error', $result['error']);
        }
    }
    
    // Generate slug
    $slug = generateSlug($data['title']);
    $slug = makeUniqueSlug('sz_insights', $slug, $id);
    
    // Set published_at if publishing
    $publishedAt = $insight['published_at'] ?? null;
    if ($data['status'] === 'published' && !$publishedAt) {
        $publishedAt = date('Y-m-d H:i:s');
    }
    
    if (empty($data['title'])) {
        setFlash('error', 'Title is required.');
    } else {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE sz_insights SET 
                title = ?, slug = ?, content = ?, excerpt = ?, featured_image = ?,
                meta_title = ?, meta_description = ?, status = ?, published_at = ?
                WHERE id = ?");
            $stmt->execute([
                $data['title'], $slug, $data['content'], $data['excerpt'], $featuredImage,
                $data['meta_title'], $data['meta_description'], $data['status'], $publishedAt, $id
            ]);
            logActivity($_SESSION['user_id'], 'update', 'insight', $id, 'Updated insight: ' . $data['title']);
        } else {
            $stmt = $pdo->prepare("INSERT INTO sz_insights 
                (title, slug, content, excerpt, featured_image, meta_title, meta_description, status, published_at, author_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['title'], $slug, $data['content'], $data['excerpt'], $featuredImage,
                $data['meta_title'], $data['meta_description'], $data['status'], $publishedAt, $_SESSION['user_id']
            ]);
            logActivity($_SESSION['user_id'], 'create', 'insight', $pdo->lastInsertId(), 'Created insight: ' . $data['title']);
        }
        
        redirectWithMessage('insights.php', 'success', 'Insight saved successfully!');
    }
}

$pageTitle = $id ? 'Edit Insight' : 'Add Insight';
require_once 'includes/header.php';
?>

<form method="POST" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Content</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="title">Title <span class="required">*</span></label>
                        <input type="text" id="title" name="title" class="form-input" required
                               value="<?php echo htmlspecialchars($insight['title'] ?? ''); ?>"
                               placeholder="Enter article title">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="slug">URL Slug</label>
                        <input type="text" id="slug" name="slug" class="form-input" readonly
                               value="<?php echo htmlspecialchars($insight['slug'] ?? ''); ?>"
                               placeholder="auto-generated-from-title">
                        <p class="form-help">Auto-generated from title</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="excerpt">Excerpt</label>
                        <textarea id="excerpt" name="excerpt" class="form-textarea" rows="3"
                                  placeholder="Brief summary for listings..."><?php echo htmlspecialchars($insight['excerpt'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="content">Content</label>
                        <textarea id="content" name="content" class="form-textarea" rows="15"
                                  placeholder="Write your content here..."><?php echo htmlspecialchars($insight['content'] ?? ''); ?></textarea>
                        <p class="form-help">You can use HTML for formatting.</p>
                    </div>
                </div>
            </div>
            
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h2 class="card-title">SEO</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="meta_title">Meta Title</label>
                        <input type="text" id="meta_title" name="meta_title" class="form-input"
                               value="<?php echo htmlspecialchars($insight['meta_title'] ?? ''); ?>"
                               placeholder="SEO title (leave empty to use article title)">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="meta_description">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" class="form-textarea" rows="2"
                                  data-max-length="160"
                                  placeholder="SEO description..."><?php echo htmlspecialchars($insight['meta_description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Publish</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="status">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="draft" <?php echo ($insight['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo ($insight['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                        </select>
                    </div>
                    
                    <?php if (!empty($insight['published_at'])): ?>
                    <p class="form-help">Published: <?php echo formatDateTime($insight['published_at']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h2 class="card-title">Featured Image</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <input type="file" name="featured_image" class="form-input" accept="image/*" data-preview="image_preview">
                        <?php if (!empty($insight['featured_image'])): ?>
                        <img id="image_preview" src="<?php echo htmlspecialchars($insight['featured_image']); ?>" 
                             alt="Preview" style="max-width: 100%; margin-top: 12px; border-radius: 8px;">
                        <?php else: ?>
                        <img id="image_preview" alt="" style="display: none; max-width: 100%; margin-top: 12px; border-radius: 8px;">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="card" style="margin-top: 24px;">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        <?php echo $id ? 'Update Insight' : 'Create Insight'; ?>
                    </button>
                    <a href="insights.php" class="btn btn-secondary" style="width: 100%; margin-top: 12px;">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>

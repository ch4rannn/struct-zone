<?php
ob_start();
/**
 * StructZone Admin - Projects List
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

$pdo = getDB();

// Handle delete (Must be before header/HTML output)
if (isset($_GET['delete']) && isAdmin()) {
    checkCSRF();
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM sz_projects WHERE id = ?");
    $stmt->execute([$id]);
    logActivity($_SESSION['user_id'], 'delete', 'project', $id, 'Deleted project');
    redirectWithMessage('projects.php', 'success', 'Project deleted successfully.');
}

$pageTitle = 'Projects';
$headerActions = '<a href="project-edit.php" class="btn btn-primary">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
    Add Project
</a>';

require_once 'includes/header.php';

// Filtering
$category = $_GET['category'] ?? '';
$featured = isset($_GET['featured']) ? 1 : 0;

$sql = "SELECT * FROM sz_projects WHERE 1=1";
$params = [];

if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

if (isset($_GET['featured'])) {
    $sql .= " AND is_featured = 1";
}

$sql .= " ORDER BY display_order ASC, id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

$categories = ['Commercial', 'Residential', 'Industrial', 'Infrastructure', 'Institutional'];
?>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-body">
        <form method="GET" style="display: flex; gap: 16px; align-items: center;">
            <select name="category" class="form-select" style="width: auto;">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat; ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                    <?php echo $cat; ?>
                </option>
                <?php endforeach; ?>
            </select>
            
            <label class="form-checkbox">
                <input type="checkbox" name="featured" value="1" <?php echo $featured ? 'checked' : ''; ?>>
                <span>Featured only</span>
            </label>
            
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            <a href="projects.php" class="btn btn-secondary btn-sm">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <?php if (empty($projects)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
            </div>
            <h3>No projects found</h3>
            <p>Add your first project to get started.</p>
            <a href="project-edit.php" class="btn btn-primary">Add Project</a>
        </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                    <tr>
                        <td><?php echo $project['display_order']; ?></td>
                        <td><strong><?php echo htmlspecialchars($project['title']); ?></strong></td>
                        <td><span class="badge badge-info"><?php echo $project['category']; ?></span></td>
                        <td><?php echo htmlspecialchars($project['location'] ?? '-'); ?></td>
                        <td>
                            <?php if ($project['is_featured']): ?>
                            <span class="badge badge-success">Featured</span>
                            <?php else: ?>
                            <span class="badge badge-secondary" style="background: var(--bg-input); color: var(--text-muted);">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <a href="project-edit.php?id=<?php echo $project['id']; ?>" title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            <?php if (isAdmin()): ?>
                            <a href="projects.php?delete=<?php echo $project['id']; ?>&csrf_token=<?php echo generateCSRFToken(); ?>" 
                               class="delete" data-confirm="Are you sure you want to delete this project?" title="Delete">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

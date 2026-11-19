<?php
ob_start();
/**
 * StructZone Admin - Insights/Blog List
 */

$pageTitle = 'Insights';
$headerActions = '<a href="insight-edit.php" class="btn btn-primary">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
    Add Insight
</a>';

require_once 'includes/header.php';

$pdo = getDB();

// Handle delete
if (isset($_GET['delete']) && isAdmin()) {
    checkCSRF();
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM sz_insights WHERE id = ?");
    $stmt->execute([$id]);
    logActivity($_SESSION['user_id'], 'delete', 'insight', $id, 'Deleted insight');
    redirectWithMessage('insights.php', 'success', 'Insight deleted successfully.');
}

// Filtering
$status = $_GET['status'] ?? '';

$sql = "SELECT i.*, u.name as author_name FROM sz_insights i 
        LEFT JOIN sz_users u ON i.author_id = u.id WHERE 1=1";
$params = [];

if ($status) {
    $sql .= " AND i.status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY i.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$insights = $stmt->fetchAll();
?>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-body">
        <form method="GET" style="display: flex; gap: 16px; align-items: center;">
            <select name="status" class="form-select" style="width: auto;">
                <option value="">All Status</option>
                <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option>
                <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option>
            </select>
            
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            <a href="insights.php" class="btn btn-secondary btn-sm">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <?php if (empty($insights)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
            </div>
            <h3>No insights yet</h3>
            <p>Write your first insight or blog post.</p>
            <a href="insight-edit.php" class="btn btn-primary">Add Insight</a>
        </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($insights as $insight): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($insight['title']); ?></strong>
                            <div style="font-size: 12px; color: var(--text-muted);">
                                /insights/<?php echo htmlspecialchars($insight['slug']); ?>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($insight['author_name'] ?? 'Unknown'); ?></td>
                        <td>
                            <?php if ($insight['status'] === 'published'): ?>
                            <span class="badge badge-success">Published</span>
                            <?php else: ?>
                            <span class="badge badge-warning">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo formatDate($insight['created_at']); ?></td>
                        <td class="table-actions">
                            <a href="insight-edit.php?id=<?php echo $insight['id']; ?>" title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            <?php if (isAdmin()): ?>
                            <a href="insights.php?delete=<?php echo $insight['id']; ?>&csrf_token=<?php echo generateCSRFToken(); ?>" 
                               class="delete" data-confirm="Are you sure you want to delete this insight?" title="Delete">
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

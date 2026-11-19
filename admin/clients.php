<?php
ob_start();
/**
 * StructZone Admin - Clients/Testimonials Management
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

$pdo = getDB();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    // Validate CSRF token for GET request
    if (!isset($_GET['csrf_token']) || !validateCSRFToken($_GET['csrf_token'])) {
        redirectWithMessage('clients.php', 'error', 'Invalid security token. Please try again.');
    }
    $stmt = $pdo->prepare("DELETE FROM sz_clients WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    logActivity($_SESSION['user_id'], 'delete', 'client', $_GET['delete'], 'Deleted client testimonial');
    redirectWithMessage('clients.php', 'success', 'Client deleted successfully!');
}

// Handle reordering
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reorder'])) {
    checkCSRF();
    $orders = $_POST['order'] ?? [];
    $stmt = $pdo->prepare("UPDATE sz_clients SET display_order = ? WHERE id = ?");
    foreach ($orders as $id => $order) {
        $stmt->execute([$order, $id]);
    }
    redirectWithMessage('clients.php', 'success', 'Order updated!');
}

$pageTitle = 'Clients & Testimonials';
require_once 'includes/header.php';

// Fetch all clients
$clients = $pdo->query("SELECT * FROM sz_clients ORDER BY display_order ASC")->fetchAll();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1>Clients & Testimonials</h1>
        <p style="color: var(--color-text-muted);">Manage client testimonials displayed on the homepage</p>
    </div>
    <a href="client-edit.php" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add New Client
    </a>
</div>

<?php if (empty($clients)): ?>
<div class="card">
    <div class="card-body" style="text-align: center; padding: 60px 20px;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity: 0.3; margin-bottom: 16px;">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
        <h3 style="margin-bottom: 8px;">No Clients Yet</h3>
        <p style="color: var(--color-text-muted); margin-bottom: 24px;">Add your first client testimonial to display on the homepage.</p>
        <a href="client-edit.php" class="btn btn-primary">Add Client</a>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Photo</th>
                    <th>Name</th>
                    <th>Company/Role</th>
                    <th>Testimonial</th>
                    <th style="width: 80px;">Status</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client): ?>
                <tr>
                    <td>
                        <?php if (!empty($client['image'])): ?>
                        <img src="<?php echo htmlspecialchars($client['image']); ?>" 
                             alt="<?php echo htmlspecialchars($client['name']); ?>"
                             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--color-bg-alt); display: flex; align-items: center; justify-content: center; font-weight: 600; color: var(--color-text-muted);">
                            <?php echo strtoupper(substr($client['name'], 0, 1)); ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($client['name']); ?></strong></td>
                    <td>
                        <?php if (!empty($client['company'])): ?>
                            <?php echo htmlspecialchars($client['company']); ?> - 
                        <?php endif; ?>
                        <?php echo htmlspecialchars($client['role']); ?>
                    </td>
                    <td>
                        <span style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            "<?php echo htmlspecialchars(substr($client['testimonial'], 0, 100)); ?>..."
                        </span>
                    </td>
                    <td>
                        <?php if ($client['is_active']): ?>
                        <span class="badge badge-success">Active</span>
                        <?php else: ?>
                        <span class="badge badge-muted">Hidden</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="client-edit.php?id=<?php echo $client['id']; ?>" class="btn btn-sm btn-outline" title="Edit">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            <a href="clients.php?delete=<?php echo $client['id']; ?>&csrf_token=<?php echo generateCSRFToken(); ?>" 
                               class="btn btn-sm btn-danger" 
                               title="Delete"
                               onclick="return confirm('Are you sure you want to delete this client?');">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<style>
.action-buttons {
    display: flex;
    gap: 8px;
}
.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}
.badge-success {
    background: #dcfce7;
    color: #166534;
}
.badge-muted {
    background: #f1f5f9;
    color: #64748b;
}
.btn-sm {
    padding: 6px 10px;
}
.btn-danger {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
}
.btn-danger:hover {
    background: #fecaca;
}
</style>

<?php require_once 'includes/footer.php'; ?>

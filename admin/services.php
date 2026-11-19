<?php
ob_start();
/**
 * StructZone Admin - Services List
 */

$pageTitle = 'Services';
$headerActions = '<a href="service-edit.php" class="btn btn-primary">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
    Add Service
</a>';

require_once 'includes/header.php';

$pdo = getDB();

// Handle delete
if (isset($_GET['delete']) && isAdmin()) {
    checkCSRF();
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM sz_services WHERE id = ?");
    $stmt->execute([$id]);
    logActivity($_SESSION['user_id'], 'delete', 'service', $id, 'Deleted service');
    redirectWithMessage('services.php', 'success', 'Service deleted successfully.');
}

// Handle toggle active
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE sz_services SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
    redirectWithMessage('services.php', 'success', 'Service status updated.');
}

// Get services
$services = $pdo->query("SELECT * FROM sz_services ORDER BY display_order ASC, id DESC")->fetchAll();
?>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <?php if (empty($services)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                </svg>
            </div>
            <h3>No services yet</h3>
            <p>Add your first service to get started.</p>
            <a href="service-edit.php" class="btn btn-primary">Add Service</a>
        </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Title</th>
                        <th>Icon</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo $service['display_order']; ?></td>
                        <td><strong><?php echo htmlspecialchars($service['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($service['icon'] ?? 'building'); ?></td>
                        <td>
                            <?php if ($service['is_active']): ?>
                            <span class="badge badge-success">Active</span>
                            <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <a href="service-edit.php?id=<?php echo $service['id']; ?>" title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            <a href="services.php?toggle=<?php echo $service['id']; ?>" title="Toggle Status">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>
                            <?php if (isAdmin()): ?>
                            <a href="services.php?delete=<?php echo $service['id']; ?>&csrf_token=<?php echo generateCSRFToken(); ?>" 
                               class="delete" data-confirm="Are you sure you want to delete this service?" title="Delete">
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

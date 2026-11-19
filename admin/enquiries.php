<?php
/**
 * StructZone Admin - Enquiries
 */

// Include auth and functions first (no output)
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Start session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require login
requireLogin();

$pdo = getDB();

// Handle status update BEFORE any output
if (isset($_POST['update_status'])) {
    checkCSRF();
    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE sz_enquiries SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    setFlash('success', 'Enquiry status updated.');
    header("Location: enquiries.php");
    exit;
}

// Handle CSV export BEFORE any output
if (isset($_GET['export'])) {
    $enquiries = $pdo->query("SELECT * FROM sz_enquiries ORDER BY created_at DESC")->fetchAll();
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="enquiries_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Company', 'Service', 'Message', 'Status', 'Date']);
    
    foreach ($enquiries as $e) {
        fputcsv($output, [
            $e['id'], $e['name'], $e['email'], $e['phone'], $e['company'],
            $e['service'], $e['message'], $e['status'], $e['created_at']
        ]);
    }
    
    fclose($output);
    exit;
}

// Handle delete BEFORE any output
if (isset($_GET['delete']) && isAdmin()) {
    // Validate CSRF from GET parameter
    if (!isset($_GET['csrf_token']) || !validateCSRFToken($_GET['csrf_token'])) {
        setFlash('error', 'Invalid security token. Please try again.');
        header("Location: enquiries.php");
        exit;
    }
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM sz_enquiries WHERE id = ?");
    $stmt->execute([$id]);
    setFlash('success', 'Enquiry deleted.');
    header("Location: enquiries.php");
    exit;
}

// NOW include header (which outputs HTML)
$pageTitle = 'Enquiries';
require_once 'includes/header.php';

// Filtering
$status = $_GET['status'] ?? '';

$sql = "SELECT * FROM sz_enquiries WHERE 1=1";
$params = [];

if ($status) {
    $sql .= " AND status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$enquiries = $stmt->fetchAll();
?>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center;">
        <form method="GET" style="display: flex; gap: 16px; align-items: center;">
            <select name="status" class="form-select" style="width: auto;">
                <option value="">All Status</option>
                <option value="new" <?php echo $status === 'new' ? 'selected' : ''; ?>>New</option>
                <option value="in_progress" <?php echo $status === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="closed" <?php echo $status === 'closed' ? 'selected' : ''; ?>>Closed</option>
            </select>
            
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            <a href="enquiries.php" class="btn btn-secondary btn-sm">Reset</a>
        </form>
        
        <a href="enquiries.php?export=1" class="btn btn-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Export CSV
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <?php if (empty($enquiries)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
            </div>
            <h3>No enquiries yet</h3>
            <p>Contact form submissions will appear here.</p>
        </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enquiries as $enquiry): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($enquiry['name']); ?></strong>
                            <?php if ($enquiry['company']): ?>
                            <div style="font-size: 12px; color: var(--text-muted);">
                                <?php echo htmlspecialchars($enquiry['company']); ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div><?php echo htmlspecialchars($enquiry['email']); ?></div>
                            <?php if ($enquiry['phone']): ?>
                            <div style="font-size: 12px; color: var(--text-muted);">
                                <?php echo htmlspecialchars($enquiry['phone']); ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td style="max-width: 300px;">
                            <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                 title="<?php echo htmlspecialchars($enquiry['message']); ?>">
                                <?php echo htmlspecialchars(truncate($enquiry['message'], 80)); ?>
                            </div>
                        </td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <?php echo csrfField(); ?>
                                <input type="hidden" name="id" value="<?php echo $enquiry['id']; ?>">
                                <input type="hidden" name="update_status" value="1">
                                <select name="status" class="form-select" style="width: auto; padding: 4px 28px 4px 8px; font-size: 12px;" onchange="this.form.submit()">
                                    <option value="new" <?php echo $enquiry['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                    <option value="in_progress" <?php echo $enquiry['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="closed" <?php echo $enquiry['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                </select>
                            </form>
                        </td>
                        <td><?php echo formatDateTime($enquiry['created_at']); ?></td>
                        <td class="table-actions">
                            <button type="button" onclick="viewEnquiry(<?php echo htmlspecialchars(json_encode($enquiry)); ?>)" title="View">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                            <?php if (isAdmin()): ?>
                            <a href="enquiries.php?delete=<?php echo $enquiry['id']; ?>&csrf_token=<?php echo generateCSRFToken(); ?>" 
                               class="delete" data-confirm="Delete this enquiry?" title="Delete">
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

<!-- View Enquiry Modal -->
<div id="enquiryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3>Enquiry Details</h3>
            <button onclick="closeEnquiryModal()" style="background: none; border: none; cursor: pointer; padding: 4px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px;">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div style="padding: 20px;" id="enquiryContent"></div>
    </div>
</div>

<script>
function viewEnquiry(data) {
    const modal = document.getElementById('enquiryModal');
    const content = document.getElementById('enquiryContent');
    
    content.innerHTML = `
        <div style="margin-bottom: 16px;">
            <strong>Name:</strong> ${data.name}
        </div>
        <div style="margin-bottom: 16px;">
            <strong>Email:</strong> <a href="mailto:${data.email}">${data.email}</a>
        </div>
        ${data.phone ? `<div style="margin-bottom: 16px;"><strong>Phone:</strong> ${data.phone}</div>` : ''}
        ${data.company ? `<div style="margin-bottom: 16px;"><strong>Company:</strong> ${data.company}</div>` : ''}
        ${data.service ? `<div style="margin-bottom: 16px;"><strong>Service:</strong> ${data.service}</div>` : ''}
        <div style="margin-bottom: 16px;">
            <strong>Message:</strong>
            <div style="background: var(--bg-input); padding: 12px; border-radius: 8px; margin-top: 8px; white-space: pre-wrap;">${data.message}</div>
        </div>
        <div style="font-size: 12px; color: var(--text-muted);">
            Received: ${data.created_at}
        </div>
    `;
    
    modal.style.display = 'flex';
}

function closeEnquiryModal() {
    document.getElementById('enquiryModal').style.display = 'none';
}

document.getElementById('enquiryModal').addEventListener('click', function(e) {
    if (e.target === this) closeEnquiryModal();
});
</script>

<?php require_once 'includes/footer.php'; ?>

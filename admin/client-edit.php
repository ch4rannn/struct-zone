<?php
/**
 * StructZone Admin - Client/Testimonial Edit
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

$pdo = getDB();

$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$client = null;

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM sz_clients WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $client = $stmt->fetch();
    if (!$client) {
        redirectWithMessage('clients.php', 'error', 'Client not found.');
    }
}

// Handle form submission BEFORE header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    $data = [
        'name' => sanitize($_POST['name'] ?? ''),
        'company' => sanitize($_POST['company'] ?? ''),
        'role' => sanitize($_POST['role'] ?? 'Client'),
        'testimonial' => sanitize($_POST['testimonial'] ?? ''),
        'rating' => (int)($_POST['rating'] ?? 5),
        'display_order' => (int)($_POST['display_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
    
    // Handle image upload
    $imagePath = $client['image'] ?? '';
    if (!empty($_FILES['image']['name'])) {
        $result = uploadFile($_FILES['image'], 'uploads');
        if ($result['success']) {
            $imagePath = $result['path'];
        } else {
            setFlash('error', $result['error']);
        }
    }
    
    if ($isEdit) {
        $stmt = $pdo->prepare("UPDATE sz_clients SET 
            name = ?, company = ?, role = ?, testimonial = ?, image = ?, 
            rating = ?, display_order = ?, is_active = ?
            WHERE id = ?");
        $stmt->execute([
            $data['name'], $data['company'], $data['role'], $data['testimonial'],
            $imagePath, $data['rating'], $data['display_order'], $data['is_active'],
            $_GET['id']
        ]);
        logActivity($_SESSION['user_id'], 'update', 'client', $_GET['id'], 'Updated client: ' . $data['name']);
        redirectWithMessage('clients.php', 'success', 'Client updated successfully!');
    } else {
        $stmt = $pdo->prepare("INSERT INTO sz_clients 
            (name, company, role, testimonial, image, rating, display_order, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'], $data['company'], $data['role'], $data['testimonial'],
            $imagePath, $data['rating'], $data['display_order'], $data['is_active']
        ]);
        $newId = $pdo->lastInsertId();
        logActivity($_SESSION['user_id'], 'create', 'client', $newId, 'Created client: ' . $data['name']);
        redirectWithMessage('clients.php', 'success', 'Client added successfully!');
    }
}

$pageTitle = $isEdit ? 'Edit Client' : 'Add Client';
require_once 'includes/header.php';
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <a href="clients.php" style="color: var(--color-text-muted); text-decoration: none; font-size: 0.875rem;">
            ← Back to Clients
        </a>
        <h1 style="margin-top: 8px;"><?php echo $isEdit ? 'Edit Client' : 'Add New Client'; ?></h1>
    </div>
</div>

<form method="POST" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    
    <div class="form-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Main Content -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Client Information</h2>
                </div>
                <div class="card-body">
                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label" for="name">Client Name *</label>
                            <input type="text" id="name" name="name" class="form-input" required
                                   value="<?php echo htmlspecialchars($client['name'] ?? ''); ?>"
                                   placeholder="John Doe">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="company">Company</label>
                            <input type="text" id="company" name="company" class="form-input"
                                   value="<?php echo htmlspecialchars($client['company'] ?? ''); ?>"
                                   placeholder="AECOM, Tata Projects, etc.">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="role">Role/Title</label>
                        <input type="text" id="role" name="role" class="form-input"
                               value="<?php echo htmlspecialchars($client['role'] ?? 'Client'); ?>"
                               placeholder="Project Manager, CEO, Client">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="testimonial">Testimonial *</label>
                        <textarea id="testimonial" name="testimonial" class="form-textarea" rows="5" required
                                  placeholder="What the client said about your services..."><?php echo htmlspecialchars($client['testimonial'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Client Photo</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="image">Upload Photo</label>
                        <input type="file" id="image" name="image" class="form-input" accept="image/*">
                        <?php if (!empty($client['image'])): ?>
                        <img src="<?php echo htmlspecialchars($client['image']); ?>" 
                             alt="Current" 
                             style="margin-top: 12px; max-width: 100px; border-radius: 50%;">
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
                        <label class="form-label" for="rating">Rating (1-5)</label>
                        <select id="rating" name="rating" class="form-input">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo $i; ?>" <?php echo ($client['rating'] ?? 5) == $i ? 'selected' : ''; ?>>
                                <?php echo str_repeat('★', $i) . str_repeat('☆', 5-$i); ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="display_order">Display Order</label>
                        <input type="number" id="display_order" name="display_order" class="form-input"
                               value="<?php echo htmlspecialchars($client['display_order'] ?? 0); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="is_active" value="1" 
                                   <?php echo ($client['is_active'] ?? 1) ? 'checked' : ''; ?>>
                            <span>Active (Show on website)</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="card" style="margin-top: 24px;">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        <?php echo $isEdit ? 'Update Client' : 'Add Client'; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>

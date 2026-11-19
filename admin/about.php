<?php
/**
 * StructZone Admin - About Page Editor
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

$pdo = getDB();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    $action = $_POST['action'] ?? 'about';
    
    if ($action === 'about') {
        $data = [
            'intro_text' => sanitize($_POST['intro_text'] ?? ''),
            'experience_years' => (int)($_POST['experience_years'] ?? 10),
            'values_text' => sanitize($_POST['values_text'] ?? ''),
            'mission_text' => sanitize($_POST['mission_text'] ?? ''),
            'vision_text' => sanitize($_POST['vision_text'] ?? ''),
        ];
        
        $stmt = $pdo->prepare("UPDATE sz_about SET 
            intro_text = ?, experience_years = ?, values_text = ?, mission_text = ?, vision_text = ?
            WHERE id = 1");
        $stmt->execute([
            $data['intro_text'],
            $data['experience_years'],
            $data['values_text'],
            $data['mission_text'],
            $data['vision_text']
        ]);
        
        logActivity($_SESSION['user_id'], 'update', 'about', 1, 'Updated about page');
        redirectWithMessage('about.php', 'success', 'About page updated successfully!');
    }
    
    if ($action === 'founder') {
        $founderId = (int)($_POST['founder_id'] ?? 0);
        $founderData = [
            'name' => sanitize($_POST['founder_name'] ?? ''),
            'role' => sanitize($_POST['founder_role'] ?? ''),
            'bio' => sanitize($_POST['founder_bio'] ?? ''),
            'linkedin' => sanitize($_POST['founder_linkedin'] ?? ''),
            'display_order' => (int)($_POST['founder_order'] ?? 0),
            'is_active' => isset($_POST['founder_active']) ? 1 : 0,
        ];
        
        // Handle image upload
        $image = '';
        if ($founderId) {
            $stmt = $pdo->prepare("SELECT image FROM sz_founders WHERE id = ?");
            $stmt->execute([$founderId]);
            $existing = $stmt->fetch();
            $image = $existing['image'] ?? '';
        }
        
        if (!empty($_FILES['founder_image']['name'])) {
            $result = uploadFile($_FILES['founder_image'], 'uploads/team');
            if ($result['success']) {
                $image = $result['path'];
            }
        }
        
        if ($founderId) {
            $stmt = $pdo->prepare("UPDATE sz_founders SET 
                name = ?, role = ?, bio = ?, image = ?, linkedin = ?, display_order = ?, is_active = ?
                WHERE id = ?");
            $stmt->execute([
                $founderData['name'], $founderData['role'], $founderData['bio'], $image,
                $founderData['linkedin'], $founderData['display_order'], $founderData['is_active'], $founderId
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO sz_founders (name, role, bio, image, linkedin, display_order, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $founderData['name'], $founderData['role'], $founderData['bio'], $image,
                $founderData['linkedin'], $founderData['display_order'], $founderData['is_active']
            ]);
        }
        
        redirectWithMessage('about.php', 'success', 'Founder saved successfully!');
    }
    
    if ($action === 'delete_founder' && isAdmin()) {
        $founderId = (int)($_POST['founder_id'] ?? 0);
        if ($founderId) {
            $stmt = $pdo->prepare("DELETE FROM sz_founders WHERE id = ?");
            $stmt->execute([$founderId]);
            redirectWithMessage('about.php', 'success', 'Founder deleted.');
        }
    }
}

$pageTitle = 'About Page';
require_once 'includes/header.php';

// Get about data
$about = $pdo->query("SELECT * FROM sz_about WHERE id = 1")->fetch();
if (!$about) {
    $pdo->exec("INSERT INTO sz_about (id) VALUES (1)");
    $about = $pdo->query("SELECT * FROM sz_about WHERE id = 1")->fetch();
}

// Get founders
$founders = $pdo->query("SELECT * FROM sz_founders ORDER BY display_order ASC")->fetchAll();
?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- About Content -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">About Content</h2>
        </div>
        <div class="card-body">
            <form method="POST">
                <?php echo csrfField(); ?>
                <input type="hidden" name="action" value="about">
                
                <div class="form-group">
                    <label class="form-label" for="intro_text">Introduction Text</label>
                    <textarea id="intro_text" name="intro_text" class="form-textarea" rows="4"
                              placeholder="Company introduction..."><?php echo htmlspecialchars($about['intro_text'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="experience_years">Years of Experience</label>
                    <input type="number" id="experience_years" name="experience_years" class="form-input"
                           value="<?php echo $about['experience_years'] ?? 10; ?>" min="0">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="mission_text">Mission Statement</label>
                    <textarea id="mission_text" name="mission_text" class="form-textarea" rows="3"
                              placeholder="Our mission..."><?php echo htmlspecialchars($about['mission_text'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="vision_text">Vision Statement</label>
                    <textarea id="vision_text" name="vision_text" class="form-textarea" rows="3"
                              placeholder="Our vision..."><?php echo htmlspecialchars($about['vision_text'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="values_text">Core Values</label>
                    <textarea id="values_text" name="values_text" class="form-textarea" rows="4"
                              placeholder="Our core values..."><?php echo htmlspecialchars($about['values_text'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Save About Content</button>
            </form>
        </div>
    </div>
    
    <!-- Founders -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Team / Founders</h2>
            </div>
            <div class="card-body">
                <?php if (empty($founders)): ?>
                <p style="color: var(--text-muted); margin-bottom: 16px;">No team members added yet.</p>
                <?php else: ?>
                <div style="margin-bottom: 20px;">
                    <?php foreach ($founders as $founder): ?>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg-input); border-radius: 8px; margin-bottom: 8px;">
                        <?php if ($founder['image']): ?>
                        <img src="<?php echo htmlspecialchars($founder['image']); ?>" alt="" 
                             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                            <?php echo strtoupper(substr($founder['name'], 0, 1)); ?>
                        </div>
                        <?php endif; ?>
                        <div style="flex: 1;">
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($founder['name']); ?></div>
                            <div style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($founder['role']); ?></div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" 
                                onclick="editFounder(<?php echo htmlspecialchars(json_encode($founder)); ?>)">Edit</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <button type="button" class="btn btn-primary btn-sm" onclick="editFounder(null)">+ Add Team Member</button>
            </div>
        </div>
    </div>
</div>

<!-- Founder Modal -->
<div id="founderModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color);">
            <h3 id="modalTitle">Add Team Member</h3>
        </div>
        <form method="POST" enctype="multipart/form-data" style="padding: 20px;">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="founder">
            <input type="hidden" name="founder_id" id="founder_id" value="">
            
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="founder_name" id="founder_name" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Role</label>
                <input type="text" name="founder_role" id="founder_role" class="form-input">
            </div>
            
            <div class="form-group">
                <label class="form-label">Bio</label>
                <textarea name="founder_bio" id="founder_bio" class="form-textarea" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">LinkedIn URL</label>
                <input type="url" name="founder_linkedin" id="founder_linkedin" class="form-input">
            </div>
            
            <div class="form-group">
                <label class="form-label">Photo</label>
                <input type="file" name="founder_image" class="form-input" accept="image/*">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Display Order</label>
                    <input type="number" name="founder_order" id="founder_order" class="form-input" value="0" min="0">
                </div>
                <div class="form-group">
                    <label class="form-checkbox" style="margin-top: 30px;">
                        <input type="checkbox" name="founder_active" id="founder_active" value="1" checked>
                        <span>Active</span>
                    </label>
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" name="action" value="delete_founder" id="deleteBtn" 
                        class="btn btn-danger" style="margin-left: auto; display: none;"
                        onclick="return confirm('Delete this team member?')">Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
function editFounder(data) {
    const modal = document.getElementById('founderModal');
    modal.style.display = 'flex';
    
    if (data) {
        document.getElementById('modalTitle').textContent = 'Edit Team Member';
        document.getElementById('founder_id').value = data.id;
        document.getElementById('founder_name').value = data.name;
        document.getElementById('founder_role').value = data.role || '';
        document.getElementById('founder_bio').value = data.bio || '';
        document.getElementById('founder_linkedin').value = data.linkedin || '';
        document.getElementById('founder_order').value = data.display_order || 0;
        document.getElementById('founder_active').checked = data.is_active == 1;
        document.getElementById('deleteBtn').style.display = 'block';
    } else {
        document.getElementById('modalTitle').textContent = 'Add Team Member';
        document.getElementById('founder_id').value = '';
        document.getElementById('founder_name').value = '';
        document.getElementById('founder_role').value = '';
        document.getElementById('founder_bio').value = '';
        document.getElementById('founder_linkedin').value = '';
        document.getElementById('founder_order').value = 0;
        document.getElementById('founder_active').checked = true;
        document.getElementById('deleteBtn').style.display = 'none';
    }
}

function closeModal() {
    document.getElementById('founderModal').style.display = 'none';
}

document.getElementById('founderModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

<?php require_once 'includes/footer.php'; ?>

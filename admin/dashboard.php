<?php
/**
 * StructZone Admin - Dashboard
 */

$pageTitle = 'Dashboard';
require_once 'includes/header.php';

// Get stats
$totalProjects = getCount('sz_projects');
$totalServices = getCount('sz_services', 'is_active = 1');
$totalInsights = getCount('sz_insights', "status = 'published'");
$newEnquiries = getCount('sz_enquiries', "status = 'new'");

// Get recent enquiries
$pdo = getDB();
$recentEnquiries = $pdo->query("SELECT * FROM sz_enquiries ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Get recent activity
$recentActivity = $pdo->query("SELECT al.*, u.name as user_name FROM sz_activity_log al 
                               LEFT JOIN sz_users u ON al.user_id = u.id 
                               ORDER BY al.created_at DESC LIMIT 8")->fetchAll();
?>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
            </div>
        </div>
        <div class="stat-value"><?php echo $totalProjects; ?></div>
        <div class="stat-label">Total Projects</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                </svg>
            </div>
        </div>
        <div class="stat-value"><?php echo $totalServices; ?></div>
        <div class="stat-label">Active Services</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
            </div>
        </div>
        <div class="stat-value"><?php echo $totalInsights; ?></div>
        <div class="stat-label">Published Insights</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
            </div>
        </div>
        <div class="stat-value"><?php echo $newEnquiries; ?></div>
        <div class="stat-label">New Enquiries</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Quick Actions</h2>
        </div>
        <div class="card-body">
            <div class="quick-actions">
                <a href="project-edit.php" class="quick-action">
                    <div class="quick-action-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </div>
                    <span class="quick-action-text">Add Project</span>
                </a>
                
                <a href="insight-edit.php" class="quick-action">
                    <div class="quick-action-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </div>
                    <span class="quick-action-text">Add Insight</span>
                </a>
                
                <a href="homepage.php" class="quick-action">
                    <div class="quick-action-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </div>
                    <span class="quick-action-text">Edit Homepage</span>
                </a>
                
                <a href="enquiries.php" class="quick-action">
                    <div class="quick-action-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <span class="quick-action-text">View Enquiries</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Recent Enquiries -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Enquiries</h2>
            <a href="enquiries.php" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (empty($recentEnquiries)): ?>
            <div class="empty-state" style="padding: 40px;">
                <p>No enquiries yet</p>
            </div>
            <?php else: ?>
            <ul class="activity-list" style="padding: 0 24px;">
                <?php foreach ($recentEnquiries as $enquiry): ?>
                <li class="activity-item">
                    <div class="activity-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">
                            <strong><?php echo htmlspecialchars($enquiry['name']); ?></strong>
                            <?php if ($enquiry['status'] === 'new'): ?>
                            <span class="badge badge-warning" style="margin-left: 8px;">New</span>
                            <?php endif; ?>
                        </div>
                        <div class="activity-time"><?php echo formatDateTime($enquiry['created_at']); ?></div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

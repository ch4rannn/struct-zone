<?php
$current_page = 'services';
$page_title = 'Professional Structural Engineering Services | StructZone';
$meta_description = 'Expert structural engineering services including design, analysis, BIM, foundation design, inspection, and structural detailing for commercial, residential, and industrial projects.';
$canonical_url = 'https://www.structzone.com/services/';

require_once '../includes/site-data.php';
require_once '../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?php echo url(); ?>">Home</a>
            <span>/</span>
            <span>Services</span>
        </nav>
        <h1>Professional Structural Engineering Services</h1>
        <p>Expert design, analysis, and documentation for safe, code-compliant structures.</p>
    </div>
</section>

<!-- Services Grid -->
<section class="section">
    <div class="container">
        <div class="services-page-grid">
            <?php 
            $services = getSiteServices();
            if (!empty($services)):
                foreach ($services as $svc): 
            ?>
            <a href="<?php echo url('services/' . $svc['slug'] . '.php'); ?>" class="service-page-card fade-in">
                <div class="service-page-icon">
                    <?php echo getIconSvg($svc['icon'] ?? 'default'); ?>
                </div>
                <div class="service-page-content">
                    <h2><?php echo htmlspecialchars($svc['title']); ?></h2>
                    <p><?php echo htmlspecialchars($svc['description']); ?></p>
                    <span class="service-page-link">Learn more →</span>
                </div>
            </a>
            <?php 
                endforeach;
            else:
            ?>
            <p>No services available yet.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Why Choose Section -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2>Why Choose StructZone</h2>
            <p>What sets our structural engineering services apart.</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-item fade-in">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <h3>Code Compliant</h3>
                <p>All designs meet IS codes and international standards.</p>
            </div>
            
            <div class="feature-item fade-in">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h3>On-Time Delivery</h3>
                <p>Projects delivered on schedule with clear milestones.</p>
            </div>
            
            <div class="feature-item fade-in">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <h3>Cost Optimized</h3>
                <p>Efficient designs that minimize material costs.</p>
            </div>
            
            <div class="feature-item fade-in">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <h3>Quality Assured</h3>
                <p>Checked and verified output for error-free execution.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container">
        <h2>Ready to Start Your Project?</h2>
        <p>Contact us to discuss your structural engineering requirements.</p>
        <a href="../contact.php" class="btn btn-primary btn-lg">Get a Quote</a>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>

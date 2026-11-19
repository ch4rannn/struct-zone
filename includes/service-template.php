<?php
/**
 * Service Page Template - Dark Navy & Buttons
 * Expects $service_slug to be set before including
 */

require_once __DIR__ . '/site-data.php';

// Get service data
$service = getSiteService($service_slug);

// 404 if not found
if (!$service) {
    http_response_code(404);
    $page_title = 'Service Not Found';
    require_once __DIR__ . '/header.php';
    echo '<div class="container" style="padding: 100px 0; text-align: center;">';
    echo '<h1>404 - Service Not Found</h1>';
    echo '<p>The service you are looking for does not exist or has been moved.</p>';
    echo '<a href="' . url('services') . '" class="btn btn-primary" style="margin-top: 20px;">View All Services</a>';
    echo '</div>';
    require_once __DIR__ . '/footer.php';
    exit;
}

// Page Metadata
$page_title = $service['title'];
$current_page = 'services';
$meta_description = strip_tags($service['description']);

// Get all services
$allServices = getSiteServices();

require_once __DIR__ . '/header.php';
?>

<style>
/* Professional Engineering Theme - Dark Navy & White Buttons */
:root {
    --brand-primary: #0A2540; /* Navy Dark - Matching Footer/Site Dark */
    --brand-secondary: #4A4A4A; /* Steel Gray */
    --brand-accent: #1F6AE1; /* Construction Safety Blue */
    --brand-bg-light: #F8FAFC;
    --brand-text-dark: #0F172A;
    --brand-text-muted: #64748B;
}

/* Force Header Contact Button to be Dark Navy */
.nav-cta {
    background-color: var(--brand-primary) !important;
    color: #fff !important;
    border-radius: 4px; /* Ensure matches style */
}
.nav-cta:hover {
    background-color: #0F172A !important; /* Even darker on hover */
}

/* Hero Section - Dark Navy */
.service-hero {
    background: var(--brand-primary); /* Dark Navy */
    padding: 80px 0 60px;
    border-bottom: none;
    color: #fff;
}

.service-hero .breadcrumb {
    margin-bottom: 20px;
    font-size: 0.9rem;
}

.service-hero .breadcrumb a,
.service-hero .breadcrumb span {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
}

.service-hero .breadcrumb a:hover {
    color: #fff;
}

.service-hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
}

.service-hero-text h1 {
    font-size: 3rem;
    color: #fff;
    margin-bottom: 20px;
    font-weight: 700;
    line-height: 1.2;
}

.service-hero-text p {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 30px;
    line-height: 1.6;
}

.service-hero-cta {
    display: flex;
    gap: 15px;
}

/* Button Styles */
.btn-primary {
    background-color: var(--brand-primary) !important; /* Navy Buttons Globally */
    border-color: var(--brand-primary) !important;
    color: #fff !important;
    font-weight: 600;
    padding: 12px 28px;
    border-radius: 4px;
}

.btn-primary:hover {
    background-color: #020617 !important;
    border-color: #020617 !important;
}

/* White Button for Dark Backgrounds */
.btn-white {
    background-color: #fff !important;
    color: var(--brand-primary) !important;
    border: 1px solid #fff;
    padding: 12px 28px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    transition: all 0.2s ease;
}

.btn-white:hover {
    background-color: #f1f5f9 !important;
    transform: translateY(-1px);
}

.btn-outline-white {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.3);
    color: #fff !important;
    padding: 12px 28px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-outline-white:hover {
    background: rgba(255,255,255,0.1);
    border-color: #fff;
}

.service-hero-image img {
    width: 100%;
    border-radius: 8px;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.1);
}

.placeholder-box {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    padding: 80px 40px;
    text-align: center;
    color: #fff;
    backdrop-filter: blur(10px);
}

.placeholder-box svg {
    width: 64px;
    height: 64px;
    margin-bottom: 20px;
    opacity: 0.9;
}

/* Features Section */
.service-features {
    padding: 80px 0;
    background: #fff;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.feature-card {
    padding: 30px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
    border-color: var(--brand-primary); /* Navy Border */
    box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05);
}

.feature-icon {
    width: 50px;
    height: 50px;
    background: #f1f5f9;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    color: var(--brand-primary);
}

.feature-icon svg {
    width: 24px;
    height: 24px;
}

.feature-card h3 {
    font-size: 1.25rem;
    margin-bottom: 10px;
    color: var(--brand-text-dark);
}

.feature-card p {
    color: var(--brand-text-muted);
    font-size: 0.95rem;
    line-height: 1.6;
}

/* Content Section */
.content-section {
    padding: 80px 0;
    background: #f8fafc;
}

.content-wrapper {
    max-width: 800px;
    margin: 0 auto;
}

.content-wrapper h2 {
    color: var(--brand-text-dark);
    margin-bottom: 20px;
}

.content-wrapper p {
    color: var(--brand-text-muted);
    line-height: 1.8;
    margin-bottom: 20px;
}

/* Process Section */
.process-section {
    padding: 80px 0;
    background: #fff;
}

.section-title {
    text-align: center;
    margin-bottom: 50px;
    font-size: 2.25rem;
    color: var(--brand-primary);
}

.process-steps {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    text-align: center;
}

.step-number {
    width: 48px;
    height: 48px;
    background: var(--brand-primary);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-weight: bold;
    font-size: 1.1rem;
    box-shadow: 0 4px 6px -1px rgba(10, 37, 64, 0.2);
}

.process-steps h3 {
    color: var(--brand-text-dark);
    margin-bottom: 10px;
}

.process-steps p {
    color: var(--brand-text-muted);
    font-size: 0.9rem;
}

/* Other Services */
.other-services {
    padding: 60px 0;
    background: #fff;
    border-top: 1px solid #e2e8f0;
}

.other-services-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.other-card {
    display: block;
    padding: 20px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.other-card:hover {
    border-color: var(--brand-primary);
    background: #f8fafc;
}

.other-card.active {
    background: var(--brand-primary);
    border-color: var(--brand-primary);
}

.other-card.active h4, .other-card.active p {
    color: #fff;
}

.other-card h4 {
    font-size: 1rem;
    color: var(--brand-text-dark);
    margin-bottom: 5px;
}

.other-card p {
    font-size: 0.85rem;
    color: var(--brand-text-muted);
    margin: 0;
}

/* CTA */
.cta-section {
    padding: 80px 0;
    background: var(--brand-primary); /* Dark Navy */
    text-align: center;
    color: #fff;
}

.cta-section h2 {
    font-size: 2.5rem;
    margin-bottom: 20px;
}

.cta-section .btn-white {
    margin-top: 20px;
}

/* Mobile */
@media (max-width: 768px) {
    .service-hero-content, .features-grid, .process-steps, .other-services-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    .service-hero-text h1 {
        font-size: 2rem;
    }
}
</style>

<!-- Hero -->
<section class="service-hero">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?php echo url(); ?>">Home</a>
            <span>/</span>
            <a href="<?php echo url('services'); ?>">Services</a>
            <span>/</span>
            <span><?php echo htmlspecialchars($service['title']); ?></span>
        </nav>
        
        <div class="service-hero-content">
            <div class="service-hero-text">
                <h1><?php echo htmlspecialchars($service['title']); ?></h1>
                <p><?php echo htmlspecialchars($service['description']); ?></p>
                <div class="service-hero-cta">
                    <a href="<?php echo url('contact.php'); ?>" class="btn-white">Get a Quote</a>
                    <a href="tel:+919876543210" class="btn btn-outline-white">Call Us</a>
                </div>
            </div>
            <div class="service-hero-image">
                <?php if (!empty($service['image'])): ?>
                    <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>">
                <?php else: ?>
                    <div class="placeholder-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 21h18M5 21V7l8-4 8 4v14M9 21v-4a2 2 0 012-2h2a2 2 0 012 2v4"/>
                        </svg>
                        <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="service-features">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <h3>Detailed Reports</h3>
                <p>Comprehensive documentation and analysis for your project requirements.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Code Compliant</h3>
                <p>All designs strictly adhere to IS codes and safety standards.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3>Timely Delivery</h3>
                <p>We respect deadlines and ensure projects are completed on schedule.</p>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<?php if (!empty($service['content'])): ?>
<section class="content-section">
    <div class="container">
        <div class="content-wrapper">
            <?php echo $service['content']; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Process -->
<section class="process-section">
    <div class="container">
        <h2 class="section-title">Our Process</h2>
        <div class="process-steps">
            <div>
                <div class="step-number">1</div>
                <h3>Consult</h3>
                <p>Discuss requirements</p>
            </div>
            <div>
                <div class="step-number">2</div>
                <h3>Plan</h3>
                <p>Strategic analysis</p>
            </div>
            <div>
                <div class="step-number">3</div>
                <h3>Design</h3>
                <p>Detailed engineering</p>
            </div>
            <div>
                <div class="step-number">4</div>
                <h3>Deliver</h3>
                <p>Final output</p>
            </div>
        </div>
    </div>
</section>

<!-- Other Services -->
<section class="other-services">
    <div class="container">
        <h3 style="margin-bottom: 30px; text-align: center;">Explore Other Services</h3>
        <div class="other-services-grid">
            <?php 
            $count = 0;
            foreach ($allServices as $s): 
                if ($count >= 4) break;
            ?>
                <a href="<?php echo url('services/' . $s['slug'] . '.php'); ?>" 
                   class="other-card <?php echo $s['slug'] === $service_slug ? 'active' : ''; ?>">
                    <h4><?php echo htmlspecialchars($s['title']); ?></h4>
                    <p>View Details →</p>
                </a>
            <?php 
                $count++;
            endforeach; 
            ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Ready to Start?</h2>
        <p>Get professional structural engineering support today.</p>
        <a href="<?php echo url('contact.php'); ?>" class="btn-white">Request a Quote</a>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>

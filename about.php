<?php
$current_page = 'about';
$page_title = 'About StructZone | Structural Engineering Experts';
$meta_description = 'StructZone is a team of professional structural engineers providing safe, code-compliant structural design, analysis, and on-site support.';
$canonical_url = 'https://www.structzone.com/about.php';

require_once 'includes/site-data.php';
require_once 'includes/header.php';

// Get founders from database
$founders = getSiteFounders();
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?php echo url(); ?>">Home</a>
            <span>/</span>
            <span>About</span>
        </nav>
        <h1>About StructZone</h1>
        <p>Structural Experts. Stronger Buildings Guaranteed.</p>
    </div>
</section>

<!-- About Content -->
<section class="section">
    <div class="container">
        <div class="about-intro">
            <div class="about-text fade-in">
                <h2>Engineering Safety, Stability, and Sustainability</h2>
                <p>With 5 years of experience in structural engineering, we design buildings and infrastructure that prioritize safety, durability, and cost-effectiveness.</p>
                <p>Our team specializes in structural analysis, foundation design, seismic engineering, and building assessment for residential, commercial, and industrial projects.</p>
                <p style="margin-top: var(--space-lg);"><strong>SAFETY • STRENGTH • PRECISION</strong></p>
            </div>
        </div>
    </div>
</section>

<!-- What We Do -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2>What We Do</h2>
            <p>Complete structural engineering solutions for your projects.</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-item fade-in">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                    </svg>
                </div>
                <h3>Structural Design</h3>
                <p>Safe and efficient RCC/steel structures with complete load calculations.</p>
            </div>
            
            <div class="feature-item fade-in">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polyline points="2 17 12 22 22 17"></polyline>
                    </svg>
                </div>
                <h3>Foundation Design</h3>
                <p>Customized foundation solutions based on soil conditions and loads.</p>
            </div>
            
            <div class="feature-item fade-in">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
                <h3>Inspection & Audit</h3>
                <p>Building inspections for cracks, settlement, and safety risks.</p>
            </div>
            
            <div class="feature-item fade-in">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                </div>
                <h3>Structural Detailing</h3>
                <p>Accurate drawings, reinforcement details, and BBS for construction.</p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Why Choose StructZone</h2>
            <p>What makes us different from other structural engineering firms.</p>
        </div>
        
        <div class="grid-3">
            <div class="card fade-in" style="text-align: center; padding: var(--space-2xl);">
                <h3 style="font-size: var(--font-size-4xl); color: var(--color-accent); margin-bottom: var(--space-sm);">5+</h3>
                <p>Years of Experience</p>
            </div>
            
            <div class="card fade-in" style="text-align: center; padding: var(--space-2xl);">
                <h3 style="font-size: var(--font-size-4xl); color: var(--color-accent); margin-bottom: var(--space-sm);">100+</h3>
                <p>Projects Completed</p>
            </div>
            
            <div class="card fade-in" style="text-align: center; padding: var(--space-2xl);">
                <h3 style="font-size: var(--font-size-4xl); color: var(--color-accent); margin-bottom: var(--space-sm);">50+</h3>
                <p>Happy Clients</p>
            </div>
        </div>
    </div>
</section>

<!-- Founders Section -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2>Meet the Founders</h2>
            <p>The visionaries behind StructZone's engineering excellence.</p>
        </div>
        
        <div class="team-grid">
            <?php if (!empty($founders)): ?>
                <?php foreach ($founders as $founder): ?>
                <div class="team-card fade-in">
                    <div class="team-image">
                        <?php 
                        // Fix image path for frontend (remove ../ prefix if present)
                        $imagePath = $founder['image'] ?? '';
                        if (strpos($imagePath, '../') === 0) {
                            $imagePath = substr($imagePath, 3);
                        }
                        if (empty($imagePath)) {
                            $imagePath = 'assets/images/placeholder.png';
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($founder['name']); ?>" loading="lazy">
                    </div>
                    <div class="team-content">
                        <h3><?php echo htmlspecialchars($founder['name']); ?></h3>
                        <span class="team-role"><?php echo htmlspecialchars($founder['role']); ?></span>
                        <p><?php echo htmlspecialchars($founder['bio']); ?></p>
                        <?php if (!empty($founder['linkedin'])): ?>
                        <div class="team-social">
                            <a href="<?php echo htmlspecialchars($founder['linkedin']); ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback static content if database unavailable -->
                <div class="team-card fade-in">
                    <div class="team-image">
                        <img src="assets/images/sadhusen-ansari.png" alt="Sadhusen Ansari" loading="lazy">
                    </div>
                    <div class="team-content">
                        <h3>Sadhusen Ansari</h3>
                        <span class="team-role">Co-Founder</span>
                        <p>Experienced structural engineer with expertise in high-rise building design and seismic analysis.</p>
                    </div>
                </div>
                <div class="team-card fade-in">
                    <div class="team-image">
                        <img src="assets/images/bajrang-sah.png" alt="Bajrang Sah" loading="lazy">
                    </div>
                    <div class="team-content">
                        <h3>Bajrang Sah</h3>
                        <span class="team-role">Co-Founder</span>
                        <p>Specialist in industrial structures and foundation engineering with a focus on cost-effective solutions.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Values -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Our Values</h2>
            <p>The principles that guide our work.</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-item fade-in">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <h3>Safety</h3>
                <p>Every design prioritizes structural safety and code compliance.</p>
            </div>
            
            <div class="feature-item fade-in">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <h3>Quality</h3>
                <p>Accurate, detailed work that meets professional standards.</p>
            </div>
            
            <div class="feature-item fade-in">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h3>Reliability</h3>
                <p>On-time delivery and clear communication throughout.</p>
            </div>
            
            <div class="feature-item fade-in">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <h3>Value</h3>
                <p>Cost-effective solutions without compromising on strength.</p>
            </div>
        </div>
    </div>
</section>

<!-- Technology Stack -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2>Our Technology Stack</h2>
            <p>Industry-leading software for precise structural engineering.</p>
        </div>
        
        <div class="tech-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--space-md); text-align: center;">
            <div class="tech-item fade-in" style="padding: var(--space-lg); background: white; border-radius: 8px; box-shadow: var(--shadow-sm);">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-sm);">📐</div>
                <h4 style="color: var(--color-primary); margin-bottom: 4px;">ETABS & SAP2000</h4>
                <p style="font-size: var(--font-size-sm); color: var(--color-text-muted); margin: 0;">Structural Analysis</p>
            </div>
            <div class="tech-item fade-in" style="padding: var(--space-lg); background: white; border-radius: 8px; box-shadow: var(--shadow-sm);">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-sm);">🏗️</div>
                <h4 style="color: var(--color-primary); margin-bottom: 4px;">STAAD.Pro</h4>
                <p style="font-size: var(--font-size-sm); color: var(--color-text-muted); margin: 0;">Steel & Concrete Design</p>
            </div>
            <div class="tech-item fade-in" style="padding: var(--space-lg); background: white; border-radius: 8px; box-shadow: var(--shadow-sm);">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-sm);">💻</div>
                <h4 style="color: var(--color-primary); margin-bottom: 4px;">Revit & Tekla</h4>
                <p style="font-size: var(--font-size-sm); color: var(--color-text-muted); margin: 0;">BIM Modeling</p>
            </div>
            <div class="tech-item fade-in" style="padding: var(--space-lg); background: white; border-radius: 8px; box-shadow: var(--shadow-sm);">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-sm);">✏️</div>
                <h4 style="color: var(--color-primary); margin-bottom: 4px;">AutoCAD</h4>
                <p style="font-size: var(--font-size-sm); color: var(--color-text-muted); margin: 0;">Structural Drafting</p>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: var(--space-lg);">
            <p style="color: var(--color-text-secondary); font-size: var(--font-size-sm);">
                <strong>Compliant with:</strong> IS 456, IS 800, IS 1893 (Seismic), IS 875, ACI, AISC Standards
            </p>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container">
        <h2>Let's Work Together</h2>
        <p>Contact us to discuss your next structural engineering project.</p>
        <a href="contact.php" class="btn btn-primary btn-lg">Get in Touch</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

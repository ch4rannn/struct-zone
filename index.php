<?php
$current_page = 'home';
$page_title = 'Structural Engineering & BIM Services India';
$meta_description = 'Expert structural design, BIM modeling, and engineering analysis for developers, architects, and contractors. Reliable consultancy across India.';
$canonical_url = 'https://www.structzone.com';

require_once 'includes/site-data.php';
require_once 'includes/header.php';

// Get homepage settings from admin panel
$homepage = getHomepageSettings();

// Hero content - use database values or fallbacks
$heroHeading = $homepage['hero_heading'] ?? 'Structural Engineering That Protects Your Investment';
$heroSubheading = $homepage['hero_subheading'] ?? 'Expert design and analysis services for residential, commercial, and industrial projects across India. Safety • Strength • Precision.';
$ctaText = $homepage['cta_text'] ?? 'Get a Consultation';
$ctaLink = $homepage['cta_link'] ?? 'contact.php';
$heroBackground = $homepage['hero_background'] ?? 'assets/videos/hero-bg.mp4';

// Detect media type
$isYouTube = (strpos($heroBackground, 'youtube.com') !== false || strpos($heroBackground, 'youtu.be') !== false);
$isVideo = preg_match('/\.(mp4|webm|mov|avi)$/i', $heroBackground);
$isImage = preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $heroBackground);

// Extract YouTube video ID if it's a YouTube URL
$youtubeId = '';
if ($isYouTube) {
    if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $heroBackground, $matches)) {
        $youtubeId = $matches[1];
    }
}
?>

<!-- Hero Section -->
<section class="hero hero-video">
    <!-- Video/Image Background -->
    <div class="hero-video-bg">
        <?php if ($isYouTube && $youtubeId): ?>
            <!-- YouTube Embed Background -->
            <iframe 
                class="hero-youtube-bg"
                src="https://www.youtube.com/embed/<?php echo $youtubeId; ?>?autoplay=1&mute=1&loop=1&playlist=<?php echo $youtubeId; ?>&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1"
                frameborder="0"
                allow="autoplay; fullscreen"
                allowfullscreen
            ></iframe>
        <?php elseif ($isImage): ?>
            <!-- Image Background -->
            <img src="<?php echo htmlspecialchars($heroBackground); ?>" alt="Hero Background" class="hero-video-element" loading="eager">
        <?php else: ?>
            <!-- Local Video -->
            <video 
                autoplay 
                muted 
                loop 
                playsinline 
                preload="metadata"
                poster="assets/images/hero-bg.jpg"
                class="hero-video-element"
            >
                <source src="<?php echo htmlspecialchars($heroBackground); ?>" type="video/mp4">
                <source src="assets/videos/hero-bg.webm" type="video/webm">
            </video>
        <?php endif; ?>
        <!-- Static fallback -->
        <img src="assets/images/project-1.jpg" alt="Structural engineering" class="hero-video-fallback" loading="eager">
    </div>
    <!-- Overlay with centered content -->
    <div class="hero-overlay">
        <div class="hero-content">
            <h1><?php echo htmlspecialchars($heroHeading); ?></h1>
            <p><?php echo htmlspecialchars($heroSubheading); ?></p>
            <div class="hero-actions">
                <a href="<?php echo htmlspecialchars($ctaLink); ?>" class="btn btn-primary btn-lg"><?php echo htmlspecialchars($ctaText); ?></a>
                <a href="projects/" class="btn btn-outline btn-lg">View Our Projects</a>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2>Our Services</h2>
            <p>Safe, code-compliant structural solutions for every project.</p>
        </div>
        
        <div class="services-grid">
            <?php 
            $services = getSiteServices();
            if (!empty($services)):
                foreach ($services as $svc): 
            ?>
            <a href="services/<?php echo $svc['slug']; ?>.php" class="card service-card">
                <div class="card-icon">
                    <?php echo getIconSvg($svc['icon'] ?? 'default'); ?>
                </div>
                <h3><?php echo htmlspecialchars($svc['title']); ?></h3>
                <p><?php echo htmlspecialchars($svc['description']); ?></p>
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

<!-- Features Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Engineering Safety, Stability, and Sustainability</h2>
            <p>With 5 years of experience in structural engineering, we design buildings and infrastructure that prioritize safety, durability, and cost-effectiveness.</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <h3>Safety First</h3>
                <p>Code-compliant designs that prioritize structural safety and long-term stability.</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h3>Timely Delivery</h3>
                <p>On-time project delivery with clear milestones and smooth coordination.</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <h3>Cost-Effective</h3>
                <p>Optimized designs that reduce material costs without compromising strength.</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <h3>Quality Assurance</h3>
                <p>Detailed drawings and specifications for error-free site execution.</p>
            </div>
        </div>
    </div>
</section>

<!-- Projects Section - Video Gallery Carousel -->
<section class="section section-alt gallery-section">
    <div class="container">
        <div class="gallery-header">
            <div class="gallery-header-text">
                <h2>Featured Projects</h2>
                <p>Discover our structural engineering work across residential, commercial, and industrial projects.</p>
            </div>
            <div class="gallery-nav">
                <button class="gallery-btn" id="galleryPrev" aria-label="Previous slide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button class="gallery-btn" id="galleryNext" aria-label="Next slide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <div class="gallery-carousel" id="galleryCarousel">
        <div class="gallery-track" id="galleryTrack">
            <?php 
            $projects = getSiteProjects();
            if (!empty($projects)):
                foreach ($projects as $project): 
                    $videoSrc = !empty($project['video_url']) ? $project['video_url'] : '';
                    $posterImg = !empty($project['featured_image']) ? $project['featured_image'] : 'assets/images/project-1.jpg';
                    $projectLink = 'projects/view.php?slug=' . $project['slug'];
            ?>
            <a href="<?php echo $projectLink; ?>" class="gallery-card gallery-video-card">
                <div class="gallery-media-wrapper">
                    <?php if (!empty($videoSrc) && (strpos($videoSrc, 'youtube') !== false || strpos($videoSrc, 'youtu.be') !== false)): 
                        // Extract YouTube ID
                        preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $videoSrc, $matches);
                        $ytId = $matches[1] ?? '';
                    ?>
                    <iframe class="gallery-video" src="https://www.youtube.com/embed/<?php echo $ytId; ?>?autoplay=1&mute=1&loop=1&playlist=<?php echo $ytId; ?>&controls=0&showinfo=0" frameborder="0" allow="autoplay"></iframe>
                    <?php elseif (!empty($videoSrc)): ?>
                    <video autoplay muted loop playsinline poster="<?php echo htmlspecialchars($posterImg); ?>" class="gallery-video">
                        <source src="<?php echo htmlspecialchars($videoSrc); ?>" type="video/mp4">
                    </video>
                    <?php else: ?>
                    <img src="<?php echo htmlspecialchars($posterImg); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="gallery-video" loading="lazy">
                    <?php endif; ?>
                </div>
                <div class="gallery-card-content">
                    <span class="gallery-card-tag"><?php echo htmlspecialchars($project['category'] ?? 'Project'); ?></span>
                    <h3 class="gallery-card-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p class="gallery-card-desc"><?php echo htmlspecialchars($project['short_desc'] ?? ''); ?></p>
                </div>
            </a>
            <?php 
                endforeach;
            else:
            ?>
            <p style="text-align: center; padding: 40px;">No projects available yet.</p>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="gallery-dots" id="galleryDots"></div>
    
    <div class="container" style="margin-top: var(--space-2xl); text-align: center;">
        <a href="projects/" class="btn btn-outline">View All Projects</a>
    </div>
</section>

<!-- Testimonials Section -->
<?php $clients = getClients(); ?>
<?php if (!empty($clients)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Structural Experts. Stronger Buildings Guaranteed</h2>
            <p>We deliver structural design, analysis, and inspection services that ensure stability, code compliance, and long-term performance.</p>
        </div>
        
        <div class="testimonials-grid">
            <?php foreach ($clients as $client): ?>
            <div class="card testimonial-card">
                <p class="testimonial-quote">"<?php echo htmlspecialchars($client['testimonial']); ?>"</p>
                <div class="testimonial-author">
                    <?php if (!empty($client['image'])): ?>
                    <img src="<?php echo htmlspecialchars($client['image']); ?>" alt="<?php echo htmlspecialchars($client['name']); ?>" class="testimonial-avatar" loading="lazy">
                    <?php else: ?>
                    <div class="testimonial-avatar" style="background: var(--color-accent); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                        <?php echo strtoupper(substr($client['name'], 0, 1)); ?>
                    </div>
                    <?php endif; ?>
                    <div class="testimonial-info">
                        <h4><?php echo htmlspecialchars($client['name']); ?></h4>
                        <span><?php echo !empty($client['company']) ? htmlspecialchars($client['company']) . ' - ' : ''; ?><?php echo htmlspecialchars($client['role']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container">
        <h2>Ready to Discuss Your Project?</h2>
        <p>Let's talk about how we can help bring your vision to life with reliable structural engineering.</p>
        <a href="contact.php" class="btn btn-primary btn-lg">Get in Touch</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

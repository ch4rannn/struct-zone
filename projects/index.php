<?php
$current_page = 'projects';
$page_title = 'Structural Engineering Projects Across India | StructZone';
$meta_description = 'View our portfolio of structural engineering projects including commercial buildings, residential complexes, industrial facilities across Punjab and India.';
$canonical_url = 'https://www.structzone.com/projects/';

require_once '../includes/site-data.php';
require_once '../includes/header.php';

// Get all projects from database
$allProjects = getSiteProjects();

// Get featured project (first one marked as featured, or first project)
$featuredProject = null;
foreach ($allProjects as $project) {
    if (!empty($project['is_featured'])) {
        $featuredProject = $project;
        break;
    }
}
if (!$featuredProject && !empty($allProjects)) {
    $featuredProject = $allProjects[0];
}
?>

<!-- Editorial Intro -->
<section class="projects-editorial-intro">
    <div class="container">
        <h1 class="projects-page-title">Structural Engineering Projects</h1>
        <p class="projects-page-tagline">Proven expertise in commercial, residential, and industrial infrastructure.</p>
    </div>
</section>

<!-- Featured Project -->
<?php if ($featuredProject): 
    $featuredVideoUrl = $featuredProject['video_url'] ?? '';
    $featuredPoster = !empty($featuredProject['featured_image']) ? '../' . $featuredProject['featured_image'] : '../assets/images/project-1.jpg';
    
    // Check if it's a YouTube URL
    $isYouTube = (strpos($featuredVideoUrl, 'youtube.com') !== false || strpos($featuredVideoUrl, 'youtu.be') !== false);
    $youtubeId = '';
    if ($isYouTube) {
        // Extract YouTube video ID
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $featuredVideoUrl, $matches)) {
            $youtubeId = $matches[1];
        }
    }
    $isLocalVideo = !empty($featuredVideoUrl) && !$isYouTube && (
        strpos($featuredVideoUrl, '.mp4') !== false || 
        strpos($featuredVideoUrl, '.webm') !== false ||
        strpos($featuredVideoUrl, 'assets/videos/') !== false
    );
?>
<section class="projects-featured">
    <div class="container container-wide">
        <a href="<?php echo url('projects/view.php?slug=' . $featuredProject['slug']); ?>" class="featured-hero-card">
            <div class="featured-hero-media">
                <?php if ($isYouTube && $youtubeId): ?>
                    <!-- YouTube Video Embed -->
                    <div class="featured-video-wrapper" style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">
                        <iframe 
                            src="https://www.youtube.com/embed/<?php echo $youtubeId; ?>?autoplay=1&mute=1&loop=1&playlist=<?php echo $youtubeId; ?>&controls=0&showinfo=0&rel=0" 
                            style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" 
                            allow="autoplay; encrypted-media" 
                            allowfullscreen>
                        </iframe>
                    </div>
                <?php elseif ($isLocalVideo): ?>
                    <!-- Local Video -->
                    <video muted loop playsinline autoplay poster="<?php echo htmlspecialchars($featuredPoster); ?>">
                        <source src="../<?php echo htmlspecialchars($featuredVideoUrl); ?>" type="video/mp4">
                    </video>
                <?php elseif (!empty($featuredProject['featured_image'])): ?>
                    <img src="<?php echo htmlspecialchars($featuredPoster); ?>" alt="<?php echo htmlspecialchars($featuredProject['title']); ?>" loading="eager">
                <?php else: ?>
                    <img src="../assets/images/project-1.jpg" alt="Project" loading="eager">
                <?php endif; ?>
            </div>
            <div class="featured-hero-content">
                <span class="featured-hero-category">Featured</span>
                <h2 class="featured-hero-title"><?php echo htmlspecialchars($featuredProject['title']); ?><?php echo !empty($featuredProject['location']) ? ', ' . htmlspecialchars($featuredProject['location']) : ''; ?></h2>
                <p class="featured-hero-desc"><?php echo htmlspecialchars($featuredProject['short_desc'] ?? ''); ?></p>
                <span class="featured-hero-link">View Project →</span>
            </div>
        </a>
    </div>
</section>
<?php endif; ?>

<!-- Category Filters -->
<section class="projects-filters-section">
    <div class="container">
        <nav class="projects-filter-nav" role="navigation" aria-label="Project categories">
            <button class="projects-filter-btn active" data-filter="all">All</button>
            <button class="projects-filter-btn" data-filter="commercial">Commercial</button>
            <button class="projects-filter-btn" data-filter="residential">Residential</button>
            <button class="projects-filter-btn" data-filter="industrial">Industrial</button>
            <button class="projects-filter-btn" data-filter="institutional">Institutional</button>
            <button class="projects-filter-btn" data-filter="infrastructure">Infrastructure</button>
        </nav>
    </div>
</section>

<!-- Project Grid: Dynamic from Database -->
<section class="projects-grid-section">
    <div class="container">
        <div class="projects-editorial-grid" id="projectsGrid">
            
            <?php if (!empty($allProjects)): ?>
                <?php foreach ($allProjects as $project): 
                    $categorySlug = strtolower($project['category'] ?? 'commercial');
                    // Fix poster image path - add ../ prefix for local paths
                    $posterImg = !empty($project['featured_image']) ? '../' . $project['featured_image'] : '../assets/images/project-1.jpg';
                    $videoUrl = !empty($project['video_url']) ? $project['video_url'] : '';
                    $projectLink = url('projects/view.php?slug=' . $project['slug']);
                    
                    // Fix video URL - only add ../ for local paths, not for external URLs
                    $isExternalVideo = (strpos($videoUrl, 'http://') === 0 || strpos($videoUrl, 'https://') === 0 || strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false);
                    $videoSrc = $isExternalVideo ? '' : '../' . $videoUrl; // Skip external URLs for now
                    $hasLocalVideo = !empty($videoUrl) && !$isExternalVideo;
                ?>
                <!-- Project Card -->
                <a href="<?php echo $projectLink; ?>" class="project-editorial-card reveal-on-scroll" data-category="<?php echo htmlspecialchars($categorySlug); ?>">
                    <div class="project-card-media">
                        <?php if ($hasLocalVideo): ?>
                            <video muted loop playsinline autoplay poster="<?php echo htmlspecialchars($posterImg); ?>" class="lazy-video">
                                <source src="<?php echo htmlspecialchars($videoSrc); ?>" type="video/mp4">
                            </video>
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($posterImg); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="lazy-image" loading="lazy">
                        <?php endif; ?>
                    </div>
                    <div class="project-card-info">
                        <span class="project-card-category"><?php echo htmlspecialchars($project['category'] ?? 'Project'); ?></span>
                        <h3 class="project-card-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                        <p class="project-card-desc"><?php echo htmlspecialchars($project['short_desc'] ?? ''); ?></p>
                    </div>
                </a>

                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; padding: 60px 20px; grid-column: 1 / -1;">No projects available yet. Add projects from the admin panel.</p>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="projects-cta">
    <div class="container">
        <div class="projects-cta-content">
            <h2>Have a Project in Mind?</h2>
            <p>Let's discuss how we can help with your structural engineering needs.</p>
            <a href="../contact.php" class="projects-cta-btn">Get in Touch</a>
        </div>
    </div>
</section>

<!-- Filter & Scroll Reveal JS -->
<script>
(function() {
    // Video hover autoplay functionality
    const projectCards = document.querySelectorAll('.project-editorial-card');
    projectCards.forEach(card => {
        const video = card.querySelector('video');
        if (video) {
            card.addEventListener('mouseenter', () => {
                video.play().catch(() => {});
            });
            card.addEventListener('mouseleave', () => {
                video.pause();
                video.currentTime = 0;
            });
        }
    });

    // Filter functionality
    const filterBtns = document.querySelectorAll('.projects-filter-btn');
    const cards = document.querySelectorAll('.project-editorial-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const filter = btn.dataset.filter;
            
            cards.forEach(card => {
                if (filter === 'all' || card.dataset.category === filter) {
                    card.style.display = 'block';
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 50);
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
    
    // Scroll Reveal
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const revealElements = document.querySelectorAll('.reveal-on-scroll');
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        
        revealElements.forEach(el => revealObserver.observe(el));
    }
})();
</script>

<?php require_once '../includes/footer.php'; ?>

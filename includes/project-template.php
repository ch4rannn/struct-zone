<?php
/**
 * Project Page Template - Premium Design
 * Expects $project_slug to be set before including
 */

require_once __DIR__ . '/site-data.php';

// Get project data
$project = getSiteProject($project_slug);

// 404 if not found
if (!$project) {
    http_response_code(404);
    $page_title = 'Project Not Found';
    require_once __DIR__ . '/header.php';
    echo '<div class="container" style="padding: 100px 0; text-align: center;">';
    echo '<h1>404 - Project Not Found</h1>';
    echo '<p>The project you are looking for does not exist or has been moved.</p>';
    echo '<a href="' . url('projects') . '" class="btn btn-primary" style="margin-top: 20px;">View All Projects</a>';
    echo '</div>';
    require_once __DIR__ . '/footer.php';
    exit;
}

// Page Metadata
$page_title = $project['title'] . ' | StructZone Projects';
$current_page = 'projects';
$meta_description = strip_tags($project['short_desc'] ?? $project['title']);

require_once __DIR__ . '/header.php';

// Prepare video data
$videoUrl = $project['video_url'] ?? '';
$isYouTube = (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false);
$youtubeId = '';
if ($isYouTube && preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
    $youtubeId = $matches[1];
}
$isLocalVideo = !empty($videoUrl) && !$isYouTube && (strpos($videoUrl, '.mp4') !== false || strpos($videoUrl, '.webm') !== false);
?>

<style>
/* ========================================
   PREMIUM PROJECT DETAIL STYLES
   ======================================== */

/* Hero Section with Full-Width Image/Video */
.project-hero {
    position: relative;
    width: 100%;
    height: 60vh;
    min-height: 400px;
    max-height: 600px;
    overflow: hidden;
    background: linear-gradient(135deg, #0B3C5D 0%, #1a5a8a 100%);
}

.project-hero-media {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.project-hero-media img,
.project-hero-media video,
.project-hero-media iframe {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.project-hero-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(11, 60, 93, 0.95) 0%, rgba(11, 60, 93, 0.7) 50%, transparent 100%);
    padding: 80px 0 40px;
}

.project-hero-content {
    max-width: 900px;
}

.project-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
    font-size: 0.875rem;
}

.project-breadcrumb a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: color 0.3s ease;
}

.project-breadcrumb a:hover {
    color: #fff;
}

.project-breadcrumb span {
    color: rgba(255, 255, 255, 0.5);
}

.project-category-badge {
    display: inline-block;
    background: linear-gradient(135deg, #D65A31 0%, #e86f4a 100%);
    color: #fff;
    padding: 6px 16px;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 16px;
}

.project-hero-title {
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 700;
    color: #fff;
    margin-bottom: 12px;
    line-height: 1.2;
}

.project-hero-location {
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(255, 255, 255, 0.85);
    font-size: 1.125rem;
}

.project-hero-location svg {
    width: 20px;
    height: 20px;
    opacity: 0.8;
}

/* Quick Stats Bar */
.project-stats-bar {
    background: #fff;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    margin-top: -30px;
    position: relative;
    z-index: 10;
    border-radius: 16px;
    overflow: hidden;
}

.project-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
}

.project-stat-item {
    padding: 24px 20px;
    text-align: center;
    border-right: 1px solid rgba(0, 0, 0, 0.05);
    transition: background 0.3s ease;
}

.project-stat-item:last-child {
    border-right: none;
}

.project-stat-item:hover {
    background: rgba(214, 90, 49, 0.05);
}

.project-stat-icon {
    width: 40px;
    height: 40px;
    margin: 0 auto 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0B3C5D 0%, #1a5a8a 100%);
    border-radius: 10px;
    color: #fff;
}

.project-stat-icon svg {
    width: 20px;
    height: 20px;
}

.project-stat-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #64748B;
    margin-bottom: 4px;
}

.project-stat-value {
    font-size: 1rem;
    font-weight: 700;
    color: #0B3C5D;
}

/* Main Content Section */
.project-content-section {
    padding: 80px 0;
    background: #fff;
}

.project-content-grid {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 60px;
    align-items: start;
}

@media (max-width: 968px) {
    .project-content-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }
}

/* Main Content Area */
.project-main {
    min-width: 0;
}

.project-section {
    margin-bottom: 48px;
}

.project-section:last-child {
    margin-bottom: 0;
}

.project-section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.5rem;
    font-weight: 700;
    color: #0B3C5D;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid #EEF2F6;
}

.project-section-title::before {
    content: '';
    width: 4px;
    height: 28px;
    background: linear-gradient(135deg, #D65A31 0%, #e86f4a 100%);
    border-radius: 2px;
}

.project-text {
    font-size: 1.0625rem;
    line-height: 1.9;
    color: #475569;
}

.project-text p {
    margin-bottom: 16px;
}

.project-text p:last-child {
    margin-bottom: 0;
}

/* Video Section */
.project-video-section {
    margin: 40px 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.project-video-wrapper {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    background: #000;
}

.project-video-wrapper iframe,
.project-video-wrapper video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}

/* Sidebar */
.project-sidebar {
    position: sticky;
    top: 100px;
}

.sidebar-card {
    background: linear-gradient(135deg, #f8fafc 0%, #EEF2F6 100%);
    border-radius: 16px;
    padding: 28px;
    margin-bottom: 24px;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.sidebar-card:last-child {
    margin-bottom: 0;
}

.sidebar-card-title {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #64748B;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}

.sidebar-fact {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 18px;
    padding-bottom: 18px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.sidebar-fact:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.sidebar-fact-icon {
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border-radius: 10px;
    color: #D65A31;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.sidebar-fact-icon svg {
    width: 18px;
    height: 18px;
}

.sidebar-fact-content {
    flex: 1;
    min-width: 0;
}

.sidebar-fact-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #94a3b8;
    margin-bottom: 3px;
}

.sidebar-fact-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: #1e293b;
}

/* CTA Card */
.sidebar-cta-card {
    background: linear-gradient(135deg, #0B3C5D 0%, #1a5a8a 100%);
    border-radius: 16px;
    padding: 28px;
    text-align: center;
    color: #fff;
}

.sidebar-cta-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.sidebar-cta-text {
    font-size: 0.9rem;
    opacity: 0.85;
    margin-bottom: 20px;
    line-height: 1.6;
}

.sidebar-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #D65A31;
    color: #fff;
    padding: 14px 28px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(214, 90, 49, 0.4);
}

.sidebar-cta-btn:hover {
    background: #e86f4a;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(214, 90, 49, 0.5);
}

.sidebar-cta-btn svg {
    width: 18px;
    height: 18px;
}

/* Related Projects Section */
.related-projects-section {
    padding: 80px 0;
    background: #f8fafc;
}

.related-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
}

.related-section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #0B3C5D;
}

.related-view-all {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #D65A31;
    text-decoration: none;
    font-weight: 600;
    transition: gap 0.3s ease;
}

.related-view-all:hover {
    gap: 12px;
}

/* CTA Banner */
.project-cta-banner {
    background: linear-gradient(135deg, #0B3C5D 0%, #1a5a8a 50%, #0B3C5D 100%);
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

.project-cta-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(214, 90, 49, 0.15) 0%, transparent 70%);
    border-radius: 50%;
}

.project-cta-content {
    position: relative;
    z-index: 1;
    text-align: center;
    max-width: 700px;
    margin: 0 auto;
}

.project-cta-title {
    font-size: clamp(1.75rem, 4vw, 2.5rem);
    font-weight: 700;
    color: #fff;
    margin-bottom: 16px;
}

.project-cta-text {
    font-size: 1.125rem;
    color: rgba(255, 255, 255, 0.85);
    margin-bottom: 32px;
    line-height: 1.7;
}

.project-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #D65A31;
    color: #fff;
    padding: 18px 40px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 8px 30px rgba(214, 90, 49, 0.4);
}

.project-cta-btn:hover {
    background: #e86f4a;
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(214, 90, 49, 0.5);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .project-hero {
        height: 50vh;
        min-height: 350px;
    }
    
    .project-hero-overlay {
        padding: 60px 0 30px;
    }
    
    .project-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .project-stat-item {
        padding: 18px 16px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .project-stat-item:nth-child(2),
    .project-stat-item:nth-child(4) {
        border-right: none;
    }
    
    .project-content-section {
        padding: 50px 0;
    }
    
    .project-sidebar {
        position: relative;
        top: 0;
    }
}
</style>

<!-- Hero Section -->
<section class="project-hero">
    <div class="project-hero-media">
        <?php if (!empty($project['featured_image'])): ?>
            <img src="<?php echo htmlspecialchars($project['featured_image']); ?>" 
                 alt="<?php echo htmlspecialchars($project['title']); ?>">
        <?php elseif ($isYouTube && $youtubeId): ?>
            <img src="https://img.youtube.com/vi/<?php echo $youtubeId; ?>/maxresdefault.jpg" 
                 alt="<?php echo htmlspecialchars($project['title']); ?>">
        <?php else: ?>
            <div style="width:100%;height:100%;background:linear-gradient(135deg, #0B3C5D 0%, #1a5a8a 100%);"></div>
        <?php endif; ?>
    </div>
    
    <div class="project-hero-overlay">
        <div class="container">
            <div class="project-hero-content">
                <!-- Breadcrumb -->
                <nav class="project-breadcrumb">
                    <a href="<?php echo url(''); ?>">Home</a>
                    <span>›</span>
                    <a href="<?php echo url('projects'); ?>">Projects</a>
                    <span>›</span>
                    <span style="color: rgba(255,255,255,0.9);"><?php echo htmlspecialchars($project['title']); ?></span>
                </nav>
                
                <?php if (!empty($project['category'])): ?>
                <span class="project-category-badge"><?php echo htmlspecialchars($project['category']); ?></span>
                <?php endif; ?>
                
                <h1 class="project-hero-title"><?php echo htmlspecialchars($project['title']); ?></h1>
                
                <?php if (!empty($project['location'])): ?>
                <div class="project-hero-location">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <?php echo htmlspecialchars($project['location']); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Quick Stats Bar -->
<div class="container">
    <div class="project-stats-bar">
        <div class="project-stats-grid">
            <div class="project-stat-item">
                <div class="project-stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="project-stat-label">Category</div>
                <div class="project-stat-value"><?php echo htmlspecialchars($project['category'] ?? 'Commercial'); ?></div>
            </div>
            
            <div class="project-stat-item">
                <div class="project-stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="project-stat-label">Location</div>
                <div class="project-stat-value"><?php echo htmlspecialchars($project['location'] ?? 'India'); ?></div>
            </div>
            
            <div class="project-stat-item">
                <div class="project-stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="project-stat-label">Status</div>
                <div class="project-stat-value"><?php echo $project['is_featured'] ? 'Featured' : 'Completed'; ?></div>
            </div>
            
            <div class="project-stat-item">
                <div class="project-stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div class="project-stat-label">Services</div>
                <div class="project-stat-value">Structural Design</div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<section class="project-content-section">
    <div class="container">
        <div class="project-content-grid">
            <!-- Main Content Area -->
            <div class="project-main">
                
                <!-- Video Section (if available) -->
                <?php if ($isYouTube && $youtubeId): ?>
                <div class="project-video-section">
                    <div class="project-video-wrapper">
                        <iframe 
                            src="https://www.youtube.com/embed/<?php echo $youtubeId; ?>?rel=0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
                <?php elseif ($isLocalVideo): ?>
                <?php 
                    // Fix video path - ensure it's absolute from root
                    $fixedVideoUrl = $videoUrl;
                    if (strpos($videoUrl, 'http') !== 0 && strpos($videoUrl, '/') !== 0) {
                        $fixedVideoUrl = url($videoUrl);
                    }
                ?>
                <div class="project-video-section">
                    <div class="project-video-wrapper">
                        <video autoplay muted loop playsinline poster="<?php echo htmlspecialchars($project['featured_image'] ?? ''); ?>">
                            <source src="<?php echo htmlspecialchars($fixedVideoUrl); ?>" type="video/mp4">
                            Your browser does not support video playback.
                        </video>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Overview Section -->
                <?php if (!empty($project['short_desc'])): ?>
                <div class="project-section">
                    <h2 class="project-section-title">Project Overview</h2>
                    <div class="project-text">
                        <p><?php echo nl2br(htmlspecialchars($project['short_desc'])); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Case Study Section -->
                <?php if (!empty($project['case_study'])): ?>
                <div class="project-section">
                    <h2 class="project-section-title">Detailed Case Study</h2>
                    <div class="project-text">
                        <?php echo $project['case_study']; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- If no content, show placeholder -->
                <?php if (empty($project['short_desc']) && empty($project['case_study'])): ?>
                <div class="project-section">
                    <h2 class="project-section-title">About This Project</h2>
                    <div class="project-text">
                        <p>This project showcases our expertise in structural engineering and design. Our team provided comprehensive solutions ensuring safety, efficiency, and aesthetic excellence.</p>
                        <p>For detailed information about this project or to discuss a similar requirement, please get in touch with our team.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <aside class="project-sidebar">
                <!-- Project Details Card -->
                <div class="sidebar-card">
                    <h3 class="sidebar-card-title">Project Details</h3>
                    
                    <?php if (!empty($project['category'])): ?>
                    <div class="sidebar-fact">
                        <div class="sidebar-fact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div class="sidebar-fact-content">
                            <div class="sidebar-fact-label">Category</div>
                            <div class="sidebar-fact-value"><?php echo htmlspecialchars($project['category']); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($project['location'])): ?>
                    <div class="sidebar-fact">
                        <div class="sidebar-fact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="sidebar-fact-content">
                            <div class="sidebar-fact-label">Location</div>
                            <div class="sidebar-fact-value"><?php echo htmlspecialchars($project['location']); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="sidebar-fact">
                        <div class="sidebar-fact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="sidebar-fact-content">
                            <div class="sidebar-fact-label">Status</div>
                            <div class="sidebar-fact-value"><?php echo $project['is_featured'] ? 'Featured Project' : 'Completed'; ?></div>
                        </div>
                    </div>
                    
                    <div class="sidebar-fact">
                        <div class="sidebar-fact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                        <div class="sidebar-fact-content">
                            <div class="sidebar-fact-label">Services Provided</div>
                            <div class="sidebar-fact-value">Structural Engineering</div>
                        </div>
                    </div>
                </div>
                
                <!-- CTA Card -->
                <div class="sidebar-cta-card">
                    <h4 class="sidebar-cta-title">Start Your Project</h4>
                    <p class="sidebar-cta-text">Have a similar project in mind? Let's discuss how we can help.</p>
                    <a href="<?php echo url('contact'); ?>" class="sidebar-cta-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Contact Us
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- CTA Banner -->
<section class="project-cta-banner">
    <div class="container">
        <div class="project-cta-content">
            <h2 class="project-cta-title">Ready to Build Something Great?</h2>
            <p class="project-cta-text">Partner with StructZone for innovative structural engineering solutions. From concept to completion, we deliver excellence.</p>
            <a href="<?php echo url('contact'); ?>" class="project-cta-btn">
                Get a Free Consultation
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>

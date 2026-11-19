<?php
$current_page = 'insights';
$page_title = 'Structural Engineering Insights & Blog';
$meta_description = 'Technical articles on structural engineering, design tips, and construction industry insights from StructZone engineers.';
$canonical_url = 'https://www.structzone.com/insights/';

require_once '../includes/site-data.php';
require_once '../includes/header.php';

// Get insights from database
$insights = getSiteInsights();
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?php echo url(); ?>">Home</a>
            <span>/</span>
            <span>Insights</span>
        </nav>
        <h1>Insights</h1>
        <p>Technical articles and industry perspectives from our team.</p>
    </div>
</section>

<!-- Articles Grid -->
<section class="section">
    <div class="container">
        <div class="insights-grid">
            <?php if (!empty($insights)): ?>
                <?php foreach ($insights as $insight): 
                    $imagePath = !empty($insight['featured_image']) ? (strpos($insight['featured_image'], '../') === 0 ? $insight['featured_image'] : '../' . $insight['featured_image']) : '../assets/images/project-1.jpg';
                    $insightLink = $insight['slug'] . '.php';
                ?>
                <article class="insight-card fade-in">
                    <a href="<?php echo $insightLink; ?>">
                        <div class="insight-image">
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($insight['title']); ?>" loading="lazy">
                        </div>
                        <div class="insight-content">
                            <span class="insight-tag"><?php echo htmlspecialchars($insight['meta_title'] ?? 'Article'); ?></span>
                            <h3><?php echo htmlspecialchars($insight['title']); ?></h3>
                            <p><?php echo htmlspecialchars($insight['excerpt'] ?? ''); ?></p>
                            <span class="insight-link">Read more →</span>
                        </div>
                    </a>
                </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; padding: 60px 20px; grid-column: 1 / -1;">No insights available yet. Add articles from the admin panel.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="section section-alt">
    <div class="container" style="text-align: center;">
        <h2>Get Structural Engineering Tips</h2>
        <p style="margin: 0 auto var(--space-xl); max-width: 500px;">Subscribe to receive structural engineering insights, seismic safety updates, and project tips. No spam — only useful updates.</p>
        <form style="display: flex; gap: var(--space-md); max-width: 480px; margin: 0 auto;">
            <input type="email" placeholder="Enter your email" class="form-input" style="flex: 1;">
            <button type="submit" class="btn btn-primary">Subscribe</button>
        </form>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container">
        <h2>Have a Question?</h2>
        <p>Contact us to discuss your structural engineering needs.</p>
        <a href="../contact.php" class="btn btn-primary btn-lg">Get in Touch</a>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>

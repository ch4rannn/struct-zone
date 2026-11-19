<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($meta_description) ? $meta_description : META_DESCRIPTION_DEFAULT; ?>">
    <link rel="canonical" href="<?php echo isset($canonical_url) ? $canonical_url : SITE_URL; ?>">
    
    <title><?php echo isset($page_title) ? $page_title . META_TITLE_SUFFIX : SITE_NAME; ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="<?php echo asset('css/main.css'); ?>">
    
    <!-- Schema Markup -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?php echo SITE_NAME; ?>",
        "url": "<?php echo SITE_URL; ?>",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "<?php echo CONTACT_PHONE; ?>",
            "email": "<?php echo CONTACT_EMAIL; ?>",
            "contactType": "customer service"
        }
    }
    </script>
</head>
<body>
    <!-- Header -->
    <header class="header" id="header">
        <div class="container">
            <a href="<?php echo url(); ?>" class="logo">
                <span class="logo-text">Struct<span class="logo-accent">Zone</span></span>
            </a>
            
            <nav class="nav" id="nav">
                <ul class="nav-list">
                    <li><a href="<?php echo url(); ?>" class="nav-link <?php echo $current_page === 'home' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="<?php echo url('services'); ?>" class="nav-link <?php echo $current_page === 'services' ? 'active' : ''; ?>">Services</a></li>
                    <li><a href="<?php echo url('projects'); ?>" class="nav-link <?php echo $current_page === 'projects' ? 'active' : ''; ?>">Projects</a></li>
                    <li><a href="<?php echo url('about.php'); ?>" class="nav-link <?php echo $current_page === 'about' ? 'active' : ''; ?>">About</a></li>
                    <li><a href="<?php echo url('insights'); ?>" class="nav-link <?php echo $current_page === 'insights' ? 'active' : ''; ?>">Insights</a></li>
                    <li><a href="<?php echo url('contact.php'); ?>" class="nav-link nav-cta <?php echo $current_page === 'contact' ? 'active' : ''; ?>">Contact</a></li>
                </ul>
            </nav>
            
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <span class="hamburger"></span>
            </button>
        </div>
    </header>
    
    <main class="main">

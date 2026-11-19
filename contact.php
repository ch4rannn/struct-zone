<?php
$current_page = 'contact';
$page_title = 'Contact StructZone | Get a Quote';
$meta_description = 'Contact StructZone for structural design, analysis, and inspection services. Quick response. Serving clients across India.';
$canonical_url = 'https://www.structzone.com/contact.php';

require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?php echo url(); ?>">Home</a>
            <span>/</span>
            <span>Contact</span>
        </nav>
        <h1>Contact Us</h1>
        <p>Let's discuss your structural engineering project.</p>
    </div>
</section>

<!-- Contact Redesign Section -->
<section class="contact-redesign-section">
    <div class="container">
        <div class="contact-redesign-wrapper">
            <!-- Left Card: Contact Info -->
            <div class="contact-info-card">
                <div class="info-card-header">
                    <h2>Contact Information</h2>
                    <p>Find us at our office or give us a call.</p>
                </div>
                
                <div class="info-card-details">
                    <!-- Address -->
                    <div class="info-card-item">
                        <div class="info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h4>Our Location</h4>
                            <p><?php echo CONTACT_ADDRESS; ?></p>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="info-card-item">
                        <div class="info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h4>Phone</h4>
                            <a href="tel:<?php echo CONTACT_PHONE; ?>"><?php echo CONTACT_PHONE; ?></a>
                            <p style="font-size: 0.85rem; opacity: 0.7;">Mon-Sat 9am-7pm</p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="info-card-item">
                        <div class="info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h4>Email</h4>
                            <a href="mailto:<?php echo CONTACT_EMAIL; ?>"><?php echo CONTACT_EMAIL; ?></a>
                            <p style="font-size: 0.85rem; opacity: 0.7;">We reply within 24 hours</p>
                        </div>
                    </div>
                </div>

                <!-- Embedded Map -->
                <div class="info-map-container">
                    <iframe src="https://maps.google.com/maps?q=30.7188296,76.6517561(Lifestyle%20Residency)&t=&z=17&ie=UTF8&iwloc=B&output=embed" 
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" 
                    style="filter: saturate(0.9) contrast(1.05);"></iframe>
                </div>
            </div>

            <!-- Right Card: Contact Form -->
            <div class="contact-form-card">
                <h2>Send us a Message</h2>
                <p>Fill out the form below and we will get back to you shortly.</p>
                
                <form action="process-contact.php" method="POST" data-validate>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="name">Your Name</label>
                            <input type="text" id="name" name="name" class="form-input" required placeholder="John Doe">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-input" placeholder="+91 XXXXXXXXXX">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" required placeholder="john@example.com">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="service">Service (Optional)</label>
                        <select id="service" name="service" class="form-select">
                            <option value="">Select a service</option>
                            <option value="structural-design">Structural Design</option>
                            <option value="bim">BIM & Drafting</option>
                            <option value="consultancy">Consultancy</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="message">Message</label>
                        <textarea id="message" name="message" class="form-textarea" required placeholder="How can we help you?"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                    <p style="font-size: 0.8rem; margin-top: 16px; text-align: center; color: var(--color-text-muted);">We usually reply within 30 minutes</p>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- LocalBusiness Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ProfessionalService",
    "name": "<?php echo SITE_NAME; ?>",
    "url": "<?php echo SITE_URL; ?>",
    "telephone": "<?php echo CONTACT_PHONE; ?>",
    "email": "<?php echo CONTACT_EMAIL; ?>",
    "address": {
        "@type": "PostalAddress",
        "addressCountry": "IN"
    },
    "openingHours": "Mo-Sa 09:00-19:00",
    "priceRange": "$$",
    "description": "Structural engineering services including design, analysis, foundation design, inspection, and detailing."
}
</script>

<?php require_once 'includes/footer.php'; ?>

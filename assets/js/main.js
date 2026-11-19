/**
 * StructZone Main JavaScript
 * Core functionality and interactions
 */

document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // Hero Video Handling
    // ==========================================
    const heroVideo = document.querySelector('.hero-video-element');

    if (heroVideo) {
        // Check for reduced motion preference
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (prefersReducedMotion) {
            heroVideo.pause();
            heroVideo.style.display = 'none';
            document.querySelector('.hero-video-fallback').style.display = 'block';
        }

        // Handle video load error - show fallback
        heroVideo.addEventListener('error', function () {
            document.querySelector('.hero-video').classList.add('no-video');
        });

        // Lazy load video when in viewport
        if ('IntersectionObserver' in window) {
            const videoObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        heroVideo.play().catch(() => {
                            // Autoplay blocked, show fallback
                            document.querySelector('.hero-video').classList.add('no-video');
                        });
                    } else {
                        heroVideo.pause();
                    }
                });
            }, { threshold: 0.25 });

            videoObserver.observe(heroVideo);
        }
    }

    // ==========================================
    // Mobile Navigation Toggle
    // ==========================================
    const navToggle = document.getElementById('navToggle');
    const nav = document.getElementById('nav');

    if (navToggle && nav) {
        navToggle.addEventListener('click', function () {
            this.classList.toggle('active');
            nav.classList.toggle('active');
        });

        // Close nav when clicking outside
        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target) && !navToggle.contains(e.target)) {
                navToggle.classList.remove('active');
                nav.classList.remove('active');
            }
        });

        // Close nav when clicking a link
        const navLinks = nav.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function () {
                navToggle.classList.remove('active');
                nav.classList.remove('active');
            });
        });
    }

    // ==========================================
    // Header Scroll Effect
    // ==========================================
    const header = document.getElementById('header');

    if (header) {
        let lastScroll = 0;

        window.addEventListener('scroll', function () {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }

            lastScroll = currentScroll;
        });
    }

    // ==========================================
    // Smooth Scroll for Anchor Links
    // ==========================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);

                if (target) {
                    const headerHeight = document.getElementById('header').offsetHeight;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // ==========================================
    // Lazy Loading Images
    // ==========================================
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('img[data-src]');

        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px'
        });

        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    }

    // ==========================================
    // Form Validation
    // ==========================================
    const forms = document.querySelectorAll('form[data-validate]');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }

                // Email validation
                if (field.type === 'email' && field.value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(field.value)) {
                        isValid = false;
                        field.classList.add('error');
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // ==========================================
    // Gallery Carousel (Projects)
    // ==========================================
    const galleryCarousel = document.getElementById('galleryCarousel');
    const galleryTrack = document.getElementById('galleryTrack');
    const galleryPrev = document.getElementById('galleryPrev');
    const galleryNext = document.getElementById('galleryNext');
    const galleryDotsContainer = document.getElementById('galleryDots');

    if (galleryCarousel && galleryTrack) {
        const cards = galleryTrack.querySelectorAll('.gallery-card');
        const cardCount = cards.length;
        // Start from slide 2 (third card, 0-indexed)
        let currentIndex = 2;
        let isDragging = false;
        let startPos = 0;
        let currentTranslate = 0;
        let prevTranslate = 0;

        // Get card width including gap
        const getCardWidth = () => {
            const card = cards[0];
            const gap = parseInt(getComputedStyle(galleryTrack).gap) || 24;
            return card.offsetWidth + gap;
        };

        // Calculate max scroll index
        const getMaxIndex = () => {
            const viewportWidth = galleryCarousel.offsetWidth;
            const totalWidth = cardCount * getCardWidth();
            const maxScroll = totalWidth - viewportWidth;
            return Math.ceil(maxScroll / getCardWidth());
        };

        // Create dots
        const createDots = () => {
            if (!galleryDotsContainer) return;
            galleryDotsContainer.innerHTML = '';
            const maxIndex = Math.min(cardCount, getMaxIndex() + 1);
            for (let i = 0; i < maxIndex; i++) {
                const dot = document.createElement('button');
                dot.classList.add('gallery-dot');
                if (i === 0) dot.classList.add('active');
                dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
                dot.addEventListener('click', () => goToSlide(i));
                galleryDotsContainer.appendChild(dot);
            }
        };

        // Update dots
        const updateDots = () => {
            if (!galleryDotsContainer) return;
            const dots = galleryDotsContainer.querySelectorAll('.gallery-dot');
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentIndex);
            });
        };

        // Update buttons state
        const updateButtons = () => {
            if (galleryPrev) galleryPrev.disabled = currentIndex === 0;
            if (galleryNext) galleryNext.disabled = currentIndex >= getMaxIndex();
        };

        // Go to specific slide
        const goToSlide = (index) => {
            currentIndex = Math.max(0, Math.min(index, getMaxIndex()));
            const translateX = -currentIndex * getCardWidth();
            galleryTrack.style.transform = `translateX(${translateX}px)`;
            prevTranslate = translateX;
            currentTranslate = translateX;
            updateButtons();
            updateDots();
        };

        // Navigation buttons
        if (galleryPrev) {
            galleryPrev.addEventListener('click', () => goToSlide(currentIndex - 1));
        }
        if (galleryNext) {
            galleryNext.addEventListener('click', () => goToSlide(currentIndex + 1));
        }

        // Drag functionality
        const dragStart = (e) => {
            isDragging = true;
            startPos = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
            galleryCarousel.classList.add('dragging');
        };

        const drag = (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const currentPos = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
            const diff = currentPos - startPos;
            currentTranslate = prevTranslate + diff;
            galleryTrack.style.transform = `translateX(${currentTranslate}px)`;
        };

        const dragEnd = () => {
            if (!isDragging) return;
            isDragging = false;
            galleryCarousel.classList.remove('dragging');

            const movedBy = currentTranslate - prevTranslate;
            const cardWidth = getCardWidth();

            if (movedBy < -cardWidth / 3) {
                currentIndex = Math.min(currentIndex + 1, getMaxIndex());
            } else if (movedBy > cardWidth / 3) {
                currentIndex = Math.max(currentIndex - 1, 0);
            }

            goToSlide(currentIndex);
        };

        // Mouse events
        galleryCarousel.addEventListener('mousedown', dragStart);
        galleryCarousel.addEventListener('mousemove', drag);
        galleryCarousel.addEventListener('mouseup', dragEnd);
        galleryCarousel.addEventListener('mouseleave', dragEnd);

        // Touch events
        galleryCarousel.addEventListener('touchstart', dragStart, { passive: true });
        galleryCarousel.addEventListener('touchmove', drag, { passive: false });
        galleryCarousel.addEventListener('touchend', dragEnd);

        // Prevent link clicks during drag
        cards.forEach(card => {
            card.addEventListener('click', (e) => {
                if (Math.abs(currentTranslate - prevTranslate) > 5) {
                    e.preventDefault();
                }
            });
        });

        // Initialize at middle position
        createDots();
        updateButtons();
        goToSlide(currentIndex);

        // Auto-advance carousel every 5 seconds
        let autoAdvanceTimer;

        const startAutoAdvance = () => {
            autoAdvanceTimer = setInterval(() => {
                if (currentIndex >= getMaxIndex()) {
                    goToSlide(0);
                } else {
                    goToSlide(currentIndex + 1);
                }
            }, 2000);
        };

        const stopAutoAdvance = () => {
            clearInterval(autoAdvanceTimer);
        };

        startAutoAdvance();

        // Pause auto-advance on hover
        galleryCarousel.addEventListener('mouseenter', stopAutoAdvance);
        galleryCarousel.addEventListener('mouseleave', startAutoAdvance);

        // Recalculate on resize
        window.addEventListener('resize', () => {
            createDots();
            goToSlide(currentIndex);
        });
    }

    // ==========================================
    // Project Filter (for Projects page)
    // ==========================================
    const filterButtons = document.querySelectorAll('[data-filter]');
    const projectItems = document.querySelectorAll('[data-category]');

    if (filterButtons.length && projectItems.length) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                const filter = this.dataset.filter;

                // Update active state
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Filter projects
                projectItems.forEach(item => {
                    if (filter === 'all' || item.dataset.category === filter) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }

});

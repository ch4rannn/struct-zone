/**
 * StructZone Subtle Animations
 * Arup-style refined, almost invisible motion
 * Respects prefers-reduced-motion
 */

(function () {
    'use strict';

    // Respect reduced motion preference
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefersReducedMotion) {
        // Show all elements immediately without animation
        document.querySelectorAll('.fade-in, .reveal').forEach(el => {
            el.style.opacity = '1';
            el.style.transform = 'none';
        });
        return;
    }

    // Wait for GSAP to load
    if (typeof gsap === 'undefined') return;

    // Register ScrollTrigger plugin
    gsap.registerPlugin(ScrollTrigger);

    // Default animation settings - slow, subtle
    const defaults = {
        duration: 0.8,
        ease: 'power2.out',
        y: 16, // Slight upward movement (Standardized)
        autoAlpha: 0 // Handles opacity + visibility
    };

    // Animate elements with .fade-in class
    gsap.utils.toArray('.fade-in').forEach((el, i) => {
        gsap.from(el, {
            ...defaults,
            delay: i * 0.1, // Stagger within viewport
            clearProps: 'all', // Ensure clean state after animation
            scrollTrigger: {
                trigger: el,
                start: 'top bottom-=50', // Trigger when top enters viewport (minus buffer)
                toggleActions: 'play none none none',
                once: true
            }
        });
    });

    // Section headers - subtle reveal
    gsap.utils.toArray('.section-header').forEach(el => {
        gsap.from(el, {
            duration: 1,
            y: 16,
            autoAlpha: 0,
            ease: 'power2.out',
            clearProps: 'all',
            scrollTrigger: {
                trigger: el,
                start: 'top bottom-=50',
                once: true
            }
        });
    });

    // Hero content - fade in on load
    const heroContent = document.querySelector('.hero-content');
    if (heroContent) {
        gsap.from(heroContent.children, {
            duration: 1,
            y: 16,
            autoAlpha: 0,
            stagger: 0.15,
            ease: 'power2.out',
            delay: 0.3,
            clearProps: 'all'
        });
    }

    // Cards - subtle stagger
    gsap.utils.toArray('.card, .service-card, .feature-item').forEach((el, i) => {
        gsap.from(el, {
            duration: 0.6,
            y: 16,
            autoAlpha: 0,
            ease: 'power2.out',
            clearProps: 'all',
            scrollTrigger: {
                trigger: el,
                start: 'top bottom-=50',
                once: true
            }
        });
    });

    // Gallery cards - no animation, just ensure visibility
    gsap.utils.toArray('.gallery-card').forEach(el => {
        el.style.opacity = '1';
    });

    // Footer - subtle toggle
    const footer = document.querySelector('.footer');
    if (footer) {
        gsap.from(footer, {
            duration: 0.8,
            autoAlpha: 0,
            clearProps: 'all',
            scrollTrigger: {
                trigger: footer,
                start: 'top bottom',
                once: true
            }
        });
    }

})();

/**
 * Animation Constants
 * Global configuration for all animations
 */
const ANIMATION_CONFIG = {
    duration: 0.3,
    stagger: 0.1,
    ease: 'power2.inOut',
    fadeFrom: {
        y: 30,
        opacity: 0
    },
    scrollTrigger: {
        start: 'top 75%',
        toggleActions: 'play none none none'
    }
};

/**
 * Main animation initialization
 * Determines page type and initializes appropriate animations
 */
document.addEventListener('DOMContentLoaded', function() {
    const isHomepage = document.body.classList.contains('home');
    
    // if (isHomepage) {
    //     initHomePageAnimations();
    // } else {
    //     initInnerPageAnimations();
    // }

    // initGlobalAnimations();
});

/**
 * Homepage-specific animations
 * Handles hero, services, works, and CTA sections
 */
function initHomePageAnimations() {
    // Hero Section
    const heroTl = gsap.timeline();
    
    heroTl.from('.banner__title', {
        duration: ANIMATION_CONFIG.duration,
        y: 30,
        opacity: 0,
        ease: ANIMATION_CONFIG.ease
    })
    .from('.banner__text', {
        duration: ANIMATION_CONFIG.duration,
        y: 30,
        opacity: 0,
        ease: ANIMATION_CONFIG.ease
    }, '-=0.5')
    .from('#banner-image', {
        duration: ANIMATION_CONFIG.duration,
        scale: 0.95,
        opacity: 0,
        ease: ANIMATION_CONFIG.ease
    }, '-=0.5');

    // Services Section
    const servicesTl = gsap.timeline({
        scrollTrigger: {
            trigger: '.ftf-module-multi-column',
            start: 'top 80%',
            toggleActions: 'play none none none'
        }
    });

    servicesTl
        .from('.ftf-module__title', {
            duration: ANIMATION_CONFIG.duration,
            y: 30,
            opacity: 0,
            ease: ANIMATION_CONFIG.ease
        })
        .from('.ftf-module__description', {
            duration: ANIMATION_CONFIG.duration,
            y: 30,
            opacity: 0,
            ease: ANIMATION_CONFIG.ease
        }, '-=0.1');

    // Animate each service column and its contents
    document.querySelectorAll('.services-col').forEach((col, index) => {
        const elements = [
            col.querySelector('.column-image'),
            col.querySelector('.column-title'),
            col.querySelector('.column-text')
        ];

        servicesTl.from(elements, {
            duration: ANIMATION_CONFIG.duration,
            y: 30,
            opacity: 0,
            stagger: ANIMATION_CONFIG.stagger,
            ease: ANIMATION_CONFIG.ease
        }, index === 0 ? '-=0.1' : '-=0.2');
    });

    // Recent Works
    gsap.utils.toArray('.ftf-post-item').forEach((item, index) => {
        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: item,
                start: 'top 80%',
                toggleActions: 'play none none none'
            }
        });

        const direction = index % 2 === 0 ? -1 : 1;
        const elements = [
            item.querySelector('.ftf-post-item__thumbnail'),
            item.querySelector('.ftf-post-item__title'),
            item.querySelector('.ftf-post-item__excerpt'),
            item.querySelector('.ftf-post-item__terms'),
            item.querySelector('.ftf-post-item__button')
        ];

        tl.from(elements[0], {
            duration: ANIMATION_CONFIG.duration,
            x: 50 * direction,
            opacity: 0,
            ease: ANIMATION_CONFIG.ease
        })
        .from(elements.slice(1), {
            duration: ANIMATION_CONFIG.duration,
            y: 20,
            opacity: 0,
            stagger: ANIMATION_CONFIG.stagger,
            ease: ANIMATION_CONFIG.ease
        }, '-=0.4');
    });

    // Testimonials Section
    initTestimonialAnimations();

    // Updated CTA Section Animation
    const ctaElement = document.querySelector('.ftf-cta');
    if (ctaElement) {
        setTimeout(() => {
            ScrollTrigger.refresh();
            
            const ctaTl = gsap.timeline({
                scrollTrigger: {
                    trigger: ctaElement,
                    start: 'top 85%',
                    end: 'bottom 20%',
                    toggleActions: 'restart none none reverse',
                    markers: false
                }
            });

            ctaTl.fromTo(ctaElement,
                {
                    opacity: 0,
                    y: 30
                },
                {
                    duration: ANIMATION_CONFIG.duration,
                    opacity: 1,
                    y: 0,
                    ease: ANIMATION_CONFIG.ease,
                    clearProps: 'transform'
                }
            );
        }, 100); // Small delay to ensure proper initialization
    }
}

/**
 * Inner pages animations
 * Handles all module types including work cards and multi-column layouts
 */
function initInnerPageAnimations() {
    const modules = gsap.utils.toArray('.ftf-module');
    const viewportHeight = window.innerHeight;

    modules.forEach((module, index) => {
        // Skip modules with both 'ftf-module-code' and 'cta-shortcode'
        if (module.classList.contains('ftf-module-code') && module.classList.contains('cta-shortcode')) {
            return; // Skip this module
        }
        const moduleTop = module.getBoundingClientRect().top;
        const isAboveFold = moduleTop < viewportHeight;
        
        if (module.classList.contains('ftf-module-works')) {
            initWorkCardsAnimation(module);
        } 
        else if (module.classList.contains('ftf-module-multi-column')) {
            initMultiColumnAnimation(module, index, isAboveFold);
        }
        else {
            initStandardModuleAnimation(module, index, isAboveFold);
        }
    });
}

/**
 * Work Cards Animation
 * Handles the sequential animation of work card items
 */
function initWorkCardsAnimation(module) {
    const cards = gsap.utils.toArray(module.querySelectorAll('.card'));
    
    gsap.to(cards, {
        duration: ANIMATION_CONFIG.duration,
        y: 0,
        opacity: 1,
        stagger: ANIMATION_CONFIG.stagger,
        ease: ANIMATION_CONFIG.ease,
        scrollTrigger: {
            trigger: module,
            ...ANIMATION_CONFIG.scrollTrigger
        }
    });
}

/**
 * Multi-Column Animation
 * Handles columns within modules
 */
function initMultiColumnAnimation(module, index, isAboveFold) {
    const columns = module.querySelectorAll('.column');
    const moduleTl = gsap.timeline({
        scrollTrigger: {
            trigger: module,
            start: isAboveFold ? 'top 100%' : ANIMATION_CONFIG.scrollTrigger.start,
            toggleActions: ANIMATION_CONFIG.scrollTrigger.toggleActions
        },
        delay: index * ANIMATION_CONFIG.stagger
    });

    moduleTl.from(columns, {
        duration: ANIMATION_CONFIG.duration,
        ...ANIMATION_CONFIG.fadeFrom,
        stagger: ANIMATION_CONFIG.stagger,
        ease: ANIMATION_CONFIG.ease
    });
}

/**
 * Standard Module Animation
 * Default animation for regular modules
 */
function initStandardModuleAnimation(module, index, isAboveFold) {
    gsap.from(module, {
        duration: ANIMATION_CONFIG.duration,
        ...ANIMATION_CONFIG.fadeFrom,
        ease: ANIMATION_CONFIG.ease,
        delay: index * ANIMATION_CONFIG.stagger,
        scrollTrigger: {
            trigger: module,
            start: isAboveFold ? 'top 100%' : ANIMATION_CONFIG.scrollTrigger.start,
            toggleActions: ANIMATION_CONFIG.scrollTrigger.toggleActions
        }
    });
}

/**
 * Global animations
 */
function initGlobalAnimations() {
    // Button hover animations
    const buttons = document.querySelectorAll('.button, a');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', () => {
            gsap.to(button, {
                scale: 1.05,
                duration: 0.3,
                ease: ANIMATION_CONFIG.ease
            });
        });
        
        button.addEventListener('mouseleave', () => {
            gsap.to(button, {
                scale: 1,
                duration: 0.3,
                ease: ANIMATION_CONFIG.ease
            });
        });
    });
}

/**
 * Testimonial carousel animations
 */
function initTestimonialAnimations() {
    jQuery(document).ready(function($) {
        const $swiperContainer = $('.ftf-module-testimonials-carousel .swiper-container');
        
        if (!$swiperContainer.length) return;

        setTimeout(() => {
            const swiper = $swiperContainer[0].swiper;
            if (!swiper) return;

            function animateMessage(message) {
                gsap.fromTo(message,
                    { 
                        opacity: 0,
                        y: 30,
                        scale: 0.95
                    },
                    {
                        duration: ANIMATION_CONFIG.duration,
                        opacity: 1,
                        y: 0,
                        scale: 1,
                        ease: ANIMATION_CONFIG.ease,
                        clearProps: 'scale'
                    }
                );
            }

            // Initial animation
            const $initialMessage = $(swiper.slides[swiper.activeIndex]).find('.testimonial__message');
            if ($initialMessage.length) {
                animateMessage($initialMessage[0]);
            }

            // Slide change animation
            swiper.on('slideChangeTransitionStart', function() {
                const $activeMessage = $(swiper.slides[swiper.activeIndex]).find('.testimonial__message');
                if ($activeMessage.length) {
                    animateMessage($activeMessage[0]);
                }
            });
        }, 500);
    });
} 
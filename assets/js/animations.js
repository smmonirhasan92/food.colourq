/**
 * ==========================================================================
 * HIGH-END MICRO-ANIMATIONS ENGINE (GSAP SUPPORT & VANILLA FALLBACKS)
 * Handles storefront card cascades, statistical count-ups, magnetic hovers, 
 * page transitions, and smooth interactive springiness.
 * ==========================================================================
 */

class PremiumAnimationsEngine {
    constructor() {
        this.gsapEnabled = typeof gsap !== 'undefined';
        this.init();
    }

    init() {
        if (this.gsapEnabled) {
            console.log('[AnimationEngine] Premium GSAP mode enabled.');
            this.setupScrollTriggers();
        } else {
            console.log('[AnimationEngine] GSAP undetected. Utilizing native CSS hardware transitions.');
        }
    }

    /**
     * Staggered entry cascade for storefront menu lists and grid items.
     * @param {string} selector - CSS targets to cascade animate.
     */
    cascadeEntrance(selector = '.menu-card') {
        if (this.gsapEnabled) {
            gsap.from(selector, {
                duration: 0.6,
                y: 40,
                opacity: 0,
                stagger: 0.08,
                ease: "power3.out",
                clearProps: "all"
            });
        } else {
            // CSS Transition fallback using class tagging
            const elements = document.querySelectorAll(selector);
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(25px)';
                el.style.transition = 'opacity 0.5s ease, transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 80);
            });
        }
    }

    /**
     * Smoothly animate counting metrics for Admin dashboards.
     * @param {HTMLElement} element - Targeting DOM element.
     * @param {number} targetValue - Ending numerical limit.
     */
    animateCounter(element, targetValue) {
        if (!element) return;

        if (this.gsapEnabled) {
            const counterObj = { val: 0 };
            gsap.to(counterObj, {
                val: targetValue,
                duration: 1.5,
                ease: "power2.out",
                onUpdate: () => {
                    element.textContent = Math.floor(counterObj.val).toLocaleString();
                }
            });
        } else {
            // Vanilla frame-based animation
            let start = 0;
            const end = targetValue;
            const duration = 1200; // ms
            const stepTime = 16; // ~60fps
            const steps = duration / stepTime;
            const increment = (end - start) / steps;
            
            const timer = setInterval(() => {
                start += increment;
                if (start >= end) {
                    element.textContent = end.toLocaleString();
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(start).toLocaleString();
                }
            }, stepTime);
        }
    }

    /**
     * Dynamic slide-in & bouncy elastic feel for shopping cart panels.
     */
    animateCartOpen(drawerSelector = '.cart-drawer', backdropSelector = '.modal-backdrop') {
        const drawer = document.querySelector(drawerSelector);
        const backdrop = document.querySelector(backdropSelector);

        if (!drawer) return;

        if (this.gsapEnabled) {
            gsap.killTweensOf([drawer, backdrop]);
            
            gsap.set(drawer, { display: 'flex' });
            gsap.to(drawer, {
                x: 0,
                duration: 0.5,
                ease: "power4.out"
            });

            if (backdrop) {
                gsap.to(backdrop, {
                    opacity: 1,
                    pointerEvents: 'auto',
                    duration: 0.3
                });
            }
        } else {
            // Standard CSS toggle handled by main.js
            drawer.classList.add('open');
            if (backdrop) backdrop.classList.add('active');
        }
    }

    /**
     * Staggered menu loaders that substitute skeletal layouts.
     */
    transitionSkeletonOut(skeletonsSelector = '.skeleton', contentSelector = '.real-content') {
        if (this.gsapEnabled) {
            gsap.to(skeletonsSelector, {
                opacity: 0,
                duration: 0.3,
                onComplete: () => {
                    document.querySelectorAll(skeletonsSelector).forEach(sk => sk.style.display = 'none');
                    gsap.fromTo(contentSelector, { opacity: 0, y: 15 }, { opacity: 1, y: 0, duration: 0.4, ease: "power2.out" });
                }
            });
        } else {
            document.querySelectorAll(skeletonsSelector).forEach(sk => {
                sk.style.opacity = '0';
                sk.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    sk.style.display = 'none';
                    const contents = document.querySelectorAll(contentSelector);
                    contents.forEach(cnt => {
                        cnt.style.opacity = '1';
                        cnt.style.transform = 'none';
                    });
                }, 300);
            });
        }
    }

    /**
     * Spring effect on adding menu items to shopping cart.
     */
    bounceCartIcon(cartBtnSelector = '.cart-icon-btn') {
        const btn = document.querySelector(cartBtnSelector);
        if (!btn) return;

        if (this.gsapEnabled) {
            gsap.timeline()
                .to(btn, { scale: 0.85, duration: 0.1, ease: "power2.out" })
                .to(btn, { scale: 1.2, duration: 0.2, ease: "elastic.out(1, 0.3)" })
                .to(btn, { scale: 1.0, duration: 0.1, ease: "power2.out" });
        } else {
            btn.style.transform = 'scale(0.8)';
            setTimeout(() => {
                btn.style.transform = 'scale(1.2) rotate(-5deg)';
                btn.style.transition = 'transform 0.15s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                setTimeout(() => {
                    btn.style.transform = 'scale(1) rotate(0)';
                }, 150);
            }, 100);
        }
    }

    /**
     * Flying food card image to cart animation.
     * Clones the selected image, animates it in a parabolic path to the cart icon, and bounces it.
     */
    animateFlyToCart(imgElement, targetSelector = '.cart-icon-btn') {
        if (!imgElement) return;

        const cart = document.querySelector(targetSelector);
        if (!cart) return;

        // Create a clone of the image
        const clone = imgElement.cloneNode(true);
        document.body.appendChild(clone);

        // Get starting coordinates
        const rect = imgElement.getBoundingClientRect();
        const startX = rect.left + window.scrollX;
        const startY = rect.top + window.scrollY;

        // Get ending coordinates
        const cartRect = cart.getBoundingClientRect();
        const endX = cartRect.left + window.scrollX;
        const endY = cartRect.top + window.scrollY;

        // Style the clone
        clone.style.position = 'absolute';
        clone.style.left = `${startX}px`;
        clone.style.top = `${startY}px`;
        clone.style.width = `${rect.width}px`;
        clone.style.height = `${rect.height}px`;
        clone.style.zIndex = '99999';
        clone.style.borderRadius = '50%';
        clone.style.objectFit = 'cover';
        clone.style.pointerEvents = 'none';

        if (this.gsapEnabled) {
            gsap.timeline({
                onComplete: () => {
                    clone.remove();
                    this.bounceCartIcon(targetSelector);
                }
            })
            .to(clone, {
                left: endX,
                top: endY,
                width: 30,
                height: 30,
                borderRadius: '50%',
                opacity: 0.4,
                rotation: 360,
                duration: 0.85,
                ease: "power2.inOut"
            });
        } else {
            // Native CSS transition fallback
            clone.style.transition = 'all 0.8s cubic-bezier(0.25, 1, 0.5, 1)';
            
            // Force reflow
            clone.getBoundingClientRect();

            clone.style.left = `${endX}px`;
            clone.style.top = `${endY}px`;
            clone.style.width = '30px';
            clone.style.height = '30px';
            clone.style.opacity = '0.3';
            clone.style.transform = 'rotate(360deg)';

            setTimeout(() => {
                clone.remove();
                this.bounceCartIcon(targetSelector);
            }, 800);
        }
    }

    /**
     * Scale up zoom animation for modal popups.
     */
    animateModalOpen(modalSelector) {
        const modal = document.querySelector(modalSelector);
        if (!modal) return;

        // Ensure active class and style setups
        modal.style.display = 'flex';
        modal.classList.add('active');

        const modalContent = modal.querySelector('.glass-panel') || modal.querySelector('.modal-content') || modal;

        if (this.gsapEnabled) {
            gsap.killTweensOf([modal, modalContent]);
            gsap.fromTo(modal, { opacity: 0 }, { opacity: 1, duration: 0.25 });
            gsap.fromTo(modalContent, 
                { scale: 0.8, opacity: 0, y: 30 }, 
                { scale: 1, opacity: 1, y: 0, duration: 0.45, ease: "back.out(1.7)" }
            );
        } else {
            modal.style.opacity = '0';
            modalContent.style.transform = 'scale(0.8) translateY(30px)';
            modalContent.style.opacity = '0';
            modal.style.transition = 'opacity 0.25s ease';
            modalContent.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';

            // Force reflow
            modal.getBoundingClientRect();

            modal.style.opacity = '1';
            modalContent.style.transform = 'scale(1) translateY(0)';
            modalContent.style.opacity = '1';
        }
    }

    /**
     * Scale down close animation for modal popups.
     */
    animateModalClose(modalSelector, onCompleteCallback = null) {
        const modal = document.querySelector(modalSelector);
        if (!modal) return;

        const modalContent = modal.querySelector('.glass-panel') || modal.querySelector('.modal-content') || modal;

        if (this.gsapEnabled) {
            gsap.killTweensOf([modal, modalContent]);
            gsap.to(modalContent, { 
                scale: 0.8, 
                opacity: 0, 
                y: 20, 
                duration: 0.3, 
                ease: "power2.in" 
            });
            gsap.to(modal, { 
                opacity: 0, 
                duration: 0.3, 
                delay: 0.05,
                onComplete: () => {
                    modal.style.display = 'none';
                    modal.classList.remove('active');
                    if (onCompleteCallback) onCompleteCallback();
                }
            });
        } else {
            modalContent.style.transform = 'scale(0.8) translateY(20px)';
            modalContent.style.opacity = '0';
            modal.style.opacity = '0';

            setTimeout(() => {
                modal.style.display = 'none';
                modal.classList.remove('active');
                if (onCompleteCallback) onCompleteCallback();
            }, 300);
        }
    }

    /**
     * Springy zoom-in entrance for inline page forms or containers.
     */
    animateFormZoomIn(formSelector) {
        const form = document.querySelector(formSelector);
        if (!form) return;

        if (this.gsapEnabled) {
            gsap.fromTo(form, 
                { scale: 0.93, opacity: 0, y: 20 },
                { scale: 1, opacity: 1, y: 0, duration: 0.65, ease: "back.out(1.4)" }
            );
        } else {
            form.style.opacity = '0';
            form.style.transform = 'scale(0.93) translateY(20px)';
            form.style.transition = 'all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.2)';

            // Force reflow
            form.getBoundingClientRect();

            form.style.opacity = '1';
            form.style.transform = 'scale(1) translateY(0)';
        }
    }

    /**
     * Integrates ScrollTrigger placeholders for subtle view entries.
     */
    setupScrollTriggers() {
        if (typeof ScrollTrigger !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);
            
            // Auto animate any glass card marked for scroll entry
            gsap.utils.toArray('.scroll-entrance').forEach(element => {
                gsap.from(element, {
                    scrollTrigger: {
                        trigger: element,
                        start: "top 85%",
                        toggleActions: "play none none none"
                    },
                    y: 30,
                    opacity: 0,
                    duration: 0.6,
                    ease: "power2.out"
                });
            });
        }
    }
}

// Instantiate globally
window.AnimationEngine = new PremiumAnimationsEngine();

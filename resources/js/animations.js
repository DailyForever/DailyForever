// DailyForever Animations
// Handles scroll animations, page transitions, and interactive effects

class AnimationManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupScrollAnimations();
        this.setupPageLoadAnimations();
        this.setupFormAnimations();
        this.setupButtonAnimations();
        this.setupNavbarAnimations();
        this.setupThemeTransitions();
        this.setupMicroInteractions();
    }

    // Scroll-based animations
    setupScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    // Add stagger animation to children
                    if (entry.target.classList.contains('stagger-children')) {
                        entry.target.classList.add('animate');
                    }
                }
            });
        }, observerOptions);

        // Observe all elements with reveal classes
        document.querySelectorAll('.reveal, .fade-in-scroll, .stagger-children').forEach(el => {
            observer.observe(el);
        });
    }

    // Page load animations
    setupPageLoadAnimations() {
        // Add entrance animations to main content
        const mainContent = document.querySelector('main');
        if (mainContent) {
            mainContent.classList.add('animate-fade-in-up');
        }

        // Animate cards with stagger effect
        const cards = document.querySelectorAll('.content-card');
        cards.forEach((card, index) => {
            card.classList.add('card-enter');
            card.style.animationDelay = `${index * 0.1}s`;
        });

        // Animate navigation items
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach((item, index) => {
            item.classList.add('animate-fade-in-down');
            item.style.animationDelay = `${index * 0.1}s`;
        });
    }

    // Form animations and validation
    setupFormAnimations() {
        const inputs = document.querySelectorAll('.input-field');
        
        inputs.forEach(input => {
            // Focus animations
            input.addEventListener('focus', () => {
                input.classList.add('focus-ring');
            });

            input.addEventListener('blur', () => {
                input.classList.remove('focus-ring');
            });

            // Validation animations
            input.addEventListener('invalid', (e) => {
                e.preventDefault();
                input.classList.add('input-error');
                setTimeout(() => {
                    input.classList.remove('input-error');
                }, 500);
            });

            // Success animations
            input.addEventListener('input', () => {
                if (input.checkValidity()) {
                    input.classList.add('input-success');
                    setTimeout(() => {
                        input.classList.remove('input-success');
                    }, 1000);
                }
            });
        });

        // Form submission animations
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.classList.add('pulse');
                    submitBtn.disabled = true;
                }
            });
        });
    }

    // Enhanced button interactions
    setupButtonAnimations() {
        const buttons = document.querySelectorAll('.btn-primary, .btn-secondary, .btn-danger');
        
        buttons.forEach(button => {
            // Ripple effect
            button.addEventListener('click', (e) => {
                const ripple = document.createElement('span');
                const rect = button.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                button.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });

            // Hover effects
            button.addEventListener('mouseenter', () => {
                button.style.transform = 'translateY(-2px)';
            });

            button.addEventListener('mouseleave', () => {
                button.style.transform = 'translateY(0)';
            });
        });
    }

    // Navbar scroll effects
    setupNavbarAnimations() {
        const navbar = document.querySelector('.navbar');
        if (!navbar) return;

        let lastScrollY = window.scrollY;
        
        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;
            
            if (currentScrollY > 100) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }

            // Hide/show navbar on scroll
            if (currentScrollY > lastScrollY && currentScrollY > 200) {
                navbar.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.transform = 'translateY(0)';
            }

            lastScrollY = currentScrollY;
        });
    }

    // Theme transition animations
    setupThemeTransitions() {
        // Smooth theme switching
        const themeToggle = document.querySelector('[data-theme-toggle]');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                document.body.style.transition = 'all 0.3s ease';
                setTimeout(() => {
                    document.body.style.transition = '';
                }, 300);
            });
        }
    }

    // Micro-interactions and feedback
    setupMicroInteractions() {
        // Click feedback for interactive elements
        const clickableElements = document.querySelectorAll('.clickable, button, a, .card');
        clickableElements.forEach(element => {
            element.addEventListener('mousedown', () => {
                element.style.transform = 'scale(0.98)';
            });

            element.addEventListener('mouseup', () => {
                element.style.transform = 'scale(1)';
            });

            element.addEventListener('mouseleave', () => {
                element.style.transform = 'scale(1)';
            });
        });

        // Loading states
        this.setupLoadingStates();
    }

    // Loading animations
    setupLoadingStates() {
        // Show loading spinner for async operations
        window.showLoading = (element) => {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            
            if (element) {
                element.innerHTML = '<div class="spinner"></div> Loading...';
                element.classList.add('pulse');
            }
        };

        window.hideLoading = (element, originalContent) => {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            
            if (element) {
                element.innerHTML = originalContent;
                element.classList.remove('pulse');
            }
        };
    }

    // Utility methods
    static fadeIn(element, duration = 300) {
        element.style.opacity = '0';
        element.style.display = 'block';
        
        let start = performance.now();
        
        function animate(currentTime) {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            
            element.style.opacity = progress;
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        }
        
        requestAnimationFrame(animate);
    }

    static fadeOut(element, duration = 300) {
        let start = performance.now();
        const initialOpacity = parseFloat(getComputedStyle(element).opacity);
        
        function animate(currentTime) {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            
            element.style.opacity = initialOpacity * (1 - progress);
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                element.style.display = 'none';
            }
        }
        
        requestAnimationFrame(animate);
    }

    static slideIn(element, direction = 'up', duration = 300) {
        const directions = {
            up: 'translateY(30px)',
            down: 'translateY(-30px)',
            left: 'translateX(30px)',
            right: 'translateX(-30px)'
        };

        element.style.transform = directions[direction];
        element.style.opacity = '0';
        element.style.display = 'block';
        
        let start = performance.now();
        
        function animate(currentTime) {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            
            element.style.opacity = progress;
            element.style.transform = `translate(0, 0)`;
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        }
        
        requestAnimationFrame(animate);
    }

    // Toast notifications
    static showToast(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type} toast-enter`;
        toast.textContent = message;
        
        // Add styles
        Object.assign(toast.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            padding: '12px 20px',
            borderRadius: '8px',
            color: 'white',
            zIndex: '9999',
            transform: 'translateX(100%)',
            transition: 'transform 0.3s ease'
        });

        // Set background color based on type
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        toast.style.backgroundColor = colors[type] || colors.info;

        document.body.appendChild(toast);

        // Trigger animation
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 10);

        // Auto remove
        setTimeout(() => {
            toast.classList.add('toast-exit');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, duration);
    }

    // Success animation
    static showSuccess(element) {
        element.classList.add('input-success');
        setTimeout(() => {
            element.classList.remove('input-success');
        }, 1000);
    }

    // Error animation
    static showError(element) {
        element.classList.add('input-error');
        setTimeout(() => {
            element.classList.remove('input-error');
        }, 500);
    }

    // Gentle press effect for buttons
    static addPressEffect(button) {
        button.addEventListener('mousedown', () => {
            button.classList.add('press-scale');
        });
        
        button.addEventListener('mouseup', () => {
            button.classList.remove('press-scale');
        });
        
        button.addEventListener('mouseleave', () => {
            button.classList.remove('press-scale');
        });
    }

    // Loading pulse effect
    static addLoadingPulse(element) {
        element.classList.add('loading-pulse');
    }

    // Focus glow effect for inputs
    static addFocusGlow(input) {
        input.addEventListener('focus', () => {
            input.classList.add('focus-glow');
        });
        
        input.addEventListener('blur', () => {
            input.classList.remove('focus-glow');
        });
    }

    // Color fade animation
    static addColorFade(element, color) {
        element.classList.add('color-fade');
        setTimeout(() => {
            element.classList.remove('color-fade');
        }, 300);
    }

    // Opacity fade animation
    static addOpacityFade(element) {
        element.classList.add('opacity-fade');
    }
}

// Initialize animations when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new AnimationManager();
    
    // Add basic animations to all buttons
    document.querySelectorAll('.btn-primary, .btn-secondary, .btn-danger').forEach(button => {
        AnimationManager.addPressEffect(button);
    });
    
    // Add focus glow to all inputs
    document.querySelectorAll('.input-field').forEach(input => {
        AnimationManager.addFocusGlow(input);
    });
    
    // Add loading pulse to loading elements
    document.querySelectorAll('.loading').forEach(element => {
        AnimationManager.addLoadingPulse(element);
    });
});

// Export for use in other modules
window.AnimationManager = AnimationManager;

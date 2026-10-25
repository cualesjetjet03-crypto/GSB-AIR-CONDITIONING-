/**
 * Christening Invitation Website - Enhanced Interactive Script
 * Professional UI/UX with smooth animations and interactions
 */

// === Utility Functions ===
const utils = {
    // Debounce function for performance
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Check if element is in viewport
    isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
};

// === Modal Management Module ===
const RSVPModal = {
    modal: null,
    openButton: null,
    closeButton: null,
    isAnimating: false,

    init() {
        this.modal = document.getElementById('rsvpModal');
        this.openButton = document.querySelector('.rsvp-button');
        this.closeButton = document.querySelector('.close');

        if (!this.modal || !this.openButton || !this.closeButton) {
            console.error('Modal elements not found');
            return;
        }

        // Event listeners
        this.openButton.addEventListener('click', () => this.open());
        this.closeButton.addEventListener('click', () => this.close());
        
        // Close when clicking outside modal content
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.classList.contains('show')) {
                this.close();
            }
        });

        // Add ripple effect to open button
        this.addRippleEffect(this.openButton);
    },

    open() {
        if (this.isAnimating) return;
        this.isAnimating = true;
        
        this.modal.classList.add('show');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        
        // Focus first input
        setTimeout(() => {
            const firstInput = this.modal.querySelector('input');
            if (firstInput) firstInput.focus();
            this.isAnimating = false;
        }, 300);
    },

    close() {
        if (this.isAnimating) return;
        this.isAnimating = true;
        
        this.modal.classList.remove('show');
        document.body.style.overflow = ''; // Restore scrolling
        
        setTimeout(() => {
            this.isAnimating = false;
        }, 300);
    },

    // Add ripple effect to button
    addRippleEffect(button) {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => ripple.remove(), 600);
        });
    }
};

// === Form Validation Module ===
const FormValidator = {
    validate(formData) {
        const errors = [];

        // Validate name
        const name = formData.name?.trim();
        if (!name || name === '') {
            errors.push('Name is required');
        } else if (name.length < 2) {
            errors.push('Name must be at least 2 characters');
        } else if (name.length > 200) {
            errors.push('Name is too long (maximum 200 characters)');
        }

        // Validate guests
        const guests = formData.guests;
        const guestsNum = Number(guests);
        
        if (!guests || isNaN(guestsNum) || guestsNum < 1 || !Number.isInteger(guestsNum)) {
            errors.push('Number of guests must be a positive integer');
        } else if (guestsNum > 99) {
            errors.push('Number of guests cannot exceed 99');
        }

        // Validate message (optional but check length if provided)
        const message = formData.message?.trim();
        if (message && message.length > 500) {
            errors.push('Message is too long (maximum 500 characters)');
        }

        return {
            isValid: errors.length === 0,
            errors: errors
        };
    },

    // Real-time field validation
    validateField(field, value) {
        const fieldName = field.getAttribute('name');
        let error = null;

        switch (fieldName) {
            case 'name':
                if (!value || value.trim() === '') {
                    error = 'Name is required';
                } else if (value.length < 2) {
                    error = 'Name too short';
                }
                break;
            case 'guests':
                const num = Number(value);
                if (!value || isNaN(num) || num < 1) {
                    error = 'Enter a valid number';
                }
                break;
        }

        return error;
    }
};

// === RSVP Form Handler Module ===
const RSVPHandler = {
    form: null,
    messageDiv: null,
    submitButton: null,

    init() {
        this.form = document.getElementById('rsvpForm');
        this.messageDiv = document.querySelector('.form-message');
        this.submitButton = this.form?.querySelector('.submit-button');

        if (!this.form || !this.messageDiv) {
            console.error('Form elements not found');
            return;
        }

        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        // Add real-time validation
        this.addRealTimeValidation();
        
        // Add character counter for message field
        this.addCharacterCounter();
    },

    addRealTimeValidation() {
        const inputs = this.form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', (e) => {
                const error = FormValidator.validateField(e.target, e.target.value);
                if (error) {
                    e.target.style.borderColor = '#e74c3c';
                } else {
                    e.target.style.borderColor = '';
                }
            });

            input.addEventListener('input', (e) => {
                if (e.target.style.borderColor === 'rgb(231, 76, 60)') {
                    const error = FormValidator.validateField(e.target, e.target.value);
                    if (!error) {
                        e.target.style.borderColor = '';
                    }
                }
            });
        });
    },

    addCharacterCounter() {
        const messageField = this.form.querySelector('textarea[name="message"]');
        if (!messageField) return;

        const counter = document.createElement('div');
        counter.style.cssText = 'text-align: right; font-size: 0.85rem; color: #999; margin-top: 5px;';
        counter.textContent = '0 / 500';
        messageField.parentElement.appendChild(counter);

        messageField.addEventListener('input', () => {
            const length = messageField.value.length;
            counter.textContent = `${length} / 500`;
            counter.style.color = length > 450 ? '#e74c3c' : '#999';
        });
    },

    async handleSubmit(e) {
        e.preventDefault();

        // Clear previous messages
        this.clearMessage();

        // Extract form data
        const formData = new FormData(this.form);
        const data = {
            name: formData.get('name'),
            guests: formData.get('guests'),
            message: formData.get('message') || ''
        };

        // Validate form data
        const validation = FormValidator.validate(data);

        if (!validation.isValid) {
            this.showError(validation.errors.join('. '));
            // Shake animation
            this.messageDiv.style.animation = 'none';
            setTimeout(() => {
                this.messageDiv.style.animation = '';
            }, 10);
            return;
        }

        // Disable submit button during submission
        const originalText = this.submitButton.textContent;
        this.submitButton.disabled = true;
        this.submitButton.textContent = 'Submitting...';
        this.submitButton.style.opacity = '0.7';

        try {
            // Send RSVP to backend
            const response = await fetch('submit_rsvp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccess('🎉 Thank you for your RSVP! We look forward to celebrating with you.');
                this.form.reset();
                
                // Trigger confetti effect
                this.triggerConfetti();
                
                // Close modal after 2.5 seconds
                setTimeout(() => {
                    RSVPModal.close();
                    setTimeout(() => this.clearMessage(), 500);
                }, 2500);
            } else {
                this.showError(result.message || 'Submission failed. Please try again.');
            }
        } catch (error) {
            console.error('RSVP submission error:', error);
            this.showError('❌ Network error. Please check your connection and try again.');
        } finally {
            // Re-enable submit button
            this.submitButton.disabled = false;
            this.submitButton.textContent = originalText;
            this.submitButton.style.opacity = '1';
        }
    },

    showError(message) {
        this.messageDiv.textContent = message;
        this.messageDiv.className = 'form-message error';
    },

    showSuccess(message) {
        this.messageDiv.textContent = message;
        this.messageDiv.className = 'form-message success';
    },

    clearMessage() {
        this.messageDiv.textContent = '';
        this.messageDiv.className = 'form-message';
    },

    triggerConfetti() {
        // Simple confetti effect
        const colors = ['#5b8db8', '#b8d4e8', '#fde8e8', '#e3f0f7'];
        const confettiCount = 50;

        for (let i = 0; i < confettiCount; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.style.cssText = `
                    position: fixed;
                    width: 10px;
                    height: 10px;
                    background: ${colors[Math.floor(Math.random() * colors.length)]};
                    top: -10px;
                    left: ${Math.random() * 100}%;
                    opacity: 1;
                    border-radius: 50%;
                    pointer-events: none;
                    z-index: 9999;
                `;
                document.body.appendChild(confetti);

                const fallDuration = 2000 + Math.random() * 1000;
                confetti.animate([
                    { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
                    { transform: `translateY(${window.innerHeight + 20}px) rotate(${Math.random() * 360}deg)`, opacity: 0 }
                ], {
                    duration: fallDuration,
                    easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
                });

                setTimeout(() => confetti.remove(), fallDuration);
            }, i * 30);
        }
    }
};

// === Audio Controller Module ===
const AudioController = {
    audio: null,
    toggleButton: null,
    audioIcon: null,
    isPlaying: false,

    init() {
        this.audio = document.getElementById('backgroundMusic');
        this.toggleButton = document.querySelector('.audio-toggle');
        this.audioIcon = document.querySelector('.audio-icon');

        if (!this.audio || !this.toggleButton || !this.audioIcon) {
            console.error('Audio elements not found');
            return;
        }

        // Event listener
        this.toggleButton.addEventListener('click', () => this.toggle());

        // Handle audio errors gracefully
        this.audio.addEventListener('error', (e) => {
            console.warn('Audio playback error:', e);
            this.toggleButton.style.opacity = '0.5';
            this.toggleButton.style.cursor = 'not-allowed';
            this.toggleButton.title = 'Audio not available';
        });

        // Show playing animation
        this.audio.addEventListener('play', () => {
            this.toggleButton.style.animation = 'gentle-pulse 2s ease-in-out infinite';
        });

        this.audio.addEventListener('pause', () => {
            this.toggleButton.style.animation = '';
        });
    },

    toggle() {
        if (this.isPlaying) {
            this.pause();
        } else {
            this.play();
        }
    },

    play() {
        this.audio.play()
            .then(() => {
                this.isPlaying = true;
                this.audioIcon.textContent = '🔊';
                this.toggleButton.title = 'Pause music';
            })
            .catch((error) => {
                console.warn('Could not play audio:', error);
                alert('Audio playback requires user interaction. Please click the button again.');
            });
    },

    pause() {
        this.audio.pause();
        this.isPlaying = false;
        this.audioIcon.textContent = '🔇';
        this.toggleButton.title = 'Play music';
    }
};

// === Scroll Animations Module ===
const ScrollAnimations = {
    elements: [],

    init() {
        this.elements = document.querySelectorAll('section, .header');
        this.observeElements();
    },

    observeElements() {
        const options = {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, options);

        this.elements.forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
            observer.observe(element);
        });
    }
};

// === Parallax Effect Module ===
const ParallaxEffect = {
    init() {
        window.addEventListener('scroll', utils.debounce(() => {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.baby-photo-container, .decorative-elements');
            
            parallaxElements.forEach(element => {
                const speed = 0.5;
                element.style.transform = `translateY(${scrolled * speed * 0.1}px)`;
            });
        }, 10));
    }
};

// === Smooth Scroll Module ===
const SmoothScroll = {
    init() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
};

// === Performance Optimization ===
const PerformanceOptimizer = {
    init() {
        // Lazy load images
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                img.src = img.dataset.src;
            });
        }

        // Reduce animations on low-end devices
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        if (prefersReducedMotion.matches) {
            document.body.style.setProperty('--animation-duration', '0s');
        }
    }
};

// === Page Initialization ===
document.addEventListener('DOMContentLoaded', () => {
    console.log('🎉 Christening Invitation Website - Professional Edition');

    // Initialize all modules
    RSVPModal.init();
    RSVPHandler.init();
    AudioController.init();
    ScrollAnimations.init();
    ParallaxEffect.init();
    SmoothScroll.init();
    PerformanceOptimizer.init();

    // Add loading complete class
    document.body.classList.add('loaded');

    console.log('✅ All modules initialized successfully');
    console.log('💙 Made with love for Baby Calix');
});

// === Service Worker Registration (optional - for offline support) ===
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        // Uncomment to enable offline support
        // navigator.serviceWorker.register('/sw.js');
    });
}

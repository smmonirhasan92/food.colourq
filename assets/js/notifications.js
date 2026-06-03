/**
 * ==========================================================================
 * REAL-TIME NOTIFICATION & SOUND SYNTHESIZER FALLBACK SYSTEM
 * Includes custom browser-native Audio synthesis so alert sounds play 
 * seamlessly without relying on physical static audio asset loading.
 * ==========================================================================
 */

class WebAudioSynthesizer {
    constructor() {
        this.audioCtx = null;
    }

    /**
     * Initializes the dynamic AudioContext safe from browser auto-play blocks.
     */
    init() {
        if (!this.audioCtx) {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (AudioContextClass) {
                this.audioCtx = new AudioContextClass();
            }
        }
        if (this.audioCtx && this.audioCtx.state === 'suspended') {
            this.audioCtx.resume();
        }
    }

    /**
     * Plays a synthesized chime pattern based on event notification type.
     * @param {string} type - 'success', 'warning', 'error', or 'info'
     */
    playChime(type = 'info') {
        try {
            // Sound notifications play ONLY inside the admin panel
            if (!window.location.pathname.includes('/admin/')) {
                return;
            }

            this.init();
            if (!this.audioCtx) return;

            const now = this.audioCtx.currentTime;

            switch (type) {
                case 'success':
                    this.synthesizeSuccessMelody(now);
                    break;
                case 'warning':
                    this.synthesizeWarningBeeps(now);
                    break;
                case 'error':
                    this.synthesizeErrorAlert(now);
                    break;
                case 'info':
                default:
                    this.synthesizeModernChime(now);
                    break;
            }
        } catch (error) {
            console.warn('[WebAudio Synth] Playback blocked or unsupported:', error);
        }
    }

    /**
     * Elegant high-frequency crystal bell chime.
     */
    synthesizeModernChime(startTime) {
        // Fundamental tone
        this.createOscillator(880, 'sine', 0.15, startTime, 0.6); // A5
        // Subtle harmonic overtone (an octave above)
        this.createOscillator(1760, 'sine', 0.05, startTime, 0.4); // A6
    }

    /**
     * Upward bright, happy two-note melodic chime.
     */
    synthesizeSuccessMelody(startTime) {
        // First note (E5)
        this.createOscillator(659.25, 'sine', 0.12, startTime, 0.3);
        // Second note (B5) starting shortly after
        this.createOscillator(987.77, 'sine', 0.15, startTime + 0.12, 0.5);
    }

    /**
     * Double cautionary tone.
     */
    synthesizeWarningBeeps(startTime) {
        // Double pulse at caution pitch (D5)
        this.createOscillator(587.33, 'triangle', 0.15, startTime, 0.15);
        this.createOscillator(587.33, 'triangle', 0.15, startTime + 0.22, 0.15);
    }

    /**
     * Deep descending buzzer-like alert.
     */
    synthesizeErrorAlert(startTime) {
        const osc = this.audioCtx.createOscillator();
        const gain = this.audioCtx.createGain();

        osc.type = 'sawtooth';
        // Descend pitch from 320Hz down to 180Hz
        osc.frequency.setValueAtTime(320, startTime);
        osc.frequency.exponentialRampToValueAtTime(180, startTime + 0.45);

        // Harsher sound requires lower gain limit
        gain.gain.setValueAtTime(0.08, startTime);
        gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.45);

        osc.connect(gain);
        gain.connect(this.audioCtx.destination);

        osc.start(startTime);
        osc.stop(startTime + 0.5);
    }

    /**
     * Helper to spawn oscillator events quickly.
     */
    createOscillator(frequency, type, volume, time, duration) {
        const osc = this.audioCtx.createOscillator();
        const gainNode = this.audioCtx.createGain();

        osc.type = type;
        osc.frequency.setValueAtTime(frequency, time);

        // Smooth amplitude envelope
        gainNode.gain.setValueAtTime(0.001, time);
        // Quick ramp up to target volume
        gainNode.gain.linearRampToValueAtTime(volume, time + 0.02);
        // Exponential fade out
        gainNode.gain.exponentialRampToValueAtTime(0.001, time + duration - 0.02);

        osc.connect(gainNode);
        gainNode.connect(this.audioCtx.destination);

        osc.start(time);
        osc.stop(time + duration);
    }
}

class NotificationToastManager {
    constructor() {
        this.synth = new WebAudioSynthesizer();
        this.container = null;
        this.createContainer();
    }

    /**
     * Builds and appends toast container DOM dynamically.
     */
    createContainer() {
        this.container = document.querySelector('.toast-container');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        }
    }

    /**
     * Displays a dynamic toast notification card and triggers sound synthesizers.
     * @param {string} type - 'success', 'warning', 'error', or 'info'
     * @param {string} title - Heading of toast notice
     * @param {string} message - Informative body message
     * @param {number} duration - Time before decay in milliseconds
     */
    toast(type = 'info', title = 'System Update', message = '', duration = 4000) {
        this.createContainer();

        const toastElement = document.createElement('div');
        toastElement.className = `toast-message glass-panel toast-${type}`;

        // Select FontAwesome / Simple SVGs for class states
        let iconClass = 'fa-info-circle';
        if (type === 'success') iconClass = 'fa-check-circle';
        if (type === 'warning') iconClass = 'fa-exclamation-triangle';
        if (type === 'error') iconClass = 'fa-times-circle';

        toastElement.innerHTML = `
            <div class="toast-icon">
                <i class="fas ${iconClass}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-body">${message}</div>
            </div>
            <div class="toast-close">
                <i class="fas fa-times"></i>
            </div>
        `;

        // Handle direct toast close button trigger
        const closeBtn = toastElement.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                this.dismissToast(toastElement);
            });
        }

        this.container.appendChild(toastElement);
        
        // Play premium synthesized alert
        this.synth.playChime(type);

        // Auto collapse after duration expires
        setTimeout(() => {
            if (toastElement.parentNode) {
                this.dismissToast(toastElement);
            }
        }, duration);

        return toastElement;
    }

    /**
     * Smoothly animate and clear elements out of display nodes.
     */
    dismissToast(toastElement) {
        toastElement.style.animation = 'toast-fade-out 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards';
        setTimeout(() => {
            if (toastElement.parentNode) {
                toastElement.remove();
            }
        }, 300);
    }
}

// Instantiate globally so it is accessible inside dashboard interfaces
window.NotificationSystem = new NotificationToastManager();

// Automatically listen to standard system interactions
document.addEventListener('click', () => {
    // Interactive unlock for Audio Contexts standard on modern browsers
    if (window.NotificationSystem && window.NotificationSystem.synth) {
        window.NotificationSystem.synth.init();
    }
}, { once: true });

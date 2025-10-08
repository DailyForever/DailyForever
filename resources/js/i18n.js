/**
 * Internationalization (i18n) System
 * Handles dynamic language switching and translation loading
 */

class I18nManager {
    constructor() {
        this.currentLang = 'en';
        this.translations = {};
        this.fallbackLang = 'en';
        this.supportedLangs = ['en', 'es'];
        this.loadedLangs = new Set();
    }

    /**
     * Initialize the i18n system
     */
    async init() {
        // Get language from localStorage, URL params, or browser
        this.currentLang = this.detectLanguage();
        
        // Load the current language translations
        await this.loadLanguage(this.currentLang);
        
        // Apply translations to the DOM
        this.translatePage();
        
        // Set up language switcher if it exists
        this.setupLanguageSwitcher();
        
        // Mark as initialized
        document.documentElement.setAttribute('data-i18n-loaded', 'true');
    }

    /**
     * Detect the user's preferred language
     */
    detectLanguage() {
        // Check localStorage first
        const stored = localStorage.getItem('preferred_language');
        if (stored && this.supportedLangs.includes(stored)) {
            return stored;
        }
        
        // Check URL parameter
        const params = new URLSearchParams(window.location.search);
        const urlLang = params.get('lang');
        if (urlLang && this.supportedLangs.includes(urlLang)) {
            localStorage.setItem('preferred_language', urlLang);
            return urlLang;
        }
        
        // Check browser language
        const browserLang = navigator.language.split('-')[0];
        if (this.supportedLangs.includes(browserLang)) {
            return browserLang;
        }
        
        // Default to English
        return this.fallbackLang;
    }

    /**
     * Load translations for a specific language
     */
    async loadLanguage(lang) {
        if (this.loadedLangs.has(lang)) {
            return; // Already loaded
        }
        
        try {
            // Load translations from JSON file
            const response = await fetch(`/locales/${lang}.json`);
            if (!response.ok) {
                throw new Error(`Failed to load language file: ${response.status}`);
            }
            this.translations[lang] = await response.json();
            this.loadedLangs.add(lang);
            console.log(`Loaded language: ${lang}`);
        } catch (error) {
            console.error(`Failed to load language ${lang}:`, error);
            // Try embedded translations as fallback
            try {
                this.translations[lang] = this.getEmbeddedTranslations(lang);
                this.loadedLangs.add(lang);
                console.log(`Using embedded translations for ${lang}`);
            } catch (embeddedError) {
                // Fall back to English if loading fails
                if (lang !== this.fallbackLang) {
                    await this.loadLanguage(this.fallbackLang);
                }
            }
        }
    }

    /**
     * Get embedded translations (temporary until API is ready)
     */
    getEmbeddedTranslations(lang) {
        const translations = {
            en: {
                // Navigation
                'nav.home': 'Home',
                'nav.new_paste': 'New Paste',
                'nav.new_file': 'New File',
                'nav.prekeys': 'Prekeys',
                'nav.how_it_works': 'How It Works',
                'nav.login': 'Login',
                'nav.register': 'Register',
                'nav.dashboard': 'Dashboard',
                'nav.logout': 'Logout',
                
                // Paste creation
                'paste.create.section_title': 'Create Secure Paste',
                'paste.create.section_desc': 'Your content is encrypted client-side before sending. Zero-knowledge architecture ensures complete privacy.',
                'paste.create.guest.title': 'Guest Mode',
                'paste.create.guest.message': 'You\'re creating a paste as a guest.',
                'paste.create.guest.link': 'Register or login to access more features',
                'paste.create.tip_shortcut': 'Tip: Press Ctrl+Enter to quickly create paste',
                'paste.create.password_note': 'Optional password adds an extra layer of access control',
                'paste.create.login_private': 'Login to create private pastes',
                'paste.create.cta_create': 'Create Paste',
                
                // Editor
                'editor.editor': 'Editor',
                'editor.line_numbers': 'Line Numbers',
                'editor.word_wrap': 'Word Wrap',
                'editor.syntax': 'Syntax',
                
                // Security features
                'security.encryption': 'End-to-End Encryption',
                'security.zero_knowledge': 'Zero-Knowledge',
                'security.key_rotation': 'Automatic Key Rotation',
                'security.random_validation': 'Secure Random Validation',
                
                // Common
                'common.loading': 'Loading...',
                'common.error': 'Error',
                'common.success': 'Success',
                'common.copy': 'Copy',
                'common.copied': 'Copied!',
                'common.close': 'Close',
                'common.save': 'Save',
                'common.cancel': 'Cancel',
                'common.delete': 'Delete',
                'common.confirm': 'Confirm',
                'common.options.title': 'Options',
                'common.options.expiration': 'Expiration',
                'common.options.never': 'Never',
                'common.options.1hour': '1 Hour',
                'common.options.1day': '1 Day',
                'common.options.1week': '1 Week',
                'common.options.1month': '1 Month',
                'common.password.label_optional': 'Password (Optional)',
                'common.view_policy.label': 'View Policy',
                'common.view_policy.multiple': 'Multiple Views',
                'common.view_policy.once': 'View Once',
                'common.security_pillars': 'Security First',
                
                // Theme
                'theme.dark': 'Dark',
                'theme.light': 'Light',
                
                // Footer
                'footer.brand.name': 'DailyForever',
                'footer.brand.tagline': 'Secure, Private, Forever',
                'footer.brand.description': 'End-to-end encrypted sharing platform',
                'footer.product': 'Product',
                'footer.learn': 'Learn',
                'footer.how_it_works': 'How It Works',
                'footer.roadmap': 'Roadmap',
                'footer.faq': 'FAQ',
                'footer.blog': 'Blog',
                'footer.support': 'Support',
                'footer.legal': 'Legal',
                'footer.terms': 'Terms',
                'footer.privacy': 'Privacy',
                'footer.cookies': 'Cookies',
                'footer.acceptable_use': 'Acceptable Use',
                'footer.no_logs': 'No Logs',
                'footer.dmca': 'DMCA',
                'footer.philosophy': 'Philosophy',
                'footer.tagline': 'Built with privacy in mind',
                'footer.e2e': 'End-to-End Encrypted',
                'footer.zk': 'Zero-Knowledge',
                'footer.no_data_collection': 'No Data Collection'
            },
            es: {
                // Navigation
                'nav.home': 'Inicio',
                'nav.create_paste': 'Crear Pasta',
                'nav.upload_file': 'Subir Archivo',
                'nav.how_it_works': 'Cómo Funciona',
                'nav.login': 'Iniciar Sesión',
                'nav.register': 'Registrarse',
                'nav.dashboard': 'Panel',
                'nav.logout': 'Cerrar Sesión',
                
                // Paste creation
                'paste.create.section_title': 'Crear Pasta Segura',
                'paste.create.section_desc': 'Tu contenido se cifra en el cliente antes de enviarse. La arquitectura de conocimiento cero garantiza privacidad completa.',
                'paste.create.guest.title': 'Modo Invitado',
                'paste.create.guest.message': 'Estás creando una pasta como invitado.',
                'paste.create.guest.link': 'Regístrate o inicia sesión para acceder a más funciones',
                
                // Editor
                'editor.editor': 'Editor',
                'editor.line_numbers': 'Números de Línea',
                'editor.word_wrap': 'Ajuste de Línea',
                'editor.syntax': 'Sintaxis',
                
                // Security features
                'security.encryption': 'Cifrado de Extremo a Extremo',
                'security.zero_knowledge': 'Conocimiento Cero',
                'security.key_rotation': 'Rotación Automática de Claves',
                'security.random_validation': 'Validación de Aleatoriedad Segura',
                
                // Common
                'common.loading': 'Cargando...',
                'common.error': 'Error',
                'common.success': 'Éxito',
                'common.copy': 'Copiar',
                'common.copied': '¡Copiado!',
                'common.close': 'Cerrar',
                'common.save': 'Guardar',
                'common.cancel': 'Cancelar',
                'common.delete': 'Eliminar',
                'common.confirm': 'Confirmar'
            }
        };
        
        return translations[lang] || translations[this.fallbackLang];
    }

    /**
     * Translate all elements on the page
     */
    translatePage() {
        const elements = document.querySelectorAll('[data-i18n]');
        console.log(`Translating ${elements.length} elements for language: ${this.currentLang}`);
        
        elements.forEach(element => {
            const key = element.getAttribute('data-i18n');
            const translation = this.translate(key);
            if (translation && translation !== key) {
                // Check if it's an input/textarea placeholder
                if (element.hasAttribute('placeholder')) {
                    element.placeholder = translation;
                } else if (element.hasAttribute('title')) {
                    element.title = translation;
                } else if (element.hasAttribute('aria-label')) {
                    element.setAttribute('aria-label', translation);
                } else {
                    // Regular text content
                    element.textContent = translation;
                }
            }
        });
        
        // Update document title if specified
        const titleElements = document.querySelectorAll('[data-i18n-doc-title]');
        titleElements.forEach(element => {
            const key = element.getAttribute('data-i18n-doc-title');
            const translation = this.translate(key);
            if (translation) {
                document.title = translation;
            }
        });
        
        // Update HTML lang attribute
        document.documentElement.lang = this.currentLang;
    }

    /**
     * Get a translation by key
     */
    translate(key, params = {}) {
        // First, check if translations are loaded
        if (!this.translations[this.currentLang]) {
            console.warn(`No translations loaded for language: ${this.currentLang}`);
            return key;
        }
        
        const keys = key.split('.');
        let translation = this.translations[this.currentLang];
        
        // Navigate through nested keys
        for (const k of keys) {
            if (translation && typeof translation === 'object') {
                translation = translation[k];
            } else {
                translation = null;
                break;
            }
        }
        
        // If not found, try fallback language
        if (!translation && this.currentLang !== this.fallbackLang) {
            translation = this.getFallbackTranslation(key);
        }
        
        // Replace parameters if any
        if (typeof translation === 'string') {
            Object.keys(params).forEach(param => {
                translation = translation.replace(`{${param}}`, params[param]);
            });
            return translation;
        }
        
        // Return the key if no translation found (for debugging)
        console.warn(`Translation not found for key: ${key}`);
        return key;
    }

    /**
     * Get translation from fallback language
     */
    getFallbackTranslation(key) {
        const keys = key.split('.');
        let translation = this.translations[this.fallbackLang];
        
        for (const k of keys) {
            if (translation && typeof translation === 'object') {
                translation = translation[k];
            } else {
                return null;
            }
        }
        
        return translation;
    }

    /**
     * Set up language switcher functionality
     */
    setupLanguageSwitcher() {
        const switchers = document.querySelectorAll('[data-lang-switch]');
        switchers.forEach(switcher => {
            switcher.addEventListener('click', (e) => {
                e.preventDefault();
                const lang = switcher.getAttribute('data-lang-switch');
                if (lang && this.supportedLangs.includes(lang)) {
                    this.switchLanguage(lang);
                }
            });
        });
        
        // Update active state
        this.updateLanguageSwitcherState();
    }

    /**
     * Switch to a different language
     */
    async switchLanguage(lang) {
        if (lang === this.currentLang) return;
        
        // Save preference
        localStorage.setItem('preferred_language', lang);
        this.currentLang = lang;
        
        // Load new language if not already loaded
        await this.loadLanguage(lang);
        
        // Retranslate the page
        this.translatePage();
        
        // Update switcher state
        this.updateLanguageSwitcherState();
        
        // Emit language change event
        window.dispatchEvent(new CustomEvent('languageChanged', { 
            detail: { language: lang } 
        }));
    }

    /**
     * Update language switcher active states
     */
    updateLanguageSwitcherState() {
        const switchers = document.querySelectorAll('[data-lang-switch]');
        switchers.forEach(switcher => {
            const lang = switcher.getAttribute('data-lang-switch');
            if (lang === this.currentLang) {
                switcher.classList.add('active', 'font-bold');
                switcher.classList.remove('text-yt-text-secondary');
            } else {
                switcher.classList.remove('active', 'font-bold');
                switcher.classList.add('text-yt-text-secondary');
            }
        });
    }

    /**
     * Get current language
     */
    getCurrentLanguage() {
        return this.currentLang;
    }

    /**
     * Get all supported languages
     */
    getSupportedLanguages() {
        return this.supportedLangs;
    }

    /**
     * Check if a language is supported
     */
    isLanguageSupported(lang) {
        return this.supportedLangs.includes(lang);
    }
}

// Create and export singleton instance
const i18n = new I18nManager();

// Auto-initialize when DOM is ready
if (typeof window !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            i18n.init();
        });
    } else {
        i18n.init();
    }
}

// Export for use in other modules
export default i18n;

// Also attach to window for global access
if (typeof window !== 'undefined') {
    window.i18n = i18n;
}

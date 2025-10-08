<div id="cookies-consent" class="fixed bottom-0 left-0 right-0 z-50 p-4 bg-yt-bg border-t border-yt-border shadow-lg transform translate-y-full transition-transform duration-300" style="display: none;">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-yt-text mb-2" data-i18n="cookies.banner.title">🍪 Cookie Consent</h3>
                <p class="text-sm text-theme-switch-important leading-relaxed" data-i18n="cookies.banner.desc">
                    We use essential cookies to ensure our website functions properly and analytics cookies to help us improve your experience. By clicking "Accept All", you consent to our use of cookies. You can customize your preferences or learn more in our Privacy Policy.
                </p>
                <a href="{{ route('legal.privacy') }}" class="text-blue-600 dark:text-yt-accent hover:underline font-medium" data-i18n="support.info.quick.privacy">Privacy Policy</a>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button 
                    id="cookies-customize" 
                    class="px-4 py-2 text-sm font-medium text-yt-text border border-yt-border rounded-lg hover:bg-yt-surface transition-colors"
                >
                    <span data-i18n="cookies.buttons.customize">Customize</span>
                </button>
                <button 
                    id="cookies-accept-necessary" 
                    class="px-4 py-2 text-sm font-medium text-yt-text border border-yt-border rounded-lg hover:bg-yt-surface transition-colors"
                >
                    <span data-i18n="cookies.buttons.necessary_only">Necessary Only</span>
                </button>
                <button 
                    id="cookies-accept-all" 
                    class="px-4 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 dark:bg-yt-accent dark:hover:bg-yt-accent/90 rounded-lg transition-colors"
                >
                    <span data-i18n="cookies.buttons.accept_all">Accept All</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cookie Preferences Modal -->
<div id="cookie-preferences-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="content-card max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-yt-text" data-i18n="cookies.modal.title">Cookie Preferences</h2>
                    <button id="close-cookie-modal" class="text-yt-text-secondary hover:text-yt-text">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-6">
                    <!-- Essential Cookies -->
                    <div class="border border-yt-border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="font-medium text-yt-text" data-i18n="cookies.modal.essential.title">Essential Cookies</h3>
                                <p class="text-sm text-yt-text-secondary" data-i18n="cookies.modal.essential.desc">Required for basic website functionality</p>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm text-yt-text-secondary mr-2" data-i18n="cookies.modal.always_active">Always Active</span>
                                <div class="w-10 h-6 bg-yt-accent rounded-full flex items-center justify-end px-1">
                                    <div class="w-4 h-4 bg-white rounded-full"></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-yt-text-secondary">
                            These cookies are necessary for the website to function and cannot be switched off. They include session cookies, security cookies, and CSRF protection.
                        </p>
                    </div>

                    <!-- Analytics Cookies -->
                    <div class="border border-yt-border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="font-medium text-yt-text" data-i18n="cookies.modal.analytics.title">Analytics Cookies</h3>
                                <p class="text-sm text-yt-text-secondary" data-i18n="cookies.modal.analytics.desc">Help us understand how visitors interact with our website</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="analytics-cookies" class="sr-only peer">
                                <div class="w-10 h-6 bg-gray-200 dark:bg-gray-700 rounded-full peer peer-checked:bg-yt-accent peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                        </div>
                        <p class="text-xs text-yt-text-secondary">
                            These cookies collect information about how you use our website, such as which pages you visit most often. This helps us improve our website's performance and user experience.
                        </p>
                    </div>

                    <!-- Marketing Cookies -->
                    <div class="border border-yt-border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="font-medium text-yt-text" data-i18n="cookies.modal.marketing.title">Marketing Cookies</h3>
                                <p class="text-sm text-yt-text-secondary" data-i18n="cookies.modal.marketing.desc">Used to track visitors across websites for advertising purposes</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="marketing-cookies" class="sr-only peer">
                                <div class="w-10 h-6 bg-gray-200 dark:bg-gray-700 rounded-full peer peer-checked:bg-yt-accent peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                        </div>
                        <p class="text-xs text-yt-text-secondary">
                            These cookies are used to track visitors across websites to display relevant and engaging advertisements.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-8">
                    <button 
                        id="save-cookie-preferences" 
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-yt-accent rounded-lg hover:bg-yt-accent/90 transition-colors"
                    >
                        <span data-i18n="cookies.modal.save">Save Preferences</span>
                    </button>
                    <button 
                        id="accept-all-cookies" 
                        class="flex-1 px-4 py-2 text-sm font-bold text-yt-text border border-yt-border rounded-lg hover:bg-yt-surface transition-colors"
                    >
                        <span data-i18n="cookies.buttons.accept_all">Accept All</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const consentBanner = document.getElementById('cookies-consent');
    const preferencesModal = document.getElementById('cookie-preferences-modal');
    const closeModal = document.getElementById('close-cookie-modal');
    const customizeBtn = document.getElementById('cookies-customize');
    const acceptNecessaryBtn = document.getElementById('cookies-accept-necessary');
    const acceptAllBtn = document.getElementById('cookies-accept-all');
    const savePreferencesBtn = document.getElementById('save-cookie-preferences');
    const acceptAllModalBtn = document.getElementById('accept-all-cookies');
    
    const analyticsCheckbox = document.getElementById('analytics-cookies');
    const marketingCheckbox = document.getElementById('marketing-cookies');

    // Check if user has already made a choice
    const cookieConsent = localStorage.getItem('cookieConsent');
    
    if (!cookieConsent) {
        // Show banner after a short delay
        setTimeout(() => {
            consentBanner.style.display = 'block';
            setTimeout(() => {
                consentBanner.classList.remove('translate-y-full');
            }, 100);
        }, 1000);
    } else {
        // Apply saved preferences
        const preferences = JSON.parse(cookieConsent);
        applyCookiePreferences(preferences);
    }

    // Event listeners
    customizeBtn.addEventListener('click', () => {
        preferencesModal.classList.remove('hidden');
        // Set current preferences
        const currentPreferences = getCurrentPreferences();
        analyticsCheckbox.checked = currentPreferences.analytics;
        marketingCheckbox.checked = currentPreferences.marketing;
    });

    closeModal.addEventListener('click', () => {
        preferencesModal.classList.add('hidden');
    });

    acceptNecessaryBtn.addEventListener('click', () => {
        saveCookiePreferences({ necessary: true, analytics: false, marketing: false });
        hideBanner();
    });

    acceptAllBtn.addEventListener('click', () => {
        saveCookiePreferences({ necessary: true, analytics: true, marketing: true });
        hideBanner();
    });

    savePreferencesBtn.addEventListener('click', () => {
        const preferences = {
            necessary: true,
            analytics: analyticsCheckbox.checked,
            marketing: marketingCheckbox.checked
        };
        saveCookiePreferences(preferences);
        hideBanner();
        preferencesModal.classList.add('hidden');
    });

    acceptAllModalBtn.addEventListener('click', () => {
        saveCookiePreferences({ necessary: true, analytics: true, marketing: true });
        hideBanner();
        preferencesModal.classList.add('hidden');
    });

    // Close modal when clicking outside
    preferencesModal.addEventListener('click', (e) => {
        if (e.target === preferencesModal) {
            preferencesModal.classList.add('hidden');
        }
    });

    function saveCookiePreferences(preferences) {
        localStorage.setItem('cookieConsent', JSON.stringify(preferences));
        applyCookiePreferences(preferences);
    }

    function getCurrentPreferences() {
        const saved = localStorage.getItem('cookieConsent');
        if (saved) {
            return JSON.parse(saved);
        }
        return { necessary: true, analytics: false, marketing: false };
    }

    function applyCookiePreferences(preferences) {
        // Enable/disable Google Analytics based on preferences
        if (preferences.analytics && typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted'
            });
        } else if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'denied'
            });
        }

        // Enable/disable marketing cookies
        if (preferences.marketing && typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'ad_storage': 'granted'
            });
        } else if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'ad_storage': 'denied'
            });
        }
    }

    function hideBanner() {
        consentBanner.classList.add('translate-y-full');
        setTimeout(() => {
            consentBanner.style.display = 'none';
        }, 300);
    }
});
</script>

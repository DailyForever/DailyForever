// Privacy-focused Analytics for DailyForever
// No-logs policy: No PII, no tracking cookies, anonymized data only
class Analytics {
    constructor() {
        this.isGA4Loaded = typeof gtag !== 'undefined';
        this.consentGiven = this.checkConsent();
        this.sessionId = this.generateSessionId();
    }

    // Check if user has given consent for analytics
    checkConsent() {
        // Check localStorage for consent status
        const consent = localStorage.getItem('analytics_consent');
        return consent === 'granted';
    }

    // Update consent status
    updateConsent(status) {
        this.consentGiven = status;
        localStorage.setItem('analytics_consent', status ? 'granted' : 'denied');
        
        if (this.isGA4Loaded) {
            gtag('consent', 'update', {
                'analytics_storage': status ? 'granted' : 'denied'
            });
        }
    }

    // Generate anonymous session ID (not persistent across sessions)
    generateSessionId() {
        return 'sess_' + Math.random().toString(36).substr(2, 9);
    }

    // Hash function to anonymize identifiers
    hashId(id) {
        if (!id) return 'anonymous';
        // Simple hash to anonymize IDs - no way to reverse
        let hash = 0;
        for (let i = 0; i < id.length; i++) {
            const char = id.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32bit integer
        }
        return 'hash_' + Math.abs(hash).toString(36);
    }

    // Track paste creation (anonymized)
    trackPasteCreated(pasteId, isPrivate = false, hasPassword = false) {
        if (!this.isGA4Loaded || !this.consentGiven) return;
        
        gtag('event', 'paste_created', {
            'event_category': 'engagement',
            'event_label': this.hashId(pasteId), // Anonymized ID
            'custom_parameter_1': isPrivate ? 'private' : 'public',
            'custom_parameter_2': hasPassword ? 'password_protected' : 'no_password',
            'session_id': this.sessionId,
            'anonymize_ip': true
        });
    }

    // Track file upload (anonymized)
    trackFileUploaded(fileId, fileSize, fileType) {
        if (!this.isGA4Loaded || !this.consentGiven) return;
        
        // Only track file type and size range, not exact size
        const sizeCategory = this.categorizeFileSize(fileSize);
        const cleanFileType = this.sanitizeFileType(fileType);
        
        gtag('event', 'file_uploaded', {
            'event_category': 'engagement',
            'event_label': this.hashId(fileId), // Anonymized ID
            'custom_parameter_1': cleanFileType,
            'custom_parameter_2': sizeCategory,
            'session_id': this.sessionId,
            'anonymize_ip': true
        });
    }

    // Track paste view (anonymized)
    trackPasteViewed(pasteId, isPrivate = false) {
        if (!this.isGA4Loaded || !this.consentGiven) return;
        
        gtag('event', 'paste_viewed', {
            'event_category': 'engagement',
            'event_label': this.hashId(pasteId), // Anonymized ID
            'custom_parameter_1': isPrivate ? 'private' : 'public',
            'session_id': this.sessionId,
            'anonymize_ip': true
        });
    }

    // Track file download (anonymized)
    trackFileDownloaded(fileId, fileType) {
        if (!this.isGA4Loaded || !this.consentGiven) return;
        
        const cleanFileType = this.sanitizeFileType(fileType);
        
        gtag('event', 'file_downloaded', {
            'event_category': 'engagement',
            'event_label': this.hashId(fileId), // Anonymized ID
            'custom_parameter_1': cleanFileType,
            'session_id': this.sessionId,
            'anonymize_ip': true
        });
    }

    // Track user registration (no PII)
    trackUserRegistered(method = 'email') {
        if (!this.isGA4Loaded || !this.consentGiven) return;
        
        gtag('event', 'sign_up', {
            'event_category': 'engagement',
            'method': method,
            'session_id': this.sessionId,
            'anonymize_ip': true
        });
    }

    // Track user login (no PII)
    trackUserLogin(method = 'email') {
        if (!this.isGA4Loaded || !this.consentGiven) return;
        
        gtag('event', 'login', {
            'event_category': 'engagement',
            'method': method,
            'session_id': this.sessionId,
            'anonymize_ip': true
        });
    }

    // Track blog article view
    trackBlogView(articleTitle, articleSlug) {
        if (!this.isGA4Loaded || !this.consentGiven) return;
        
        // Limit title length to avoid tracking full content
        const truncatedTitle = articleTitle ? articleTitle.substring(0, 50) : 'untitled';
        
        gtag('event', 'blog_view', {
            'event_category': 'content',
            'event_label': this.hashId(articleSlug),
            'custom_parameter_1': truncatedTitle,
            'session_id': this.sessionId,
            'anonymize_ip': true
        });
    }

    // Track search (anonymized search terms)
    trackSearch(searchTerm, resultsCount = 0) {
        if (!this.isGA4Loaded || !this.consentGiven) return;
        
        // Don't track exact search terms to protect privacy
        const searchCategory = this.categorizeSearch(searchTerm);
        
        gtag('event', 'search', {
            'event_category': 'engagement',
            'custom_parameter_1': searchCategory,
            'value': resultsCount,
            'session_id': this.sessionId,
            'anonymize_ip': true
        });
    }

    // Track error (sanitized - no sensitive info)
    trackError(errorType, errorMessage) {
        if (!this.isGA4Loaded || !this.consentGiven) return;
        
        // Sanitize error message to remove any potential PII or sensitive data
        const sanitizedMessage = this.sanitizeErrorMessage(errorMessage);
        
        gtag('event', 'exception', {
            'event_category': 'error',
            'event_label': errorType,
            'description': sanitizedMessage,
            'fatal': false,
            'session_id': this.sessionId,
            'anonymize_ip': true
        });
    }

    // Track page timing (performance metrics only)
    trackPageTiming(pageName, loadTime) {
        if (!this.isGA4Loaded || !this.consentGiven) return;
        
        // Round load time to nearest 100ms to avoid fingerprinting
        const roundedLoadTime = Math.round(loadTime / 100) * 100;
        
        gtag('event', 'timing_complete', {
            'name': pageName,
            'value': roundedLoadTime,
            'session_id': this.sessionId,
            'anonymize_ip': true
        });
    }

    // Helper: Categorize file size to avoid tracking exact sizes
    categorizeFileSize(sizeInBytes) {
        if (!sizeInBytes || sizeInBytes <= 0) return 'empty';
        const sizeInKB = sizeInBytes / 1024;
        
        if (sizeInKB < 10) return 'tiny'; // < 10KB
        if (sizeInKB < 100) return 'small'; // 10KB - 100KB
        if (sizeInKB < 1024) return 'medium'; // 100KB - 1MB
        if (sizeInKB < 10240) return 'large'; // 1MB - 10MB
        return 'very_large'; // > 10MB
    }

    // Helper: Sanitize file types to common categories
    sanitizeFileType(fileType) {
        if (!fileType) return 'unknown';
        
        const type = fileType.toLowerCase();
        
        // Group into general categories
        if (type.includes('image/') || ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'].some(ext => type.includes(ext))) {
            return 'image';
        }
        if (type.includes('video/') || ['mp4', 'avi', 'mov', 'webm'].some(ext => type.includes(ext))) {
            return 'video';
        }
        if (type.includes('audio/') || ['mp3', 'wav', 'ogg', 'm4a'].some(ext => type.includes(ext))) {
            return 'audio';
        }
        if (type.includes('text/') || ['txt', 'csv', 'log'].some(ext => type.includes(ext))) {
            return 'text';
        }
        if (type.includes('application/pdf') || type.includes('.pdf')) {
            return 'pdf';
        }
        if (['doc', 'docx', 'odt'].some(ext => type.includes(ext))) {
            return 'document';
        }
        if (['xls', 'xlsx', 'ods'].some(ext => type.includes(ext))) {
            return 'spreadsheet';
        }
        if (['zip', 'rar', '7z', 'tar', 'gz'].some(ext => type.includes(ext))) {
            return 'archive';
        }
        if (['js', 'py', 'java', 'cpp', 'html', 'css', 'json', 'xml', 'php', 'rb'].some(ext => type.includes(ext))) {
            return 'code';
        }
        
        return 'other';
    }

    // Helper: Categorize searches without storing actual terms
    categorizeSearch(searchTerm) {
        if (!searchTerm) return 'empty';
        
        const length = searchTerm.length;
        const wordCount = searchTerm.trim().split(/\s+/).length;
        
        // Categorize by query characteristics, not content
        if (length < 3) return 'very_short';
        if (length < 10) return 'short';
        if (wordCount === 1) return 'single_word';
        if (wordCount <= 3) return 'few_words';
        if (wordCount > 3) return 'phrase';
        
        return 'query';
    }

    // Helper: Sanitize error messages to remove sensitive data
    sanitizeErrorMessage(errorMessage) {
        if (!errorMessage) return 'unknown_error';
        
        // Remove URLs, IPs, emails, file paths, and other potential PII
        let sanitized = String(errorMessage)
            // Remove URLs
            .replace(/https?:\/\/[^\s]+/gi, '[URL]')
            // Remove IP addresses
            .replace(/\b(?:\d{1,3}\.){3}\d{1,3}\b/g, '[IP]')
            // Remove email addresses
            .replace(/[\w.-]+@[\w.-]+\.\w+/g, '[EMAIL]')
            // Remove file paths
            .replace(/[a-zA-Z]:[\\/][\w\\/.-]+/g, '[PATH]')
            .replace(/\/[\w\\/.-]+/g, '[PATH]')
            // Remove potential IDs or tokens (long alphanumeric strings)
            .replace(/\b[a-zA-Z0-9]{20,}\b/g, '[TOKEN]')
            // Remove numbers that might be IDs
            .replace(/\b\d{6,}\b/g, '[ID]')
            // Limit length
            .substring(0, 100);
        
        return sanitized || 'error_sanitized';
    }

    // Helper: Show consent banner
    showConsentBanner() {
        // This can be called from your UI to show a consent dialog
        // Returns a promise that resolves with the user's choice
        return new Promise((resolve) => {
            // Your UI implementation would go here
            // For now, just check localStorage
            const currentConsent = this.checkConsent();
            resolve(currentConsent);
        });
    }

    // Helper: Clear all analytics data
    clearAnalyticsData() {
        localStorage.removeItem('analytics_consent');
        this.consentGiven = false;
        this.sessionId = this.generateSessionId();
    }
}

// Initialize analytics
window.Analytics = new Analytics();

// Privacy-focused event tracking
// Only tracks non-PII data with user consent

// Track page load time (performance only, no PII)
window.addEventListener('load', function() {
    if (window.Analytics && window.Analytics.consentGiven) {
        const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
        window.Analytics.trackPageTiming('page_load', loadTime);
    }
});

// Track errors (sanitized, no sensitive info)
window.addEventListener('error', function(e) {
    if (window.Analytics && window.Analytics.consentGiven) {
        // Only track error type, not full stack traces or user data
        window.Analytics.trackError('javascript_error', e.message);
    }
});

// Track unhandled promise rejections (sanitized)
window.addEventListener('unhandledrejection', function(e) {
    if (window.Analytics && window.Analytics.consentGiven) {
        const reason = e.reason instanceof Error ? e.reason.message : String(e.reason);
        window.Analytics.trackError('promise_rejection', reason);
    }
});

// Check for consent preference on page load
document.addEventListener('DOMContentLoaded', function() {
    // If no consent preference is stored, default to denied (privacy-first)
    if (window.Analytics && localStorage.getItem('analytics_consent') === null) {
        window.Analytics.updateConsent(false);
    }
});

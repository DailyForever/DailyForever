/**
 * Key Rotation Manager for Client-Side Encryption
 * Handles automatic and manual key rotation with re-encryption support
 */

export class KeyRotationManager {
    static ROTATION_CHECK_INTERVAL = 60 * 60 * 1000; // Check every hour
    static MAX_KEY_AGE = 90 * 24 * 60 * 60 * 1000; // 90 days
    static MAX_KEY_USES = 10000;
    
    constructor() {
        this.currentKeyId = null;
        this.keyUsageCount = new Map();
        this.rotationInProgress = false;
        this.lastRotationCheck = null;
        this.rotationListeners = [];
    }
    
    /**
     * Initialize key rotation monitoring
     */
    async initialize() {
        // Load current key info from localStorage
        this.loadKeyMetadata();
        
        // Start periodic rotation checks
        this.startRotationMonitoring();
        
        // Check rotation status immediately
        await this.checkRotationStatus();
    }
    
    /**
     * Load key metadata from local storage
     */
    loadKeyMetadata() {
        try {
            const metadata = localStorage.getItem('key_rotation_metadata');
            if (metadata) {
                const parsed = JSON.parse(metadata);
                this.currentKeyId = parsed.currentKeyId;
                this.keyUsageCount = new Map(Object.entries(parsed.usageCounts || {}));
                this.lastRotationCheck = parsed.lastCheck;
            }
        } catch (e) {
            console.error('Failed to load key metadata:', e);
        }
    }
    
    /**
     * Save key metadata to local storage
     */
    saveKeyMetadata() {
        try {
            const metadata = {
                currentKeyId: this.currentKeyId,
                usageCounts: Object.fromEntries(this.keyUsageCount),
                lastCheck: this.lastRotationCheck,
                timestamp: Date.now()
            };
            localStorage.setItem('key_rotation_metadata', JSON.stringify(metadata));
        } catch (e) {
            console.error('Failed to save key metadata:', e);
        }
    }
    
    /**
     * Start periodic rotation monitoring
     */
    startRotationMonitoring() {
        setInterval(async () => {
            if (!this.rotationInProgress) {
                await this.checkRotationStatus();
            }
        }, KeyRotationManager.ROTATION_CHECK_INTERVAL);
    }
    
    /**
     * Check if key rotation is needed
     */
    async checkRotationStatus() {
        try {
            const response = await fetch('/api/keys/rotation-status', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`Status check failed: ${response.status}`);
            }
            
            const data = await response.json();
            this.lastRotationCheck = Date.now();
            this.saveKeyMetadata();
            
            // Handle rotation recommendations
            if (data.status && data.status.needs_rotation) {
                this.handleRotationNeeded(data.status);
            }
            
            return data;
        } catch (error) {
            console.error('Failed to check rotation status:', error);
            return null;
        }
    }
    
    /**
     * Handle rotation needed scenario
     */
    handleRotationNeeded(status) {
        const urgency = status.urgency;
        const reasons = status.reasons || [];
        
        // Notify listeners
        this.notifyListeners({
            type: 'rotation_needed',
            urgency,
            reasons,
            keyId: this.currentKeyId
        });
        
        // Auto-rotate for critical urgency
        if (urgency === 'critical') {
            console.warn('Critical key rotation needed:', reasons);
            this.performRotation('automatic', reasons.join('; '));
        } else if (urgency === 'urgent') {
            console.warn('Urgent key rotation recommended:', reasons);
            // Show user notification
            this.showRotationNotification(urgency, reasons);
        }
    }
    
    /**
     * Perform key rotation
     */
    async performRotation(trigger = 'manual', reason = '') {
        if (this.rotationInProgress) {
            console.warn('Rotation already in progress');
            return null;
        }
        
        this.rotationInProgress = true;
        
        try {
            // Request rotation from server
            const response = await fetch('/api/keys/rotate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: JSON.stringify({
                    trigger,
                    reason,
                    current_key_id: this.currentKeyId
                }),
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`Rotation failed: ${response.status}`);
            }
            
            const result = await response.json();
            
            // Update local metadata
            const oldKeyId = this.currentKeyId;
            this.currentKeyId = result.new_key_id;
            this.keyUsageCount.delete(oldKeyId);
            this.keyUsageCount.set(result.new_key_id, 0);
            this.saveKeyMetadata();
            
            // Notify listeners
            this.notifyListeners({
                type: 'rotation_completed',
                oldKeyId,
                newKeyId: result.new_key_id,
                gracePeriod: result.grace_period_ends
            });
            
            // Schedule re-encryption of local data
            await this.scheduleReEncryption(oldKeyId, result.new_key_id);
            
            return result;
            
        } catch (error) {
            console.error('Key rotation failed:', error);
            this.notifyListeners({
                type: 'rotation_failed',
                error: error.message
            });
            throw error;
            
        } finally {
            this.rotationInProgress = false;
        }
    }
    
    /**
     * Schedule re-encryption of local encrypted data
     */
    async scheduleReEncryption(oldKeyId, newKeyId) {
        // Get list of items needing re-encryption
        const items = this.getEncryptedItems();
        
        if (items.length === 0) {
            return;
        }
        
        console.log(`Scheduling re-encryption of ${items.length} items`);
        
        // Process in batches to avoid blocking
        const batchSize = 10;
        for (let i = 0; i < items.length; i += batchSize) {
            const batch = items.slice(i, i + batchSize);
            await this.reEncryptBatch(batch, oldKeyId, newKeyId);
            
            // Progress notification
            this.notifyListeners({
                type: 're_encryption_progress',
                completed: Math.min(i + batchSize, items.length),
                total: items.length
            });
        }
        
        console.log('Re-encryption completed');
    }
    
    /**
     * Re-encrypt a batch of items
     */
    async reEncryptBatch(items, oldKeyId, newKeyId) {
        // This would decrypt with old key and re-encrypt with new key
        // Implementation depends on your storage strategy
        for (const item of items) {
            try {
                // Placeholder for actual re-encryption logic
                console.log(`Re-encrypting item ${item.id}`);
            } catch (e) {
                console.error(`Failed to re-encrypt item ${item.id}:`, e);
            }
        }
    }
    
    /**
     * Get list of locally encrypted items
     */
    getEncryptedItems() {
        // This should return items from IndexedDB or localStorage
        // that need re-encryption
        const items = [];
        
        // Check localStorage for encrypted data
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith('encrypted_')) {
                try {
                    const data = JSON.parse(localStorage.getItem(key));
                    if (data.keyId === this.currentKeyId) {
                        items.push({ id: key, data });
                    }
                } catch (e) {
                    // Skip invalid items
                }
            }
        }
        
        return items;
    }
    
    /**
     * Increment key usage counter
     */
    incrementKeyUsage(keyId = null) {
        const id = keyId || this.currentKeyId;
        if (!id) return;
        
        const current = this.keyUsageCount.get(id) || 0;
        this.keyUsageCount.set(id, current + 1);
        
        // Check if usage limit exceeded
        if (current + 1 >= KeyRotationManager.MAX_KEY_USES) {
            console.warn('Key usage limit reached, rotation needed');
            this.handleRotationNeeded({
                urgency: 'urgent',
                reasons: [`Key usage exceeded ${KeyRotationManager.MAX_KEY_USES} operations`]
            });
        }
        
        // Save periodically
        if ((current + 1) % 100 === 0) {
            this.saveKeyMetadata();
        }
    }
    
    /**
     * Register rotation event listener
     */
    onRotationEvent(callback) {
        this.rotationListeners.push(callback);
        return () => {
            const index = this.rotationListeners.indexOf(callback);
            if (index > -1) {
                this.rotationListeners.splice(index, 1);
            }
        };
    }
    
    /**
     * Notify all listeners
     */
    notifyListeners(event) {
        for (const listener of this.rotationListeners) {
            try {
                listener(event);
            } catch (e) {
                console.error('Rotation listener error:', e);
            }
        }
    }
    
    /**
     * Show rotation notification to user
     */
    showRotationNotification(urgency, reasons) {
        // Check if notifications are supported
        if (!('Notification' in window)) {
            console.log('Notifications not supported');
            return;
        }
        
        // Check permission
        if (Notification.permission === 'granted') {
            new Notification('Key Rotation Recommended', {
                body: `${urgency.toUpperCase()}: ${reasons.join(', ')}`,
                icon: '/icon-192x192.png',
                tag: 'key-rotation'
            });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    this.showRotationNotification(urgency, reasons);
                }
            });
        }
    }
    
    /**
     * Force emergency rotation
     */
    async emergencyRotation(reason) {
        if (!confirm('Perform emergency key rotation? This will invalidate all current encryption keys.')) {
            return null;
        }
        
        try {
            const response = await fetch('/api/keys/emergency-rotate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: JSON.stringify({
                    reason,
                    confirm: true
                }),
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`Emergency rotation failed: ${response.status}`);
            }
            
            const result = await response.json();
            
            // Clear all local encrypted data as it's now invalid
            this.clearAllEncryptedData();
            
            // Update metadata
            this.currentKeyId = result.new_key_id;
            this.keyUsageCount.clear();
            this.keyUsageCount.set(result.new_key_id, 0);
            this.saveKeyMetadata();
            
            // Notify
            this.notifyListeners({
                type: 'emergency_rotation_completed',
                newKeyId: result.new_key_id
            });
            
            return result;
            
        } catch (error) {
            console.error('Emergency rotation failed:', error);
            throw error;
        }
    }
    
    /**
     * Clear all encrypted data (emergency use)
     */
    clearAllEncryptedData() {
        const keysToRemove = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith('encrypted_')) {
                keysToRemove.push(key);
            }
        }
        
        for (const key of keysToRemove) {
            localStorage.removeItem(key);
        }
        
        console.warn(`Cleared ${keysToRemove.length} encrypted items`);
    }
    
    /**
     * Get CSRF token
     */
    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
    
    /**
     * Get rotation statistics
     */
    getStats() {
        return {
            currentKeyId: this.currentKeyId,
            keyUsage: Object.fromEntries(this.keyUsageCount),
            lastCheck: this.lastRotationCheck,
            rotationInProgress: this.rotationInProgress,
            encryptedItemCount: this.getEncryptedItems().length
        };
    }
}

// Create singleton instance
const keyRotationManager = new KeyRotationManager();

// Auto-initialize when DOM is ready
if (typeof window !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            keyRotationManager.initialize();
        });
    } else {
        keyRotationManager.initialize();
    }
    
    // Export to window for global access
    window.KeyRotationManager = KeyRotationManager;
    window.keyRotationManager = keyRotationManager;
}

export default keyRotationManager;

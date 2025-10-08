// UI Mode Toggle - Handles switching between Quick Mode and Advanced Settings
// Used on both paste/create and files/create pages

(function initUIMode() {
  'use strict';
  
  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize);
  } else {
    initialize();
  }
  
  function initialize() {
    const toggleBtn = document.getElementById('toggleMode');
    const quickMode = document.getElementById('quickMode');
    const advancedMode = document.getElementById('advancedMode');
    const modeToggleText = document.getElementById('modeToggleText');
    
    if (!toggleBtn || !quickMode || !advancedMode) return;
    
    let isAdvanced = false;
    
    // Load saved preference from localStorage
    const savedMode = localStorage.getItem('df_ui_mode');
    if (savedMode === 'advanced') {
      isAdvanced = true;
      switchToAdvanced();
    }
    
    toggleBtn.addEventListener('click', function() {
      isAdvanced = !isAdvanced;
      
      if (isAdvanced) {
        switchToAdvanced();
      } else {
        switchToQuick();
      }
      
      // Save preference
      localStorage.setItem('df_ui_mode', isAdvanced ? 'advanced' : 'quick');
    });
    
    function switchToAdvanced() {
      // Hide quick mode, show advanced
      quickMode.classList.add('hidden');
      advancedMode.classList.remove('hidden');
      
      // Update button text
      if (modeToggleText) {
        modeToggleText.textContent = 'Simple Mode';
        modeToggleText.dataset.i18n = 'common.simple_mode';
      }
      
      // Sync content between textareas (for paste page)
      syncPasteContent('quick', 'advanced');
      
      // Sync file input (for file page)
      syncFileInput('quick', 'advanced');
      
      // Sync common fields
      syncCommonFields('quick', 'advanced');
      
      // Add active state to toggle button
      toggleBtn.classList.add('text-emerald-500');
    }
    
    function switchToQuick() {
      // Hide advanced mode, show quick
      advancedMode.classList.add('hidden');
      quickMode.classList.remove('hidden');
      
      // Update button text
      if (modeToggleText) {
        modeToggleText.textContent = 'Advanced Settings';
        modeToggleText.dataset.i18n = 'common.advanced_settings';
      }
      
      // Sync content between textareas (for paste page)
      syncPasteContent('advanced', 'quick');
      
      // Sync file input (for file page)
      syncFileInput('advanced', 'quick');
      
      // Sync common fields
      syncCommonFields('advanced', 'quick');
      
      // Remove active state from toggle button
      toggleBtn.classList.remove('text-emerald-500');
    }
    
    function syncPasteContent(from, to) {
      const fromContent = document.getElementById(from === 'quick' ? 'content' : 'contentAdvanced');
      const toContent = document.getElementById(to === 'quick' ? 'content' : 'contentAdvanced');
      
      if (fromContent && toContent && fromContent.value) {
        toContent.value = fromContent.value;
        
        // Update character count
        const charCountId = to === 'quick' ? 'charCount' : 'charCountAdvanced';
        const charCountEl = document.getElementById(charCountId);
        if (charCountEl) {
          charCountEl.textContent = fromContent.value.length + (to === 'quick' ? '' : ' chars');
        }
        
        // Trigger input event for any listeners
        toContent.dispatchEvent(new Event('input', { bubbles: true }));
      }
    }
    
    function syncFileInput(from, to) {
      const fromInput = document.getElementById(from === 'quick' ? 'fileInput' : 'fileInputAdvanced');
      const toInput = document.getElementById(to === 'quick' ? 'fileInput' : 'fileInputAdvanced');
      
      if (fromInput && toInput && fromInput.files && fromInput.files.length > 0) {
        // Note: We cannot directly copy files due to security restrictions
        // We'll just show the selected file info in the other mode
        const fileName = fromInput.files[0].name;
        const fileSize = formatFileSize(fromInput.files[0].size);
        
        const selectedFileId = to === 'quick' ? 'selectedFile' : 'selectedFileAdvanced';
        const fileNameId = to === 'quick' ? 'selectedFileName' : 'selectedFileNameAdvanced';
        const fileMetaId = to === 'quick' ? 'selectedFileMeta' : 'selectedFileMetaAdvanced';
        
        const selectedFileEl = document.getElementById(selectedFileId);
        const fileNameEl = document.getElementById(fileNameId);
        const fileMetaEl = document.getElementById(fileMetaId);
        
        if (selectedFileEl && fileNameEl && fileMetaEl) {
          selectedFileEl.classList.remove('hidden');
          fileNameEl.textContent = fileName;
          fileMetaEl.textContent = fileSize;
        }
      }
    }
    
    function syncCommonFields(from, to) {
      // Sync expiration
      const fromExpires = document.getElementById(from === 'quick' ? 'expires_in' : 'expires_in_advanced');
      const toExpires = document.getElementById(to === 'quick' ? 'expires_in' : 'expires_in_advanced');
      if (fromExpires && toExpires) {
        toExpires.value = fromExpires.value;
      }
      
      // Sync password
      const fromPassword = document.getElementById(from === 'quick' ? 'password' : 'password_advanced');
      const toPassword = document.getElementById(to === 'quick' ? 'password' : 'password_advanced');
      if (fromPassword && toPassword) {
        toPassword.value = fromPassword.value;
      }
    }
    
    function formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      
      return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }
    
    // Add real-time sync for input fields
    setupRealtimeSync();
    
    function setupRealtimeSync() {
      // Sync content textareas
      const contentQuick = document.getElementById('content');
      const contentAdvanced = document.getElementById('contentAdvanced');
      
      if (contentQuick && contentAdvanced) {
        contentQuick.addEventListener('input', () => {
          if (!quickMode.classList.contains('hidden')) {
            contentAdvanced.value = contentQuick.value;
          }
        });
        
        contentAdvanced.addEventListener('input', () => {
          if (!advancedMode.classList.contains('hidden')) {
            contentQuick.value = contentAdvanced.value;
          }
        });
      }
      
      // Sync expiration selects
      const expiresQuick = document.getElementById('expires_in');
      const expiresAdvanced = document.getElementById('expires_in_advanced');
      
      if (expiresQuick && expiresAdvanced) {
        expiresQuick.addEventListener('change', () => {
          expiresAdvanced.value = expiresQuick.value;
        });
        
        expiresAdvanced.addEventListener('change', () => {
          expiresQuick.value = expiresAdvanced.value;
        });
      }
      
      // Sync password fields
      const passwordQuick = document.getElementById('password');
      const passwordAdvanced = document.getElementById('password_advanced');
      
      if (passwordQuick && passwordAdvanced) {
        passwordQuick.addEventListener('input', () => {
          passwordAdvanced.value = passwordQuick.value;
        });
        
        passwordAdvanced.addEventListener('input', () => {
          passwordQuick.value = passwordAdvanced.value;
        });
      }
    }
  }
})();

// Export for use in other modules if needed
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { initUIMode };
}

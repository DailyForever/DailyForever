<div id="backupCodeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden" style="display: none;">
    <div class="bg-yt-bg border border-yt-border rounded-lg p-8 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-yt-text mb-2" data-i18n="backup.title">Save Your Backup Code</h2>
            <p class="text-yt-text-secondary mb-6" data-i18n="backup.subtitle">
                This is your backup code. Save it in a safe place - you can use it to log in if you forget your PIN.
            </p>
            
            <div class="bg-yt-surface border border-yt-border rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-yt-text" data-i18n="backup.label">Backup Code:</span>
                    <button onclick="copyBackupCode()" class="text-xs text-yt-accent hover:underline" data-i18n="common.buttons.copy">Copy</button>
                </div>
                <div id="backupCodeDisplay" class="font-mono text-lg text-yt-text break-all select-all">
                    {{ $backupCode ?? 'Loading...' }}
                </div>
            </div>
            
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div class="text-sm text-yellow-800 dark:text-yellow-200">
                        <strong data-i18n="backup.important">Important:</strong> <span data-i18n="backup.note">This code will change every time you log in. 
                        Make sure to save it in a secure location like a password manager.</span>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button onclick="downloadBackupCode()" class="flex-1 btn-secondary px-4 py-2 text-sm" data-i18n="backup.download_txt">
                    Download as Text
                </button>
                <button onclick="closeBackupCodeModal()" class="flex-1 btn-primary px-4 py-2 text-sm" data-i18n="backup.saved">
                    I've Saved It
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showBackupCodeModal(backupCode) {
    console.log('showBackupCodeModal called with:', backupCode);
    const modal = document.getElementById('backupCodeModal');
    const display = document.getElementById('backupCodeDisplay');
    
    if (display) {
        display.textContent = backupCode;
    }
    
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        console.log('Modal should now be visible');
    } else {
        console.error('Modal element not found');
    }
}

function closeBackupCodeModal() {
    const modal = document.getElementById('backupCodeModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
}

function copyBackupCode() {
    const backupCode = document.getElementById('backupCodeDisplay').textContent;
    navigator.clipboard.writeText(backupCode).then(() => {
        // Show temporary success message
        const button = event.target;
        const originalText = button.textContent;
        const copied = (window.I18N && typeof window.I18N.t === 'function') ? window.I18N.t('common.buttons.copied') : 'Copied!';
        button.textContent = copied;
        button.classList.add('text-green-500');
        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('text-green-500');
        }, 2000);
    });
}

function downloadBackupCode() {
    const backupCode = document.getElementById('backupCodeDisplay').textContent;
    const lines = {
        header: (window.I18N ? window.I18N.t('backup.file.header') : 'DailyForever Backup Code'),
        label: (window.I18N ? window.I18N.t('backup.label_plain') : 'Backup Code:'),
        generated: (window.I18N ? window.I18N.t('backup.file.generated') : 'Generated:'),
        important: (window.I18N ? window.I18N.t('backup.file.important') : 'Important:'),
        note: (window.I18N ? window.I18N.t('backup.file.note') : 'This code will change every time you log in. Save it in a secure location like a password manager.'),
        usage: (window.I18N ? window.I18N.t('backup.file.usage') : 'You can use this code to log in if you forget your PIN.')
    };
    const content = `${lines.header}\n\n${lines.label} ${backupCode}\n\n${lines.generated} ${new Date().toLocaleString()}\n\n${lines.important} ${lines.note}\n\n${lines.usage}`;
    
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'dailyforever-backup-code.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Show modal if backup code is provided
@if(isset($backupCode) && $backupCode)
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Backup code detected:', '{{ $backupCode }}');
        showBackupCodeModal('{{ $backupCode }}');
    });
@endif
</script>

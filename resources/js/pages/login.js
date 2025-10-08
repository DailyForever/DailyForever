// Login page logic (moved from inline script for strict CSP)
(function(){
  function onReady(fn){ if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn); else fn(); }
  function ti(key, fallback){ try { return (window.I18N && window.I18N.t(key)) || fallback; } catch(_) { return fallback; } }

  onReady(() => {
    const loginInput = document.getElementById('loginInput');
    const passwordInput = document.getElementById('password');
    const backupCodeInput = document.getElementById('backup_code');
    const inputHint = document.getElementById('inputHint');
    const passwordIndicator = document.getElementById('passwordIndicator');
    const backupCodeIndicator = document.getElementById('backupCodeIndicator');
    const passwordIcon = document.getElementById('passwordIcon');
    const backupCodeIcon = document.getElementById('backupCodeIcon');

    function detectInputType(value) {
      if (value.length === 16 && /^[A-Z0-9]+$/.test(value)) return 'backup_code';
      return 'password';
    }

    function updateInputType(type) {
      if (!loginInput) return;
      if (type === 'backup_code') {
        passwordIndicator && passwordIndicator.classList.add('hidden');
        backupCodeIndicator && backupCodeIndicator.classList.remove('hidden');
        if (inputHint) inputHint.textContent = ti('login.hint_backup', 'Backup code detected - this will change after login');
        loginInput.classList.add('border-yt-accent');
        loginInput.classList.remove('border-yt-border');
        passwordIcon && passwordIcon.classList.add('hidden');
        backupCodeIcon && backupCodeIcon.classList.remove('hidden');
        loginInput.type = 'text';
      } else {
        passwordIndicator && passwordIndicator.classList.remove('hidden');
        backupCodeIndicator && backupCodeIndicator.classList.add('hidden');
        if (inputHint) inputHint.textContent = ti('login.hint_password', 'Password detected - enter your account password');
        loginInput.classList.remove('border-yt-accent');
        loginInput.classList.add('border-yt-border');
        passwordIcon && passwordIcon.classList.remove('hidden');
        backupCodeIcon && backupCodeIcon.classList.add('hidden');
        loginInput.type = 'password';
      }
    }

    if (loginInput) {
      loginInput.addEventListener('input', (e) => {
        let value = e.target.value;
        const inputType = detectInputType(value);
        if (inputType === 'backup_code') {
          value = value.toUpperCase().replace(/[^A-Z0-9]/g, '');
          e.target.value = value;
        }
        updateInputType(inputType);
        if (passwordInput) passwordInput.value = '';
        if (backupCodeInput) backupCodeInput.value = '';
      });

      loginInput.addEventListener('paste', function(e){
        setTimeout(() => {
          const value = e.target.value;
          const inputType = detectInputType(value);
          updateInputType(inputType);
        }, 10);
      });
    }

    updateInputType('password');

    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    if (togglePasswordBtn && loginInput) {
      togglePasswordBtn.addEventListener('click', function(){
        const type = loginInput.getAttribute('type') === 'password' ? 'text' : 'password';
        loginInput.setAttribute('type', type);
        const icon = this.querySelector('svg');
        if (!icon) return;
        if (type === 'text') {
          icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>';
        } else {
          icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
        }
      });
    }

    const srpLoginEnabled = document.getElementById('srpLoginEnabled');
    const loginForm = document.querySelector('form');
    const loginInputField = document.getElementById('loginInput')?.parentElement?.parentElement;

    async function ensureSrpReady(timeoutMs = 3000) {
      const sleep = (ms) => new Promise(r => setTimeout(r, ms));
      const start = Date.now();
      while (Date.now() - start < timeoutMs) {
        if (typeof window.SRPAuthentication !== 'undefined') {
          try {
            if (!window.SRPAuthentication.isSupported() && typeof window.SRPAuthentication.initialize === 'function') {
              await window.SRPAuthentication.initialize();
            }
          } catch(_){}
          return true;
        }
        await sleep(50);
      }
      return false;
    }

    if (srpLoginEnabled && loginInputField) {
      srpLoginEnabled.addEventListener('change', function(){
        if (this.checked) {
          loginInputField.style.display = 'block';
          const li = document.getElementById('loginInput');
          if (li) { li.placeholder = ti('login.placeholder_srp', 'Enter your password for SRP authentication'); li.type = 'password'; }
        } else {
          loginInputField.style.display = 'block';
          const li = document.getElementById('loginInput');
          if (li) { li.placeholder = ti('login.placeholder_default', 'Enter your password or 16-character backup code'); updateInputType('password'); }
        }
      });
    }

    if (srpLoginEnabled && loginForm) {
      loginForm.addEventListener('submit', async function(e){
        if (!srpLoginEnabled.checked) return; // normal path
        e.preventDefault();
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.innerHTML : '';
        try {
          const ready = await ensureSrpReady(3000);
          if (!ready || typeof window.SRPAuthentication === 'undefined') {
            throw new Error('SRP authentication is not supported in this browser');
          }
          if (!window.SRPAuthentication.isSupported()) throw new Error('SRP authentication is not available');

          const username = document.getElementById('username')?.value;
          const password = document.getElementById('loginInput')?.value;
          if (!username || !password) throw new Error('Username and password are required');

          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="w-5 h-5 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>' + ti('login.srp_initiating', 'Initiating SRP authentication...');
          }

          const initiateResult = await window.SRPAuthentication.initiateLogin(username);
          if (!initiateResult.success) throw new Error(initiateResult.error || 'Failed to initiate SRP authentication');

          if (submitBtn) submitBtn.innerHTML = '<svg class="w-5 h-5 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>' + ti('login.srp_generating', 'Generating proof...');

          const loginResult = await window.SRPAuthentication.completeLogin(username, password, initiateResult.data);
          if (!loginResult.success) throw new Error(loginResult.error || 'SRP authentication failed');

          const redirectUrl = (loginResult.data && loginResult.data.redirect) ? loginResult.data.redirect : '/';
          window.location.href = redirectUrl;
        } catch (error) {
          console.error('SRP login error:', error);
          try {
            const msg = (error && error.message) ? String(error.message) : '';
            if (msg.includes('SRP authentication not enabled')) {
              if (srpLoginEnabled) srpLoginEnabled.checked = false;
              if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalText; }
              const visiblePwd = document.getElementById('loginInput')?.value || '';
              if (passwordInput) passwordInput.value = visiblePwd;
              if (backupCodeInput) backupCodeInput.value = '';
              if (loginForm.requestSubmit) loginForm.requestSubmit(); else loginForm.submit();
              return;
            }
          } catch(_){}

          const errorDiv = document.createElement('div');
          errorDiv.className = 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-4';
          const unknownMsg = ti('login.unknown_error','Unknown error');
          const failedTitle = ti('login.srp_failed_title','SRP Login Failed');
          const tryAgainMsg = ti('login.srp_failed_try_again','Please try again or use regular login.');
          errorDiv.innerHTML = `
<div class="flex items-start space-x-3">
  <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
  </svg>
  <div>
    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">${failedTitle}</h3>
    <p class="text-sm text-red-700 dark:text-red-300 mt-1">${(error && error.message) ? error.message : unknownMsg}</p>
    <p class="text-xs text-red-600 dark:text-red-400 mt-2">${tryAgainMsg}</p>
  </div>
</div>`;
          if (loginForm) loginForm.insertBefore(errorDiv, loginForm.firstChild);
          if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalText; }
        }
      });
    }
  });
})();

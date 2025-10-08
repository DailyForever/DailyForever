import 'highlight.js/styles/github-dark.css';
import './bootstrap';
import './crypto';
import './webcrypto-wrapper';
import './i18n';
import './ui-mode-toggle';

// Helper to run after DOM ready
function onDomReady(fn) {
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn);
  else fn();
}

onDomReady(async () => {
  const isPasteCreate = !!document.getElementById('pasteCreateRoot');
  const isPasteShow = !!document.getElementById('pasteShowRoot');
  const isPrekeysPage = !!document.getElementById('prekeysRoot');
  const isFilesCreate = !!document.getElementById('filesCreateRoot');
  const isFileShow = !!document.getElementById('fileIdentifier');
  const isLoginPage = !!document.querySelector('#srpLoginForm');
  const isHowItWorksPage = !!document.querySelector('#api-demos');

  // Lazily load highlight.js only if code blocks or paste pages are present
  if (isPasteCreate || isPasteShow || document.querySelector('pre code')) {
    try {
      const hl = await import('highlight.js');
      window.hljs = hl.default || hl; // expose globally for legacy usage
      window.hljs.highlightAll?.();
    } catch (_) {}
  }

  // Conditionally load page scripts
  // Ensure Kyber (PostQuantumKEM) is present on files pages for KEM wrapping/unwrapping
  if (isFilesCreate || isFileShow) {
    try { await import('./kyber.js'); } catch (_) {}
  }
  if (isPasteCreate) {
    // QRCode only needed on create page (for QR modal)
    try {
      const qr = await import('qrcode');
      window.QRCode = (qr && (qr.default || qr));
    } catch (_) {}
    await import('./pages/paste-create.js');
  }
  if (isPasteShow) {
    await import('./pages/paste-show.js');
  }
  if (isPrekeysPage) {
    await import('./pages/prekeys.js');
  }
  if (isLoginPage) {
    await import('./pages/login.js');
  }
  if (isHowItWorksPage) {
    await import('./pages/how-it-works-demo.js');
  }

  // Register action handlers after initial page code (non-critical path)
  // Load on idle to avoid blocking main chunk
  const loadLater = () => import('./paste-actions').catch(() => {});
  if ('requestIdleCallback' in window) requestIdleCallback(loadLater); else setTimeout(loadLater, 0);

  // Load SRP auth ONLY when SRP-related UI is present to avoid background calls on unrelated pages (e.g., /settings 2FA)
  const hasSrpUi = !!(
    document.querySelector('[data-srp]') ||
    document.querySelector('#srpLoginForm') ||
    document.querySelector('#srpRegisterForm') ||
    document.querySelector('form[action*="/srp/"]')
  );
  if (hasSrpUi) {
    const loadSrp = () => import('./srp-auth').catch(() => {});
    loadSrp();
  }

  // Navbar logo fallback: ensure a visible logo even if inline SVG fails to render
  (function ensureNavLogo() {
    try {
      const link = document.querySelector('header.main-navbar nav .brand-mark');
      if (!link) return;
      const svg = link.querySelector('svg');
      const existingImg = link.querySelector('img[data-injected-logo]');

      // Decide if we need a fallback: no SVG or SVG renders with negligible size
      let needsFallback = !svg;
      if (svg && !needsFallback) {
        try {
          const rect = svg.getBoundingClientRect();
          if (!rect || rect.width < 12 || rect.height < 12) needsFallback = true;
        } catch (_) { /* ignore */ }
      }

      if (needsFallback && !existingImg) {
        const img = new Image();
        img.src = (window.APP_LOGO_URL || '/images/logo-navbar.svg');
        img.alt = 'DailyForever';
        img.className = 'h-8 w-auto';
        img.setAttribute('data-injected-logo', '1');
        img.onload = () => {
          try { if (svg) svg.style.display = 'none'; } catch (_) {}
          link.prepend(img);
        };
        img.onerror = () => {
          // Text fallback if image also fails
          const span = document.createElement('span');
          span.textContent = 'DailyForever';
          span.style.fontWeight = '600';
          span.style.fontSize = '18px';
          span.style.lineHeight = '1';
          link.appendChild(span);
        };
      }
    } catch (_) { /* no-op */ }
  })();

  // Ensure brand mark uses current theme text color and visible fill for inline SVG text
  (function ensureBrandColor() {
    try {
      const apply = () => {
        const brand = document.querySelector('header.main-navbar nav .brand-mark');
        if (!brand) return;
        brand.style.color = 'var(--color-text)';
        const txt = brand.querySelector('svg text');
        if (txt) txt.setAttribute('fill', 'currentColor');
      };
      apply();
      // Observe theme attribute changes to re-apply
      const mo = new MutationObserver(apply);
      mo.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
      // Also re-apply on i18n language change which may re-render parts
      window.addEventListener('languageChanged', apply);
    } catch (_) { /* no-op */ }
  })();

});

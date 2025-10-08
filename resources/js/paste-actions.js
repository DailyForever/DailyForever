// Attach declarative event listeners for elements with data-action
// This removes inline onclick= handlers to enable strict CSP.

function bindAction(el) {
  const action = el.getAttribute('data-action');
  if (!action) return;
  const fn = window[action];
  if (typeof fn !== 'function') return;
  el.addEventListener('click', (e) => {
    // Prevent default for anchors
    if (el.tagName === 'A') {
      e.preventDefault();
    }
    try {
      if (action === 'copyContent') {
        fn(e);
      } else if (action === 'copyToClipboard') {
        fn(e);
      } else if (action === 'downloadAndDecrypt') {
        const id = el.getAttribute('data-file-id');
        const filename = el.getAttribute('data-filename');
        fn(id, filename);
      } else {
        fn();
      }
    } catch (err) {
      console.warn('Action failed:', action, err);
    }
  });
}

function initPasteActions() {
  const nodes = document.querySelectorAll('[data-action]');
  nodes.forEach(bindAction);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initPasteActions);
} else {
  initPasteActions();
}

// Keyboard: submit password on Enter in #pastePwd
document.addEventListener('keydown', (e) => {
  try {
    if (e.key === 'Enter' && e.target && e.target.id === 'pastePwd') {
      const submit = window.submitPassword;
      if (typeof submit === 'function') {
        e.preventDefault();
        submit();
      }
    }
  } catch (_) {}
});

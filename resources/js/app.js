import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/* ═══════════════════════════════════════════════════
   APP-READY — drop skeleton after window.load
═══════════════════════════════════════════════════ */
const minimumSkeletonMs = 350;

function revealApp() {
    const elapsed = performance.now();
    const remaining = Math.max(0, minimumSkeletonMs - elapsed);
    window.setTimeout(() => {
        document.documentElement.classList.add('app-ready');
    }, remaining);
}

if (document.readyState === 'complete') {
    revealApp();
} else {
    window.addEventListener('load', revealApp, { once: true });
}

/* ═══════════════════════════════════════════════════
   FORM SUBMIT — lock buttons + reset on bfcache restore
═══════════════════════════════════════════════════ */
function bindSubmitLocking() {
    document.querySelectorAll('form[data-loading-form]').forEach((form) => {
        if (form.dataset.loadingBound === 'true') return;
        form.dataset.loadingBound = 'true';

        form.addEventListener('submit', () => {
            form.querySelectorAll('button[type="submit"]').forEach((btn) => {
                btn.classList.add('is-submitting');
                btn.setAttribute('disabled', 'disabled');
                btn.setAttribute('data-reset-on-pageshow', 'true');
            });
        });
    });
}

function resetSubmitButtons() {
    document.querySelectorAll('button[data-reset-on-pageshow]').forEach((btn) => {
        btn.classList.remove('is-submitting');
        btn.removeAttribute('disabled');
    });
}

window.addEventListener('DOMContentLoaded', bindSubmitLocking);
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        resetSubmitButtons();
    }
    bindSubmitLocking();
});

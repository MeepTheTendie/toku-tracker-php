'use strict';

let pending = false;
document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-action')) return;
    event.preventDefault();
    if (pending || (form.dataset.confirm && !window.confirm(form.dataset.confirm))) return;
    pending = true;
    const notice = document.getElementById('notice');
    const buttons = [...document.querySelectorAll('[data-action] button')];
    const data = Object.fromEntries(new FormData(form));
    buttons.forEach(button => { button.disabled = true; });
    notice.hidden = true;
    try {
        const response = await fetch(form.getAttribute('action'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': data.csrf },
            body: JSON.stringify(data),
        });
        const result = await response.json();
        if (!response.ok || result.success !== true) throw new Error(result.error || 'Could not save progress. Please try again.');
        window.location.assign(result.redirect);
    } catch (error) {
        notice.textContent = error.message || 'Connection failed. Please try again.';
        notice.hidden = false;
        notice.scrollIntoView({ block: 'nearest' });
        buttons.forEach(button => { button.disabled = false; });
        pending = false;
    }
});

document.addEventListener('keydown', (event) => {
    if (pending || event.repeat || event.altKey || event.ctrlKey || event.metaKey ||
        event.target.closest('input, textarea, select, button, a, [contenteditable]')) return;
    const watch = document.querySelector('.watch-panel');
    if (!watch) return;
    const selector = event.key === 'ArrowLeft' ? '[data-prev]' : event.key === 'ArrowRight' ? '[data-next]' : null;
    if (selector) {
        const link = watch.querySelector(selector);
        if (link) { event.preventDefault(); link.click(); }
    } else if (event.key.toLowerCase() === 'w' || event.key === ' ') {
        event.preventDefault();
        watch.querySelector('form[data-action]').requestSubmit();
    }
});

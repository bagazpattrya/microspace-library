// microspace-v1.0.9a

NProgress.configure({
    showSpinner: false,
    speed: 300,
    minimum: 0.15,
    trickleSpeed: 150,
});

NProgress.done();

document.addEventListener('click', function (e) {
    const anchor = e.target.closest('a');
    if (!anchor) return;

    const href = anchor.getAttribute('href');
    if (!href) return;

    const isExternal = anchor.hostname && anchor.hostname !== location.hostname;
    const isAnchor = href.startsWith('#');
    const isVoid = href.startsWith('javascript');
    const isNewTab = anchor.target === '_blank';
    const hasModifier = e.ctrlKey || e.metaKey || e.shiftKey || e.altKey;

    if (isExternal || isAnchor || isVoid || isNewTab || hasModifier) return;

    NProgress.start();
});

window.addEventListener('pageshow', function () {
    NProgress.done();
});
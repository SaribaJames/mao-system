{{--
    Adds a show/hide eye button to every password field on the page.

    Done here once rather than editing each form, so any password or PIN field
    added later gets the toggle automatically. Purely visual — it only flips the
    input's type between "password" and "text"; nothing is stored or sent.
--}}
<script>
(function () {
    function attachToggle(input) {
        // Skip fields already handled, and any explicitly opted out.
        if (input.dataset.pwToggle === 'done' || input.hasAttribute('data-no-toggle')) return;
        input.dataset.pwToggle = 'done';

        // Wrap the input so the button can sit inside its right edge.
        var wrap = document.createElement('div');
        wrap.style.position = 'relative';
        wrap.style.display = 'block';
        input.parentNode.insertBefore(wrap, input);
        wrap.appendChild(input);

        // Leave room for the icon so long text doesn't run underneath it.
        input.style.paddingRight = '2.25rem';

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.tabIndex = -1;
        btn.setAttribute('aria-label', 'Show password');
        btn.style.cssText = 'position:absolute;top:50%;right:0.6rem;transform:translateY(-50%);' +
                            'background:none;border:0;padding:0;cursor:pointer;line-height:1;' +
                            'color:#9ca3af;display:flex;align-items:center;';
        var EYE = '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>';
        var EYE_OFF = '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-6.4 0-10-7-10-7a18.4 18.4 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c6.4 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19M1 1l22 22"/><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/></svg>';

        btn.innerHTML = EYE;

        btn.addEventListener('click', function () {
            var showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            btn.innerHTML = showing ? EYE : EYE_OFF;
            btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            input.focus();
        });

        wrap.appendChild(btn);
    }

    function scan(root) {
        (root || document).querySelectorAll('input[type="password"]').forEach(attachToggle);
    }

    document.addEventListener('DOMContentLoaded', function () {
        scan();

        // Password fields inside modals are often already in the DOM, but watch
        // for any that get added later (dynamically rendered forms).
        if (window.MutationObserver) {
            new MutationObserver(function (mutations) {
                mutations.forEach(function (m) {
                    m.addedNodes.forEach(function (node) {
                        if (node.nodeType !== 1) return;
                        if (node.matches && node.matches('input[type="password"]')) attachToggle(node);
                        else scan(node);
                    });
                });
            }).observe(document.body, { childList: true, subtree: true });
        }
    });
})();
</script>

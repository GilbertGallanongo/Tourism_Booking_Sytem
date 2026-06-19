// Public JavaScript entrypoint.
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.querySelector('[data-auth-modal]');

    if (!modal) {
        return;
    }

    const triggers = document.querySelectorAll('[data-auth-open]');
    const closers = modal.querySelectorAll('[data-auth-close]');
    const modalTitle = modal.querySelector('#auth-modal-title');

    const setModalTitle = (mode) => {
        if (!modalTitle) {
            return;
        }

        if (mode === 'register') {
            modalTitle.textContent = 'Create your Bolinao account';
        } else if (mode === 'token') {
            modalTitle.textContent = 'Continue with access token';
        } else {
            modalTitle.textContent = 'Log in to your account';
        }
    };

    const openModal = (mode = 'signin') => {
        modal.hidden = false;
        document.body.style.overflow = 'hidden';

        const selectedMode = ['signin', 'register', 'token'].includes(mode) ? mode : 'signin';

        modal.classList.toggle('auth-modal-register', selectedMode === 'register');
        setModalTitle(selectedMode);

        const panes = modal.querySelectorAll('.auth-pane');
        panes.forEach((p) => {
            const paneMode = p.getAttribute('data-auth-pane');
            if (paneMode === selectedMode) {
                p.classList.add('active');
            } else {
                p.classList.remove('active');
            }
        });

        const firstInput = modal.querySelector('.auth-pane.active input');
        firstInput?.focus();
    };

    const closeModal = () => {
        modal.hidden = true;
        document.body.style.overflow = '';
        modal.classList.remove('auth-modal-register');

        const panes = modal.querySelectorAll('.auth-pane');
        panes.forEach((p) => p.classList.remove('active'));
        const signin = modal.querySelector('[data-auth-pane="signin"]');
        if (signin) signin.classList.add('active');
        setModalTitle('signin');
    };

    const openFromQuery = () => {
        const params = new URLSearchParams(window.location.search);
        const mode = params.get('auth');

        if (['signin', 'register', 'token'].includes(mode)) {
            openModal(mode);
            params.delete('auth');
            const url = `${window.location.pathname}${params.toString() ? `?${params.toString()}` : ''}`;
            window.history.replaceState({}, document.title, url);
        }
    };

    const openOnError = () => {
        const errorPanel = modal.querySelector('.auth-pane .alert-error');
        if (errorPanel) {
            const pane = errorPanel.closest('.auth-pane');
            const mode = pane?.getAttribute('data-auth-pane') || 'signin';
            openModal(mode);
        }
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            const mode = trigger.dataset.authMode || trigger.dataset.authmode || 'signin';
            openModal(mode);
        });
    });

    closers.forEach((closer) => {
        closer.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });

    openFromQuery();
    openOnError();
});

// --- Minimal package loader for non-built frontend ---
(function(){
    const backendUrl = window.__API_URL__ || '';
    const statusText = document.getElementById('status-text');
    const packagesEl = document.getElementById('packages');
    const backendLink = document.getElementById('backend-link');
    const apiLink = document.getElementById('api-link');

    function normalizeUrl(url) { return (url || '').replace(/\/$/, ''); }
    function formatCurrency(value) {
        try { return new Intl.NumberFormat('en-PH',{style:'currency',currency:'PHP'}).format(value||0); }
        catch(e){ return 'PHP ' + (value||0); }
    }

    function renderStatus(message){ if(statusText) statusText.textContent = message; }
    function renderError(message){ if(!packagesEl) return; packagesEl.innerHTML = '<div class="message"><strong>Unable to load package data.</strong><p>'+ (message||'') +'</p><p>Make sure the backend is available and the API URL is configured in the environment.</p></div>'; }

    function renderPackages(packages){
        if(!packagesEl) return;
        if(!packages || packages.length===0){ packagesEl.innerHTML = '<div class="message">No packages available yet. Check your backend data or run migrations/seeds.</div>'; return; }
        packagesEl.innerHTML = '';
        packages.forEach(function(packageItem){
            var card = document.createElement('article'); card.className = 'card';
            var rating = (typeof packageItem.rating === 'number') ? packageItem.rating.toFixed(1) : 'N/A';
            card.innerHTML = '<h3>' + (packageItem.name||'Unnamed package') + '</h3>' +
                '<p>' + (packageItem.description||'No description available yet.') + '</p>' +
                '<div class="card-meta"><span><strong>Location:</strong> ' + (packageItem.location||'Unknown') + '</span>' +
                '<span><strong>Duration:</strong> ' + (packageItem.duration_days||'N/A') + ' day(s)</span>' +
                '<span><strong>Price:</strong> ' + formatCurrency(packageItem.price) + '</span>' +
                '<span class="status">Rating ' + rating + '</span></div>' +
                '<p><a href="' + normalizeUrl(backendUrl) + '/packages/' + (packageItem.id||'') + '" target="_blank" rel="noopener">View in backend</a></p>';
            packagesEl.appendChild(card);
        });
    }

    async function loadPackages(){
        if(!backendUrl){ renderStatus('No backend configured. Set VITE_API_URL or APP_URL in the environment.'); if(backendLink){backendLink.href='#';backendLink.textContent='Configure API URL';} if(apiLink){apiLink.href='#';apiLink.textContent='No backend configured';} renderError('API URL is not defined.'); return; }
        var apiBase = normalizeUrl(backendUrl);
        if(backendLink) backendLink.href = apiBase;
        if(apiLink) apiLink.href = apiBase + '/api/packages';
        renderStatus('Loading packages from backend...');
        try{
            var resp = await fetch(apiBase + '/api/packages', { headers: { 'Accept': 'application/json' } });
            if(!resp.ok) throw new Error('Backend responded with status ' + resp.status);
            var json = await resp.json(); renderStatus('Connected to backend.'); renderPackages(json.data || []);
        } catch (err) { renderStatus('Backend connection failed.'); renderError(err.message || 'An unknown error occurred.'); }
    }

    if (document.readyState === 'loading') { window.addEventListener('DOMContentLoaded', loadPackages); } else { loadPackages(); }
})();

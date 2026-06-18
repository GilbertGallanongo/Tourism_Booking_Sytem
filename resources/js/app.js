import '../css/app.css';
import './bootstrap';

const backendUrl = import.meta.env.VITE_API_URL || '';
const statusText = document.getElementById('status-text');
const packagesEl = document.getElementById('packages');
const backendLink = document.getElementById('backend-link');
const apiLink = document.getElementById('api-link');

function normalizeUrl(url) {
    return url.replace(/\/$/, '');
}

function formatCurrency(value) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(value || 0);
}

function renderStatus(message) {
    if (statusText) {
        statusText.textContent = message;
    }
}

function renderError(message) {
    if (!packagesEl) {
        return;
    }

    packagesEl.innerHTML = `
        <div class="message">
            <strong>Unable to load package data.</strong>
            <p>${message}</p>
            <p>Make sure the backend is available and <code>VITE_API_URL</code> is configured in Vercel.</p>
        </div>
    `;
}

function renderPackages(packages) {
    if (!packagesEl) {
        return;
    }

    if (!packages || packages.length === 0) {
        packagesEl.innerHTML = '<div class="message">No packages available yet. Check your backend data or run migrations/seeds.</div>';
        return;
    }

    packagesEl.innerHTML = '';

    packages.forEach((packageItem) => {
        const card = document.createElement('article');
        card.className = 'card';
        const rating = typeof packageItem.rating === 'number' ? packageItem.rating.toFixed(1) : 'N/A';

        card.innerHTML = `
            <h3>${packageItem.name || 'Unnamed package'}</h3>
            <p>${packageItem.description || 'No description available yet.'}</p>
            <div class="card-meta">
                <span><strong>Location:</strong> ${packageItem.location || 'Unknown'}</span>
                <span><strong>Duration:</strong> ${packageItem.duration_days || 'N/A'} day(s)</span>
                <span><strong>Price:</strong> ${formatCurrency(packageItem.price)}</span>
                <span class="status">Rating ${rating}</span>
            </div>
            <p><a href="${normalizeUrl(backendUrl)}/packages/${packageItem.id}" target="_blank" rel="noopener">View in backend</a></p>
        `;

        packagesEl.appendChild(card);
    });
}

async function loadPackages() {
    if (!backendUrl) {
        renderStatus('No backend configured. Set VITE_API_URL in your Vercel environment.');

        if (backendLink) {
            backendLink.href = '#';
            backendLink.textContent = 'Configure VITE_API_URL';
        }
        if (apiLink) {
            apiLink.href = '#';
            apiLink.textContent = 'No backend configured';
        }

        renderError('VITE_API_URL is not defined.');
        return;
    }

    const apiBase = normalizeUrl(backendUrl);

    if (backendLink) {
        backendLink.href = apiBase;
    }
    if (apiLink) {
        apiLink.href = `${apiBase}/api/packages`;
    }

    renderStatus('Loading packages from backend...');

    try {
        const response = await fetch(`${apiBase}/api/packages`, {
            headers: {
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error(`Backend responded with status ${response.status}`);
        }

        const json = await response.json();
        renderStatus('Connected to backend.');
        renderPackages(json.data || []);
    } catch (error) {
        renderStatus('Backend connection failed.');
        renderError(error.message || 'An unknown error occurred.');
    }
}

window.addEventListener('DOMContentLoaded', loadPackages);

// Auth modal handler
window.addEventListener('DOMContentLoaded', () => {
    const authModal = document.querySelector('[data-auth-modal]');
    if (!authModal) return;

    const authPanes = authModal.querySelectorAll('[data-auth-pane]');
    const authOpenButtons = document.querySelectorAll('[data-auth-open]');
    const authCloseButtons = authModal.querySelectorAll('[data-auth-close]');
    const authTitle = authModal.querySelector('#auth-modal-title');
    const authSubtitle = authModal.querySelector('#auth-modal-subtitle');

    const authCopy = {
        signin: {
            title: 'Sign in to your account',
            subtitle: 'Access your bookings, reservations, and saved trip details.',
        },
        register: {
            title: 'Create your account',
            subtitle: 'Set up a tourist account before booking your Bolinao trip.',
        },
    };

    const setAuthMode = (mode = 'signin') => {
        const selectedMode = mode === 'register' ? 'register' : 'signin';

        authModal.classList.toggle('auth-modal-register', selectedMode === 'register');

        authPanes.forEach(pane => {
            pane.classList.toggle('active', pane.getAttribute('data-auth-pane') === selectedMode);
        });

        if (authTitle) {
            authTitle.textContent = authCopy[selectedMode].title;
        }

        if (authSubtitle) {
            authSubtitle.textContent = authCopy[selectedMode].subtitle;
        }
    };

    const openAuthModal = (mode = 'signin') => {
        authModal.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';
        setAuthMode(mode);
        authModal.querySelector('.auth-pane.active input')?.focus();
    };

    const closeAuthModal = () => {
        authModal.setAttribute('hidden', '');
        document.body.style.overflow = '';
        setAuthMode('signin');
    };

    // Open modal
    authOpenButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const mode = button.getAttribute('data-auth-mode') || 'signin';
            openAuthModal(mode);
        });
    });

    // Close modal
    authCloseButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            closeAuthModal();
        });
    });

    // Close on backdrop click
    const backdrop = authModal.querySelector('[data-auth-close]');
    if (backdrop) {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                closeAuthModal();
            }
        });
    }

    // Switch panes via auth-open links inside modal
    authModal.querySelectorAll('[data-auth-open]').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const mode = link.getAttribute('data-auth-mode') || 'signin';
            setAuthMode(mode);
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !authModal.hasAttribute('hidden')) {
            closeAuthModal();
        }
    });

    const params = new URLSearchParams(window.location.search);
    const requestedMode = params.get('auth');
    if (requestedMode === 'signin' || requestedMode === 'register') {
        openAuthModal(requestedMode);
        params.delete('auth');
        const nextQuery = params.toString();
        const nextUrl = `${window.location.pathname}${nextQuery ? `?${nextQuery}` : ''}`;
        window.history.replaceState({}, document.title, nextUrl);
    }
});

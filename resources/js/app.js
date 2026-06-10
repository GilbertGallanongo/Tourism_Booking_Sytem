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

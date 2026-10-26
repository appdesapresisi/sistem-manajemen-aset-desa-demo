/**
 * API Helper for Laravel + Fetch API
 * Automatically includes CSRF token and credentials for all API requests
 */

// Get CSRF token from meta tag
window.getCsrfToken = window.getCsrfToken || function() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
};

// Default fetch options with CSRF token and credentials
window.defaultFetchOptions = window.defaultFetchOptions || {
    credentials: 'same-origin', // Include cookies for session authentication
    headers: {
        'X-CSRF-TOKEN': window.getCsrfToken(),
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
};
var defaultFetchOptions = window.defaultFetchOptions;

/**
 * Enhanced fetch with automatic CSRF token and credentials
 * @param {string} url - API endpoint
 * @param {object} options - Fetch options
 * @returns {Promise<Response>}
 */
window.apiFetch = function(url, options = {}) {
    // Merge default options with provided options
    const mergedOptions = {
        ...defaultFetchOptions,
        ...options,
        headers: {
            ...defaultFetchOptions.headers,
            ...(options.headers || {})
        }
    };

    // If body is FormData, remove Content-Type (let browser set it with boundary)
    if (mergedOptions.body instanceof FormData) {
        delete mergedOptions.headers['Content-Type'];
    }

    return fetch(url, mergedOptions);
};

/**
 * API GET request
 */
window.apiGet = function(url, options = {}) {
    return window.apiFetch(url, { ...options, method: 'GET' });
};

/**
 * API POST request
 */
window.apiPost = function(url, data, options = {}) {
    return window.apiFetch(url, {
        ...options,
        method: 'POST',
        body: data instanceof FormData ? data : JSON.stringify(data)
    });
};

/**
 * API PUT request
 */
window.apiPut = function(url, data, options = {}) {
    return window.apiFetch(url, {
        ...options,
        method: 'PUT',
        body: JSON.stringify(data)
    });
};

/**
 * API DELETE request
 */
window.apiDelete = function(url, options = {}) {
    return window.apiFetch(url, { ...options, method: 'DELETE' });
};

/**
 * Handle API response with automatic error handling
 * @param {Promise<Response>} fetchPromise
 * @returns {Promise<any>}
 */
window.handleApiResponse = async function(fetchPromise) {
    try {
        const response = await fetchPromise;
        
        if (!response.ok) {
            // Try to get error message from JSON
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `HTTP Error: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
};

// console.log('✅ API Helper loaded - use apiFetch(), apiGet(), apiPost(), apiPut(), apiDelete()');

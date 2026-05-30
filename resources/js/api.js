import axios from 'axios';

/**
 * Shared axios instance pointed at the Laravel JSON API.
 * API routes are stateless (no session/CSRF), so a bare instance is enough.
 */
const api = axios.create({
    baseURL: '/api',
    headers: { Accept: 'application/json' },
});

export default api;

/** Pull a human-readable message out of an axios error. */
export function errorMessage(error, fallback = 'Что-то пошло не так') {
    return (
        error?.response?.data?.message ||
        error?.response?.data?.errors?.[Object.keys(error?.response?.data?.errors || {})[0]]?.[0] ||
        error?.message ||
        fallback
    );
}

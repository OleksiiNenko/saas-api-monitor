/**
 * Frontend helpers for presenting monitor data.
 *
 * Kept framework-agnostic so they can be unit-tested in isolation and reused
 * once the dashboard UI lands.
 */

/**
 * Format a response time in milliseconds into a human-readable string.
 *
 * @param {number} ms - Response time in milliseconds.
 * @returns {string}
 */
export function formatLatency(ms) {
    if (ms === null || ms === undefined || Number.isNaN(ms)) {
        return 'n/a';
    }

    if (ms < 1000) {
        return `${Math.round(ms)} ms`;
    }

    return `${(ms / 1000).toFixed(2)} s`;
}

/**
 * Decide whether an observed HTTP status matches what a monitor expects.
 *
 * @param {number} expected - The status code the monitor expects.
 * @param {number} actual - The status code that was observed.
 * @returns {boolean}
 */
export function isHealthy(expected, actual) {
    return Number(expected) === Number(actual);
}

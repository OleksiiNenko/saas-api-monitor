import { describe, expect, it } from 'vitest';
import { formatLatency, isHealthy } from './format.js';

describe('formatLatency', () => {
    it('renders sub-second values in milliseconds', () => {
        expect(formatLatency(0)).toBe('0 ms');
        expect(formatLatency(250)).toBe('250 ms');
        expect(formatLatency(999)).toBe('999 ms');
    });

    it('renders values >= 1s in seconds', () => {
        expect(formatLatency(1000)).toBe('1.00 s');
        expect(formatLatency(1530)).toBe('1.53 s');
    });

    it('handles missing values gracefully', () => {
        expect(formatLatency(null)).toBe('n/a');
        expect(formatLatency(undefined)).toBe('n/a');
        expect(formatLatency(NaN)).toBe('n/a');
    });
});

describe('isHealthy', () => {
    it('is true when expected and actual status match', () => {
        expect(isHealthy(200, 200)).toBe(true);
        expect(isHealthy('200', 200)).toBe(true);
    });

    it('is false otherwise', () => {
        expect(isHealthy(200, 500)).toBe(false);
    });
});

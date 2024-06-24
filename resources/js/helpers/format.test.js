import { describe, expect, it } from 'vitest';
import { formatDate } from '@/helpers/format.js';

describe('format helpers', () => {
    describe('formatDate', () => {
        it('returns the date in a more readable format', () => {
            const date = new Date('2022-07-23T00:00:00');
            expect(formatDate(date)).toEqual('July 23, 2022');
        });
    });
});

import { inject } from 'vue';

export function useRoute() {
    /** @type {import('ziggy-js').route} */
    const route = inject('route');

    return { route };
}

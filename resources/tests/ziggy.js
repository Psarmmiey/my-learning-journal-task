import { beforeEach, vi } from 'vitest';
import { route as _route } from 'ziggy-js';

import { Ziggy as Generated } from '@/ziggy.js';

const Ziggy = {
    .../** @type {import('ziggy-js').Config} */ Generated,
    url: 'http://localhost',
    port: null,
};

/** @type {import('ziggy-js').route} */
const route = (
    name = undefined,
    params = undefined,
    absolute = undefined,
    config = Ziggy,
) => _route(name, params, absolute, config);
const routeSpy = vi.fn(route);

beforeEach(() => {
    routeSpy.mockRestore();
});

const ZiggyVue = {
    install(app) {
        app.config.globalProperties.route = routeSpy;
        app.provide('route', routeSpy);
    },
};

export { ZiggyVue, route, routeSpy };

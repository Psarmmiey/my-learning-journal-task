import globals from 'globals';
import pluginJs from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';

export default [
    { files: ['**/*.{js,mjs,cjs,vue}'] },
    { languageOptions: { globals: globals.browser } },
    pluginJs.configs.recommended,
    ...pluginVue.configs['flat/essential'],
    {
        ignores: [
            '**/vendor/',
            '**/public/**',
            '**/storage/**',
            '**/node_modules/**',
            'resources/js/ziggy.js',
            'resources/js/ziggy.d.ts',
        ],
    },
    {
        rules: {
            // Eslint rules.
            'array-callback-return': 'error',
            'no-unreachable-loop': 'error',
            'no-use-before-define': 'warn',
            'no-shadow': 'error',
            'vue/multi-word-component-names': 'off',
        },
    },
];

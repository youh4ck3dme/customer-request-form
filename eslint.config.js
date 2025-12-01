import tsParser from '@typescript-eslint/parser';
import tseslint from '@typescript-eslint/eslint-plugin';
import reactPlugin from 'eslint-plugin-react';
import reactHooks from 'eslint-plugin-react-hooks';

export default [
    {
        files: ['**/*.{js,jsx,ts,tsx}'],
        languageOptions: {
            parser: tsParser,
            parserOptions: {
                ecmaVersion: 2021,
                sourceType: 'module',
                ecmaFeatures: { jsx: true },
            },
        },
        plugins: {
            '@typescript-eslint': tseslint,
            react: reactPlugin,
            'react-hooks': reactHooks,
        },
        rules: {},
        settings: { react: { version: 'detect' } },
    },
];

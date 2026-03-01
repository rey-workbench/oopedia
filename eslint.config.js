import js from '@eslint/js'
import tseslint from 'typescript-eslint'
import svelte from 'eslint-plugin-svelte'
import svelteParser from 'svelte-eslint-parser'

export default [
  js.configs.recommended,

  ...tseslint.configs.recommended,

  {
    files: ['**/*.svelte'],
    languageOptions: {
      parser: svelteParser,
      parserOptions: {
        parser: tseslint.parser,
        extraFileExtensions: ['.svelte'],
        sourceType: 'module'
      }
    },
    plugins: {
      svelte
    },
    rules: {
      ...svelte.configs.recommended.rules
    }
  },

  {
    rules: {
      '@typescript-eslint/no-explicit-any': 'error'
    }
  }
]
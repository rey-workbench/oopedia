import typography from '@tailwindcss/typography';
import type { Config } from 'tailwindcss';

export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './resources/**/*.svelte',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#0c0c14',
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#0c0c14',
                    600: '#05050a',
                    700: '#020205',
                    800: '#000000',
                    900: '#000000',
                },
                accent: {
                    DEFAULT: '#7c3aed',
                    50: '#f5f3ff',
                    100: '#ede9fe',
                    200: '#ddd6fe',
                    300: '#c4b5fd',
                    400: '#a78bfa',
                    500: '#7c3aed',
                    600: '#6d28d2',
                    700: '#5b21b6',
                    800: '#4c1d95',
                    900: '#2e1065',
                },
                blue: {
                    primary: '#0c0c14',
                    secondary: '#1e1b4b',
                    accent: '#7c3aed',
                    dark: '#05050a',
                    light: '#f1f5f9',
                },
            },
            backgroundImage: {
                none: 'none',
            },
            fontFamily: {
                sans: [
                    'Inter',
                    'ui-sans-serif',
                    'system-ui',
                    '-apple-system',
                    'BlinkMacSystemFont',
                    'Segoe UI',
                    'Roboto',
                    'Helvetica Neue',
                    'Arial',
                    'sans-serif',
                ],
                display: ['Poppins', 'Inter', 'sans-serif'],
            },
            typography: {
                DEFAULT: {
                    css: {
                        maxWidth: '100%',
                        color: '#1a1a1a',
                        fontFamily: 'Segoe UI, Arial, sans-serif',
                        h1: {
                            fontWeight: '700',
                            color: '#1a1a1a',
                        },
                        h2: {
                            fontWeight: '600',
                            color: '#1a1a1a',
                            marginTop: '2em',
                        },
                        h3: {
                            fontWeight: '600',
                            color: '#1a1a1a',
                        },
                        strong: {
                            fontWeight: '700',
                            color: '#1a1a1a',
                        },
                        pre: {
                            backgroundColor: '#1E1E1E', // VS Code Dark
                            color: '#D4D4D4',
                            borderLeft: '5px solid #04AA6D',
                            borderRadius: '8px',
                            padding: '24px',
                            fontFamily: 'Consolas, monospace',
                            fontSize: '15px',
                            lineHeight: '1.6',
                            position: 'relative',
                            boxShadow:
                                '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)', // Shadow-md
                        },
                        code: {
                            color: '#d63384',
                            backgroundColor: '#f1f1f1',
                            padding: '2px 4px',
                            borderRadius: '4px',
                            fontWeight: '500',
                        },
                        'code::before': {
                            content: '""',
                        },
                        'code::after': {
                            content: '""',
                        },
                        // Ensure nested code blocks in pre don't double up styles
                        'pre code': {
                            backgroundColor: 'transparent',
                            color: 'inherit',
                            padding: '0',
                            borderRadius: '0',
                            fontSize: 'inherit',
                            fontWeight: 'inherit',
                        },
                    },
                },
            },
        },
    },
    plugins: [typography],
} satisfies Config;

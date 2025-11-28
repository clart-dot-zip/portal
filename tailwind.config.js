import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: [
                    'Segoe UI',
                    'system-ui',
                    '-apple-system',
                    'BlinkMacSystemFont',
                    'Roboto',
                    'Oxygen',
                    'Ubuntu',
                    'Cantarell',
                    'Helvetica Neue',
                    'sans-serif'
                ],
            },
            colors: {
                // Fluent UI 2 Color Tokens - Microsoft Azure Portal Theme
                fluent: {
                    // Primary Brand Colors
                    'brand': {
                        DEFAULT: '#0078d4', // Azure Blue
                        10: '#e6f2fb',
                        20: '#cce5f7',
                        30: '#99cbef',
                        40: '#66b0e7',
                        50: '#3395df',
                        60: '#0078d4', // Primary
                        70: '#006cbe',
                        80: '#005ba1',
                        90: '#004b84',
                        100: '#003b67',
                    },
                    // Neutral Colors
                    'neutral': {
                        DEFAULT: '#605e5c',
                        0: '#ffffff',
                        4: '#faf9f8',
                        6: '#f5f5f5',
                        8: '#f3f2f1',
                        10: '#edebe9',
                        12: '#e1dfdd',
                        14: '#d2d0ce',
                        16: '#c8c6c4',
                        18: '#bebbb8',
                        20: '#b3b0ad',
                        22: '#a19f9d',
                        24: '#979593',
                        26: '#8a8886',
                        28: '#7a7574',
                        30: '#605e5c',
                        40: '#484644',
                        50: '#323130',
                        60: '#292827',
                        70: '#252423',
                        80: '#201f1e',
                        90: '#1b1a19',
                        100: '#161514',
                        110: '#11100f',
                        120: '#0b0a09',
                    },
                    // Semantic Colors
                    'success': '#107c10',
                    'warning': '#ffc83d',
                    'error': '#d13438',
                    'info': '#0078d4',
                    // Background Colors
                    'bg': {
                        DEFAULT: '#faf9f8',
                        'canvas': '#ffffff',
                        'overlay': '#ffffff',
                        'subtle': '#f3f2f1',
                    },
                },
            },
            spacing: {
                // Fluent UI spacing scale (4px base)
                '1': '4px',
                '2': '8px',
                '3': '12px',
                '4': '16px',
                '5': '20px',
                '6': '24px',
                '7': '28px',
                '8': '32px',
                '9': '36px',
                '10': '40px',
                '12': '48px',
                '16': '64px',
                '20': '80px',
                '24': '96px',
            },
            borderRadius: {
                'none': '0',
                'sm': '2px',
                'DEFAULT': '4px',
                'md': '6px',
                'lg': '8px',
                'xl': '12px',
            },
            boxShadow: {
                // Fluent UI elevation shadows
                'depth4': '0 1.6px 3.6px 0 rgba(0, 0, 0, 0.132), 0 0.3px 0.9px 0 rgba(0, 0, 0, 0.108)',
                'depth8': '0 3.2px 7.2px 0 rgba(0, 0, 0, 0.132), 0 0.6px 1.8px 0 rgba(0, 0, 0, 0.108)',
                'depth16': '0 6.4px 14.4px 0 rgba(0, 0, 0, 0.132), 0 1.2px 3.6px 0 rgba(0, 0, 0, 0.108)',
                'depth64': '0 25.6px 57.6px 0 rgba(0, 0, 0, 0.22), 0 4.8px 14.4px 0 rgba(0, 0, 0, 0.18)',
            },
            fontSize: {
                // Fluent UI type ramp
                'caption': ['12px', { lineHeight: '16px', fontWeight: '400' }],
                'body': ['14px', { lineHeight: '20px', fontWeight: '400' }],
                'body-strong': ['14px', { lineHeight: '20px', fontWeight: '600' }],
                'subtitle': ['18px', { lineHeight: '24px', fontWeight: '600' }],
                'title': ['28px', { lineHeight: '36px', fontWeight: '600' }],
                'large-title': ['40px', { lineHeight: '52px', fontWeight: '600' }],
            },
            transitionTimingFunction: {
                // Fluent UI motion curves
                'fluent-accelerate': 'cubic-bezier(0.9, 0.1, 1, 0.2)',
                'fluent-decelerate': 'cubic-bezier(0.1, 0.9, 0.2, 1)',
                'fluent-standard': 'cubic-bezier(0.8, 0, 0.2, 1)',
            },
            transitionDuration: {
                'fluent-fast': '100ms',
                'fluent-normal': '200ms',
                'fluent-slow': '400ms',
            },
        },
    },

    corePlugins: {
        preflight: true, // Enabled for Fluent UI reset
    },

    plugins: [forms],
};

import defaultTheme from 'tailwindcss/defaultTheme';
import daisyui from 'daisyui';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    violet:          '#7C3AED',
                    'violet-dark':   '#6D28D9',
                    'violet-light':  '#F5F3FF',
                    gold:            '#F59E0B',
                    'gold-dark':     '#D97706',
                },
                sidebar: {
                    from: '#1B0D35',
                    to:   '#0E0720',
                }
            }
        },
    },
    plugins: [daisyui],
    daisyui: {
        themes: [{
            brandara: {
                "primary":           "#7C3AED",
                "primary-content":   "#ffffff",
                "secondary":         "#F59E0B",
                "secondary-content": "#ffffff",
                "accent":            "#10B981",
                "neutral":           "#0F172A",
                "base-100":          "#ffffff",
                "base-200":          "#FAFBFF",
                "base-300":          "#E2E8F0",
                "base-content":      "#0F172A",
                "info":              "#3B82F6",
                "success":           "#10B981",
                "warning":           "#F59E0B",
                "error":             "#EF4444",
            }
        }],
        darkTheme: false,
    }
};

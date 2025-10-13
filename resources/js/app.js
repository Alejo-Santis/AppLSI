import './bootstrap';
import '../css/app.css';
import { createInertiaApp } from '@inertiajs/svelte';
import { mount } from 'svelte';

// Importar Bootstrap JS para dropdowns, tooltips, etc.
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Importar Iconify (iconos)
import 'iconify-icon';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.svelte', { eager: true });
        return pages[`./Pages/${name}.svelte`];
    },
    setup({ el, App, props }) {
        mount(App, { target: el, props });
    },
    progress: {
        color: '#4B5563',
    },
});

import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import MainLayout from "@/Layouts/MainLayout.vue";
import { Head } from "@inertiajs/vue3";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import {translations} from "@/Mixins/translations.js";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';


createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        return resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'))
            .then((component) => {
                component.default.layout = component.default.layout || MainLayout; // Set the default layout
                return component;
            });
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mixin(translations)
            .use(Toast, {
                timeout: 1750,
                pauseOnFocusLoss: false,
                maxToasts: 3,
                toastClassName: 'custom',
                bodyClassName: ['custom'],
                // You can set your default options here
            });
        app.component('Head', Head); // Register the Head component globally
        app.component('Link', Link); // Register the Link component globally

        return app.mount(el);
    },
    progress: {
        color: '#000000',
    },
});

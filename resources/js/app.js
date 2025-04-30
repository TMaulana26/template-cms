import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import Toast from "vue-toastification";
import { i18nVue } from "laravel-vue-i18n";

const toastOptions = {
    position: "top-right",
    timeout: 3000,
    closeOnClick: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: "button",
    icon: true,
    rtl: false,
    allowHtml: true,
};

const appName = import.meta.env.VITE_APP_NAME || "Template CMS";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Toast, toastOptions)
            .use(i18nVue, {
                resolve: (lang) => {
                    const langs = import.meta.glob("../../lang/*.json", {
                        eager: true,
                    });
                    return langs[`../../lang/${lang}.json`].default;
                },
            })
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});

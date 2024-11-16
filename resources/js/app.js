import "./bootstrap";
import "../css/app.css";

import { install as VueMonacoEditorPlugin } from "@guolao/vue-monaco-editor";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

import Toast from "vue-toastification";
// Import the CSS or use your own!
import "vue-toastification/dist/index.css";
import "@vueup/vue-quill/dist/vue-quill.bubble.prod.css";
import "@vueup/vue-quill/dist/vue-quill.snow.prod.css";

const appName = import.meta.env.VITE_APP_NAME || "Oneway";

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue")),
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(Toast, {})
      .use(VueMonacoEditorPlugin, {
        paths: {
          // The recommended CDN config
          vs: "https://cdn.jsdelivr.net/npm/monaco-editor@0.43.0/min/vs",
        },
      })
      .mount(el);
  },
  progress: {
    color: "#4B5563",
  },
}).then((r) => {});

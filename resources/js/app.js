import "./bootstrap"
import "../css/app.css"

import { createApp, h } from "vue"
import { createInertiaApp } from "@inertiajs/vue3"
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers"
import { ZiggyVue } from 'ziggy-js';
import { createPinia } from 'pinia'

// PrimeVue v4
import PrimeVue from "primevue/config"
import ToastService from "primevue/toastservice"
import ConfirmationService from "primevue/confirmationservice"
import Tooltip from "primevue/tooltip"

// PrimeVue v4 CSS y Temas
import Aura from '@primeuix/themes/aura'
import 'primeicons/primeicons.css'
import "primeflex/primeflex.css"

// Componentes globales comunes
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Toast from 'primevue/toast'
import ConfirmDialog from 'primevue/confirmdialog'

const appName = import.meta.env.VITE_APP_NAME || "Taller de Vehículos"

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue")),
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(createPinia())
      .use(PrimeVue, {
        theme: {
          preset: Aura,
          options: {
            prefix: 'p',
            darkModeSelector: '.dark',
            cssLayer: false
          }
        }
      })
      .use(ToastService)
      .use(ConfirmationService)
      .directive("tooltip", Tooltip)
      // Componentes globales
      .component('Button', Button)
      .component('InputText', InputText)
      .component('Toast', Toast)
      .component('ConfirmDialog', ConfirmDialog)
      .mount(el)
  },
  progress: {
    color: "#4B5563",
  },
})
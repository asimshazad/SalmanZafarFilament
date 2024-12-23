import './bootstrap';
import '../css/app.css';
import './functions';

import { createApp, h } from 'vue';
import { createInertiaApp, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Notifications from 'notiwind';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/src/sweetalert2.scss';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
const appName = import.meta.env.VITE_APP_NAME || 'Salman Zafar';

createInertiaApp({
    title: (title) => {
      if (title) {
          return `${title} - ${appName}`;
      } else {
          return appName;
      }
    },
    resolve: (name) =>
      resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
      const app = createApp({
        render: () => h(App, props),
      })
      .use(plugin)
      .use(ZiggyVue)
      .use(Notifications)
      .use(VueSweetalert2)
        app.config.errorHandler = function (err, vm, info) {
            console.error('Vue Error:', err, info);
        };
        app.component('Link', Link);
        app.component('VueSelect', VueSelect);
        app.mount(el);
    },
    progress: {
      color: '#4B5563',
    },
  });


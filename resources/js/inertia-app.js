/**
 * inertia-app.js
 * 
 * Entry point TERPISAH untuk halaman Inertia.
 * File ini TIDAK mengganggu app.js (SPA Vue Router + Pinia) yang sudah ada.
 * 
 * Dipakai HANYA oleh: resources/views/inertia-app.blade.php
 * Route yang dilayani: /inertia-demo
 */

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

import './assets/silab.css';

createInertiaApp({
  // Nama tab browser
  title: (title) => {
    const metaApp = document.querySelector('meta[name="application-name"]');
    const appName = metaApp ? metaApp.content : 'SILAB';
    return title ? `${title} – ${appName}` : appName;
  },

  // Resolver: cari komponen di folder resources/js/inertia-pages/
  resolve: (name) =>
    resolvePageComponent(
      `./inertia-pages/${name}.vue`,
      import.meta.glob('./inertia-pages/**/*.vue')
    ),

  // Setup Vue app
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },

  // Tampilkan progress bar saat navigasi Inertia
  progress: {
    color: '#3b82f6',
  },
})

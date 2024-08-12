import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

import { createApp } from 'vue';
import { createRouter, createWebHashHistory } from 'vue-router';
import { routes } from "./routes";
import { CkeditorPlugin } from '@ckeditor/ckeditor5-vue';



import App from './Apps.vue'
let app = createApp(App)

const router = createRouter({
    history: createWebHashHistory(),
    routes: routes,
})

app.use( CkeditorPlugin )
app.use(router);
app.mount("#app")

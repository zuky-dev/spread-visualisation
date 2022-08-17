import './bootstrap';

import { createApp } from 'vue';
import store from './store';
import App from './vue/App.vue';


createApp(App)
    .use(store)
    .mount('#app');
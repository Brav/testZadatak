import axios from 'axios';
window.axios = axios;

import { createApp } from 'vue'
import App from '../App.vue'
import '../css/app.css'

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

createApp(App).mount('#app')

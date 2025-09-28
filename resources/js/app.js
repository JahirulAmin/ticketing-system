import { createApp, provide, ref } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import 'bootstrap/dist/css/bootstrap.min.css';

const token = localStorage.getItem('token');
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}


window.Echo = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
  wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
  wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
  forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'http') === 'https',
  enabledTransports: ['ws', 'wss'],
});

const user = ref(null);
const fetchUser = async () => {
  const token = localStorage.getItem('token');
  if (!token) {
    localStorage.removeItem('user');
    return null;
  }
  try {
    const response = await axios.get('/api/user');
    user.value = response.data;
    localStorage.setItem('user', JSON.stringify(response.data));
    return response.data;
  } catch (error) {
    console.error('User fetch failed:', error.response ? error.response.status : error.message);
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    delete axios.defaults.headers.common['Authorization'];
    user.value = null;
    return null;
  }
};

const app = createApp(App);
app.use(router);
app.provide('user', user); 
app.provide('fetchUser', fetchUser); 

app.mount('#app').$nextTick(() => {
  if (token) {
    fetchUser();
  }
});
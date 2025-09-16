<template>
  <div id="app">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container">
        <a class="navbar-brand" href="#">Ticketing System</a>
        <div v-if="user" class="navbar-nav ms-auto">
          <span class="nav-link">Welcome, {{ user.name }} ({{ user.role }})</span>
          <button @click="logout" class="btn btn-outline-danger">Logout</button>
        </div>
        <div v-else class="navbar-nav ms-auto">
          <router-link to="/login" class="nav-link">Login</router-link>
          <router-link to="/register" class="nav-link">Register</router-link>
        </div>
      </div>
    </nav>
    <div class="container mt-4">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import { inject } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
  setup() {
    const router = useRouter();
    const user = inject('user');

    const logout = async () => {
      await axios.post('/api/logout');
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      delete axios.defaults.headers.common['Authorization'];
      user.value = null; 
      router.push('/login');
    };

    return { user, logout };
  },
};
</script>
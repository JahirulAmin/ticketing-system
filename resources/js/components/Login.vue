<template>
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h3>Login</h3>
          <form @submit.prevent="login">
            <div class="mb-3">
              <input v-model="form.email" type="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
              <input v-model="form.password" type="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
          </form>
          <p v-if="error" class="text-danger">{{ error }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
  setup() {
    const form = ref({ email: '', password: '' });
    const error = ref('');
    const router = useRouter();

    const login = async () => {
      try {
        const response = await axios.post('/api/login', form.value);
        localStorage.setItem('token', response.data.token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        localStorage.setItem('user', JSON.stringify(response.data.user));
        router.push('/tickets');
      } catch (e) {
        error.value = 'Invalid credentials';
      }
    };

    return { form, error, login };
  },
};
</script>
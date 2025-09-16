<template>
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h3>Register</h3>
          <form @submit.prevent="register">
            <div class="mb-3">
              <input v-model="form.name" type="name" class="form-control" placeholder="Name" required>
            </div>
            <div class="mb-3">
              <input v-model="form.email" type="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
              <input v-model="form.password" type="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="mb-3">
              <input v-model="form.password_confirmation" type="password" class="form-control" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="btn btn-primary">Sign Up</button>
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
    const form = ref({ name: '', email: '', password: '', password_confirmation: '', role: 'customer' });
    const error = ref('');
    const router = useRouter();
    const register = async () => {
      try {
        const response = await axios.post('/api/register', form.value);
        localStorage.setItem('token', response.data.token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        router.push('/tickets');
      } catch (e) {
        error.value = 'Registration failed';
      }
    };

    return { form, error, register };
  }
};
</script>
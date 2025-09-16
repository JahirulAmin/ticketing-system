<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Tickets</h2>
      <button v-if="user.role === 'customer'" @click="$router.push('/tickets/create')"
        class="btn btn-success mb-3">Create Ticket</button>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>Subject</th>
          <th>Status</th>
          <th>Priority</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="ticket in tickets" :key="ticket.id">
          <td>{{ ticket.subject }}</td>
          <td>{{ ticket.status }}</td>
          <td>{{ ticket.priority }}</td>
          <td>
            <router-link :to="`/tickets/${ticket.id}/edit`" v-if="user.role === 'customer'" class="btn btn-sm btn-warning">Edit</router-link>
            <router-link :to="`/tickets/${ticket.id}`" class="btn btn-sm btn-info ms-2">View</router-link>
            <button @click="deleteTicket(ticket.id)" class="btn btn-sm btn-danger ms-2">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
  setup() {
    const tickets = ref([]);
    const user = ref(JSON.parse(localStorage.getItem('user') || '{}'));  // Store user on login

    onMounted(async () => {
      const response = await axios.get('/api/tickets');
      tickets.value = response.data;
    });

    const deleteTicket = async (id) => {
      await axios.delete(`/api/tickets/${id}`);
      tickets.value = tickets.value.filter(t => t.id !== id);
    };

    return { tickets, user, deleteTicket };
  }
};
</script>
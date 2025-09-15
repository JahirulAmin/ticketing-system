<template>
  <div class="h-screen flex flex-col bg-gray-100">
    <!-- Bottom: Comments + Chat side by side -->
    <div class="flex-1 grid grid-cols-2 gap-4 m-4">
      <!-- Comments -->
      <div class="bg-white shadow rounded-2xl p-6 flex flex-col">
        <h5 class="text-2xl font-bold mb-4">🎫 Ticket Details</h5>
        <div class="grid grid-cols-2 ">
          <p><strong>Subject:</strong> {{ ticket.subject }}</p>
          <p><strong>Category:</strong> {{ ticket.category }}</p>
          <p><strong>Priority:</strong> {{ ticket.priority }}</p>
          <p><strong>Status:</strong> {{ ticket.status }}</p>
        </div>
        <div v-if="user.role === 'admin'" class="mt-4">
          <label class="block font-semibold mb-2">Update Status</label>
          <select v-model="newStatus" @change="updateStatus" class="w-48 border rounded-lg p-2">
            <option v-for="status in statuses" :key="status" :value="status">
              {{ status }}
            </option>
          </select>
        </div>
        <hr class="my-4">
        <h5 class="text-xl font-semibold mb-4">💬 Comments</h5>
        <div class="flex-1 space-y-3 overflow-hidden">
          <div v-for="comment in comments" :key="comment.id" class="border-b pb-2">
            <p class="font-medium text-gray-800 bg-amber-100 inline-block px-2 py-1 rounded">
              {{ comment.user.name }}
            </p>
            <p class="text-gray-700">{{ comment.comment }}</p>
            <small class="text-gray-500">{{ comment.created_at }}</small>
          </div>
        </div>

        <!-- Add comment -->
        <form @submit.prevent="addComment" class="mt-4 flex gap-2">
          <input v-model="newComment" type="text" placeholder="Write a comment..." class="flex-1 border rounded-lg p-2"
            required>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Submit
          </button>
        </form>
      </div>

      <!-- Chat -->
      <div class="bg-white shadow rounded-2xl p-6 flex flex-col">
        <h3 class="text-xl font-semibold mb-4">📨 Chat with Admin</h3>
        <div class="flex-1 space-y-3 overflow-hidden">
          <div v-for="chat in chats" :key="chat.id" :class="chat.user.id === user.id ? 'text-right' : 'text-left'">
            <div :class="[
              'inline-block px-4 py-2 rounded-xl',
              chat.user.id === user.id
                ? 'bg-blue-600 text-white'
                : 'bg-gray-200 text-gray-800'
            ]">
              <p class="font-medium">{{ chat.user.name }}</p>
              <p>{{ chat.message }}</p>
              <small class="block text-xs text-gray-500">
                {{ chat.created_at }}
              </small>
            </div>
          </div>
        </div>

        <!-- Chat input -->
        <form @submit.prevent="sendMessage" class="mt-4 flex gap-2">
          <input v-model="newMessage" type="text" placeholder="Type a message..." class="flex-1 border rounded-lg p-2"
            required>
          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            Send
          </button>
        </form>
      </div>
    </div>
  </div>
</template>



<script>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

export default {
  setup() {
    const route = useRoute();
    const router = useRouter();
    const ticket = ref({});
    const comments = ref([]);
    const chats = ref([]);
    const newComment = ref('');
    const newMessage = ref('');
    const newStatus = ref('');
    const user = ref(JSON.parse(localStorage.getItem('user') || '{}'));
    const statuses = ['open', 'in_progress', 'resolved', 'closed'];

    // Fetch ticket and related data
    const fetchData = async () => {
      try {
        const ticketResponse = await axios.get(`/api/tickets/${route.params.id}`);
        ticket.value = ticketResponse.data;
        newStatus.value = ticket.value.status;

        const commentsResponse = await axios.get(`/api/tickets/${route.params.id}/comments`);
        comments.value = commentsResponse.data;

        const chatsResponse = await axios.get(`/api/tickets/${route.params.id}/chats`);
        chats.value = chatsResponse.data;
      } catch (error) {
        console.error('Error fetching data:', error);
        router.push('/tickets'); // Redirect if unauthorized or not found
      }
    };

    onMounted(() => {
      fetchData();

      // Real-time chat with Echo
      if (window.Echo) {
        window.Echo.channel(`ticket.${route.params.id}`)
          .listen('TicketChatSent', (e) => {
            chats.value.unshift(e.chat);
          });
      }

      // Poll for comments if real-time fails (fallback)
      const pollInterval = setInterval(fetchData, 5000);
      onUnmounted(() => clearInterval(pollInterval));
    });

    onUnmounted(() => {
      if (window.Echo) {
        window.Echo.leave(`ticket.${route.params.id}`);
      }
    });

    // Add a comment
    const addComment = async () => {
      if (!newComment.value) return;
      try {
        const response = await axios.post(`/api/tickets/${route.params.id}/comments`, {
          comment: newComment.value,
        });
        comments.value.push(response.data);
        newComment.value = '';
      } catch (error) {
        console.error('Error adding comment:', error);
      }
    };

    // Send a chat message
    const sendMessage = async () => {
      if (!newMessage.value) return;
      try {
        const response = await axios.post(`/api/tickets/${route.params.id}/chats`, {
          message: newMessage.value,
        });
        // Real-time broadcast handles the update; no need to push manually
        newMessage.value = '';
      } catch (error) {
        console.error('Error sending message:', error);
      }
    };

    // Update ticket status (admin only)
    const updateStatus = async () => {
      if (user.value.role !== 'admin') return;
      try {
        await axios.put(`/api/tickets/${route.params.id}/status`, {
          status: newStatus.value,
        });
        ticket.value.status = newStatus.value;
      } catch (error) {
        console.error('Error updating status:', error);
      }
    };

    return {
      ticket,
      comments,
      chats,
      newComment,
      newMessage,
      newStatus,
      user,
      statuses,
      addComment,
      sendMessage,
      updateStatus,
    };
  },
};
</script>

<style scoped>
.chat-messages {
  background-color: #f8f9fa;
  padding: 10px;
  border-radius: 5px;
}
</style>
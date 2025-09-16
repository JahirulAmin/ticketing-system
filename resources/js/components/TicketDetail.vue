<template>
  <div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
      <section class="lg:col-span-3 flex flex-col gap-6">
        <div class="bg-white rounded-2xl shadow p-6">
          <div class="flex items-start justify-between">
            <div>
              <h4 class="text-2xl font-semibold text-gray-900">🎫 {{ ticket.subject || 'Ticket subject' }}</h4>
              <p class="text-sm text-gray-500 mt-1">#{{ ticket.id || '—' }} • Created by <span class="font-medium">{{
                ticket.user?.name || 'User' }}</span></p>
            </div>
            <div class="text-right">
              <p class="text-sm p-0 m-0 text-gray-500">Category: <span class="font-bold">{{ ticket.category || '-'
              }}</span></p>
            </div>
          </div>

          <div class="mt-3 grid grid-cols-3 gap-4">
            <div class="p-3 pb-0 bg-gray-50 rounded-lg">
              <p class="text-xs text-gray-500">Priority</p>
              <p class="font-medium">{{ ticket.priority || 'medium' }}</p>
            </div>
            <div class="p-3 pb-0 bg-gray-50 rounded-lg">
              <p class="text-xs text-gray-500">Status</p>
              <p class="font-medium">{{ ticket.status || 'open' }}</p>
            </div>
            <div class="p-3 pb-0 bg-gray-50 rounded-lg">
              <p class="text-xs text-gray-500">Attachment</p>
                <!-- loop attachments -->
                <span v-if="ticket.attachments?.length === 0">No attachment</span>
                <a v-for="(att, index) in ticket.attachments" :key="index" :href="att.file_path" target="_blank"
                  class="text-blue-600 underline mr-2">Attachment {{ index + 1 }}</a>
                
            </div>
          </div>
          <div v-if="user.role === 'admin'" class="mt-4">
            <label class="block font-semibold mb-2 mr-2">Update Status</label>
            <select v-model="newStatus" @change="updateStatus" class="w-48 border rounded-lg p-2 ml-10">
              <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
            </select>
          </div>

          <hr class="my-6" />

          <h5 class="text-lg font-semibold mb-2">Description</h5>
          <p class="text-gray-700 whitespace-pre-line">{{ ticket.description || 'No description provided.' }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-6 flex flex-col">
          <div class="flex items-center justify-between mb-4">
            <h5 class="text-lg font-semibold">💬 Comments</h5>
            <div class="text-sm text-gray-500">{{ comments.length }} comments</div>
          </div>

          <div class="space-y-4 overflow-auto max-h-72 pr-2" ref="commentsScroll">
            <div v-for="c in comments" :key="c.id" class="flex gap-3 items-start">
              <div
                class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 font-semibold">
                {{ initials(c.user?.name) }}</div>
              <div class="flex-1 bg-blue-50 p-3 rounded-2xl pb-0 mb-0">
                <div class="flex items-center justify-between">
                  <p class="text-sm font-medium">{{ c.user?.name || 'Unknown' }}</p>
                  <p class="text-xs text-gray-400">{{ formatDate(c.created_at) }}</p>
                </div>
                <hr class="m-0 p-0" />
                <p class="m-0 p-0 py-2 text-gray-700">{{ c.comment }}</p>
              </div>
            </div>
            <div v-if="!comments.length" class="text-gray-400">No comments yet.</div>
          </div>

          <form @submit.prevent="addComment" class="mt-4 flex gap-2">
            <input v-model="newComment" class="flex-1 border rounded-full px-4 py-2 focus:outline-none"
              placeholder="Write a comment..." />
            <button type="submit"
              class="bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700">Comment</button>
          </form>
        </div>
      </section>
    </div>

    <div class="fixed bottom-4 right-6 z-50 flex flex-col items-end gap-4">
      <button v-if="!showChat" @click="toggleChat" style="border-radius: 10px;"
        class="bg-blue-600 hover:bg-blue-700 text-white rounded-2xl p-2 shadow-lg flex items-center gap-2 transition-transform hover:scale-105">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <span>Support Chat</span>
      </button>
      <div v-show="showChat"
        class="fixed bottom-6 right-6 w-96 rounded-2xl shadow-xl bg-white overflow-hidden transform transition-all"
        :class="showChat ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">
        <div class="flex items-center justify-between px-4 py-3 bg-blue-600 text-white">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
              👋
            </div>
            <div>
              <h4 class="font-semibold">Support Chat</h4>
              <p class="text-xs opacity-75">Ticket #{{ ticket.id }}</p>
            </div>
          </div>
          <button @click="toggleChat" class="hover:bg-white/20 p-1 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
          </button>
        </div>
        <div ref="messagesWrap" class="h-96 overflow-y-auto p-4 space-y-4 bg-gray-50">
          <div v-for="m in chats" :key="m.id" class="flex"
            :class="m.user.id === user.id ? 'justify-end' : 'justify-start'">
            <div
              :class="['max-w-[80%] px-4 py-3 rounded-2xl shadow-sm break-words', m.user.id === user.id ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-900 rounded-bl-none']">
              <div class="flex items-baseline justify-between gap-4">
                <p class="text-sm font-medium">{{ m.user.name }}</p>
                <span class="text-xs text-gray-400 ml-2">{{ formatDate(m.created_at) }}</span>
              </div>
              <p class="mt-1 text-sm leading-relaxed">{{ m.message }}</p>
              <div v-if="m.file_url" class="mt-3">
                <a :href="m.file_url" target="_blank"
                  class="inline-flex items-center gap-2 text-sm text-blue-600 underline">Download attachment</a>
              </div>
            </div>
          </div>

          <div v-if="!chats.length" class="text-center text-gray-400 py-6">No messages yet — say hi 👋</div>
        </div>
        <form @submit.prevent="sendMessage" class="p-3 border-t flex items-center gap-2 bg-white">
          <label class="relative flex-1">
            <input v-model="newMessage" :disabled="minimized" type="text" placeholder="Write a message..."
              class="w-full border rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
          </label>
          <input ref="fileInput" type="file" @change="onFileChange" class="hidden" />
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700">Send</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

export default {
  name: 'TicketChatPage',
  setup() {
    const route = useRoute();
    const router = useRouter();

    const ticket = ref({});
    const comments = ref([]);
    const chats = ref([]);
    const newComment = ref('');
    const newMessage = ref('');
    const fileToSend = ref(null);
    const newStatus = ref('');
    const user = ref(JSON.parse(localStorage.getItem('user') || '{}'));
    const statuses = ['open', 'in_progress', 'resolved', 'closed'];
    const minimized = ref(false);
    const showChat = ref(false);

    const messagesWrap = ref(null);
    const commentsScroll = ref(null);

    const fetchData = async () => {
      try {
        const ticketRes = await axios.get(`/api/tickets/${route.params.id}`);
        ticket.value = ticketRes.data;
        newStatus.value = ticket.value.status;

        const commentsRes = await axios.get(`/api/tickets/${route.params.id}/comments`);
        comments.value = commentsRes.data;

        const chatsResponse = await axios.get(`/api/tickets/${route.params.id}/chats`);
        chats.value = chatsResponse.data;

        await nextTick();
        scrollMessagesToBottom();
      } catch (err) {
        console.error('fetchData error', err);
        router.push('/tickets');
      }
    };

    onMounted(() => {
      fetchData();
      if (window.Echo) {
        window.Echo.channel(`ticket.${route.params.id}`)
          .listen('TicketChatSent', (e) => {
            chats.value.push(e.chat);
            nextTick(() => scrollMessagesToBottom());
          });
      }
      const pollInterval = setInterval(fetchData, 5000);
      onUnmounted(() => clearInterval(pollInterval));
    });

    onUnmounted(() => {
      if (window.Echo) {
        window.Echo.leave(`ticket.${route.params.id}`);
      }
    });

    const addComment = async () => {
      if (!newComment.value) return;
      try {
        const res = await axios.post(`/api/tickets/${route.params.id}/comments`, { comment: newComment.value });
        comments.value.push(res.data);
        newComment.value = '';
        nextTick(() => commentsScroll.value?.scrollTo({ top: commentsScroll.value.scrollHeight, behavior: 'smooth' }));
      } catch (err) {
        console.error('addComment', err);
      }
    };

    const sendMessage = async () => {
      if (!newMessage.value && !fileToSend.value) return;
      try {
        const form = new FormData();
        form.append('message', newMessage.value);
        if (fileToSend.value) form.append('file', fileToSend.value);

        await axios.post(`/api/tickets/${route.params.id}/chats`, form, { headers: { 'Content-Type': 'multipart/form-data' } });
        newMessage.value = '';
        fileToSend.value = null;
        if (document.querySelector('input[type=file]')) document.querySelector('input[type=file]').value = '';
      } catch (err) {
        console.error('sendMessage', err);
      }
    };

    const onFileChange = (e) => {
      const f = e.target.files?.[0];
      if (f) fileToSend.value = f;
    };

    const updateStatus = async () => {
      if (user.value.role !== 'admin') return;
      try {
        await axios.put(`/api/tickets/${route.params.id}/status`, { status: newStatus.value });
        ticket.value.status = newStatus.value;
      } catch (err) {
        console.error('updateStatus', err);
      }
    };

    const formatDate = (d) => {
      if (!d) return '';
      try {
        const date = new Date(d);
        return new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }).format(date);
      } catch (e) { return d; }
    };

    const toggleMinimize = () => { minimized.value = !minimized.value; };
    const toggleChat = () => {
      showChat.value = !showChat.value;
      if (showChat.value) {
        nextTick(() => scrollMessagesToBottom());
      }
    };

    const initials = (name = '') => name.split(' ').map(s => s[0]).slice(0, 2).join('').toUpperCase();

    const scrollMessagesToBottom = () => {
      if (!messagesWrap.value) return;
      messagesWrap.value.scrollTop = messagesWrap.value.scrollHeight + 200;
    };

    const refreshAll = () => fetchData();

    return {
      ticket, comments, chats, newComment, newMessage, newStatus, statuses, user, minimized, showChat,
      addComment, sendMessage, onFileChange, updateStatus, formatDate, initials, messagesWrap, commentsScroll, toggleMinimize, toggleChat, refreshAll
    };
  }
};
</script>

<style scoped>
.messages-scroll,
.comments-scroll {
  scrollbar-width: thin;
  scrollbar-color: rgba(0, 0, 0, 0.15) transparent;
}

.messages-scroll::-webkit-scrollbar,
.comments-scroll::-webkit-scrollbar {
  height: 8px;
  width: 8px;
}

.messages-scroll::-webkit-scrollbar-thumb,
.comments-scroll::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.12);
  border-radius: 999px;
}

@media (max-width: 1024px) {
  aside {
    order: 2;
  }
}

.fixed {
  transition: all 0.3s ease;
}

.transform {
  transition-property: transform, opacity;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}
</style>

<template>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3>Create Ticket</h3>
                    <form @submit.prevent="createTicket">
                        <div class="mb-3">
                            <input v-model="form.subject" type="text" class="form-control" placeholder="Subject"
                                required>
                        </div>
                        <div class="mb-3">
                            <textarea v-model="form.description" class="form-control" placeholder="Description"
                                required></textarea>
                        </div>
                        <!-- Category -->
                        <div class="mb-3">
                            <select v-model="form.category" class="form-control" required>
                                <option disabled value="">Select Category</option>
                                <option value="general">General</option>
                                <option value="technical">Technical</option>
                                <option value="billing">Billing</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <select v-model="form.priority" class="form-control" required>
                                <option disabled value="">Select Priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <!-- attachment -->
                        <div class="mb-3">
                            <input type="file" class="form-control" @change="e => form.attachment = e.target.files[0]">
                        </div>
                        <button type="submit" class="btn btn-primary">Create Ticket</button>
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
        const form = ref({ subject: '', description: '', category: '', priority: '', attachment: null });
        const error = ref('');
        const router = useRouter();

        const createTicket = async () => {
            error.value = '';
            const formData = new FormData();
            for (const key in form.value) {
                formData.append(key, form.value[key]);
            }
            try {
                const res = await axios.post('/api/tickets', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                router.push({ name: 'TicketDetail', params: { id: res.data.id } });
            } catch (err) {
                error.value = err.response?.data?.message || 'Failed to create ticket.';
            }
        };

        return { form, error, createTicket };
    }
};
</script>
<template>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3>Update Ticket</h3>
                    <!-- Add error alert -->
                    <div v-if="error" class="alert alert-danger">
                        {{ error }}
                    </div>
                    <form @submit.prevent="updateTicket">
                        <!-- Add loading state to form -->
                        <fieldset :disabled="loading">
                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <input type="text" class="form-control" v-model="ticket.subject" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" v-model="ticket.description" rows="4" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" v-model="ticket.category">
                                    <option value="general">General</option>
                                    <option value="technical">Technical</option>
                                    <option value="billing">Billing</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-select" v-model="ticket.priority">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" :disabled="loading">
                                {{ loading ? 'Updating...' : 'Update Ticket' }}
                            </button>
                            <button type="button" @click="$router.push('/tickets')"
                                class="btn btn-secondary ms-2" :disabled="loading">Cancel</button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';

export default {
    setup() {
        const ticket = ref({
            subject: '',
            description: '',
            priority: 'low',
            category: 'general'
        });
        const loading = ref(false);
        const error = ref('');
        const router = useRouter();
        const route = useRoute();

        onMounted(async () => {
            loading.value = true;
            try {
                const response = await axios.get(`/api/tickets/${route.params.id}`);
                ticket.value = response.data;
            } catch (err) {
                error.value = 'Failed to load ticket';
                console.error('Error fetching ticket:', err);
            } finally {
                loading.value = false;
            }
        });

        const updateTicket = async () => {
            loading.value = true;
            error.value = '';
            try {
                await axios.put(`/api/tickets/${route.params.id}`, ticket.value);
                router.push('/tickets');
            } catch (err) {
                error.value = 'Failed to update ticket';
                console.error('Error updating ticket:', err);
            } finally {
                loading.value = false;
            }
        };

        return {
            ticket,
            updateTicket,
            loading,
            error
        };
    }
};
</script>
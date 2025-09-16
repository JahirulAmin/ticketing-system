import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import Register from '../components/Register.vue';
import TicketList from '../components/TicketList.vue';
import TicketDetail from '../components/TicketDetail.vue';
import CreateTicket from '../components/CreateTicket.vue';

import { inject } from 'vue';
import EditTicket from '../components/EditTicket.vue';

const routes = [
  { path: '/', redirect: '/tickets' },
  { path: '/login', component: Login, meta: { requiresAuth: false } },
  { path: '/register', component: Register, meta: { requiresAuth: false } },
  { path: '/tickets', component: TicketList, meta: { requiresAuth: true } },
  { path: '/tickets/:id', component: TicketDetail, meta: { requiresAuth: true } },
  { path: '/tickets/create', component: () => CreateTicket, meta: { requiresAuth: true } },
  { path: '/tickets/:id/edit', component: () => EditTicket, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const app = document.getElementById('app')?.__vue_app__;
  if (!app) {
    console.log('App not mounted, delaying navigation');
    return next(false); // Delay until app mounts
  }

  const fetchUser = inject('fetchUser');
  const user = inject('user');
  if (!fetchUser || !user) {
    console.error('Dependency injection failed');
    return next('/login');
  }

  // Fetch user if not already fetched
  if (!user.value) {
    await fetchUser();
  }

  console.log('Router guard user check:', user.value);
  
  if (user.value && (to.path === '/login' || to.path === '/register')) {
    return next('/tickets');
  }

  // Check auth requirement for other routes
  if (to.meta.requiresAuth && !user.value) {
    next('/login');
  } else {
    next();
  }
});

export default router;
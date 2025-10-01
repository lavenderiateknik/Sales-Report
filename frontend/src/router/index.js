import { createRouter, createWebHistory } from 'vue-router'

import LoginView from '../views/LoginView.vue'
import CheckinView from '@/views/CheckinView.vue'
import SalesDashboardView from '../views/SalesDashboardView.vue'
import CustomerDashboardView from '@/views/CustomerDashboardView.vue'
import SpvRekapView from '@/views/SpvRekapView.vue'
import DashboardView from '@/views/DashboardView.vue'
import UserView from '@/views/UserView.vue'
import BranchesView from '@/views/BranchesView.vue'



const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: DashboardView,
    },
    {
      path: '/sales',
      name: 'sales',
      component: SalesDashboardView,
    },
    {
      path: '/customer',
      name: 'customer',
      component: CustomerDashboardView,
    },
    {
      path: '/spvdashboard',
      name: 'spv dashboard',
      component: SpvRekapView,
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/login',
      name: 'Login',
      component: LoginView,
    },
    {
      path: '/checkin',
      name: 'Checkin',
      component: CheckinView,
    },
    {
      path: '/adduser',
      name: 'Add User',
      component: UserView,
    },
    {
      path: '/branches',
      name: 'Branches',
      component: BranchesView,
    }
  ],
})


router.beforeEach((to, from, next) => {
  const isAuth = localStorage.getItem('api_token'); // Menggunakan token, bukan boolean

  if (to.name !== "Login" && !isAuth) {
    next({ name: 'Login' });
  } else if (to.name === "Login" && isAuth) {
    next({ name: 'home' });
  } else {
    next();
  }
});
export default router

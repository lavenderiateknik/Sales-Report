import { createRouter, createWebHistory } from 'vue-router'

import LoginView from '../views/LoginView.vue'
import CheckinView from '@/views/CheckinView.vue'
import SalesDashboardView from '../views/SalesDashboardView.vue'
import CustomerDashboardView from '@/views/CustomerDashboardView.vue'
import SpvRekapView from '@/views/SpvRekapView.vue'



const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
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
  ],
})


router.beforeEach((to, from, next) => {
  const isAuth = JSON.parse(localStorage.getItem('auth'));
  
  if (to.name !== "Login" && !isAuth) next({ name: 'Login' });
  if (to.name == "Login" && isAuth) next({ name: 'home' });
  else next();
})
export default router

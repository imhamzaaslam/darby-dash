import { createRouter, createWebHistory } from 'vue-router'
import Login from '../js/pages/login.vue'
import ForgotPassword from '../js/pages/forgot-password.vue'
import ResetPassword from '../js/pages/reset-password.vue'

const routes = [
  {
    path: '/login',
    name: 'login',
    component: Login,
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: ForgotPassword,
  },
  {
    path: '/reset-password',
    name: 'reset-password',
    component: ResetPassword,
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router

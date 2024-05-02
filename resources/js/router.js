import { createRouter, createWebHistory } from 'vue-router'
import Login from '../js/pages/login.vue'

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router

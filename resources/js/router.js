import { createRouter, createWebHistory } from 'vue-router'
import Dummy from './views/pages/Dummy.vue' // Import your component

const routes = [
    {
        path: '/',
        name: 'dummy',
        meta: { mustBeAuthorized: false },
        component: Dummy,
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router

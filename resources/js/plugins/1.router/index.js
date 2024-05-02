import { setupLayouts } from 'virtual:generated-layouts'
import { createRouter, createWebHistory } from 'vue-router/auto'
import { useAuthStore } from '@/store/auth'
import adminAuthorizedPages from './adminAuthorizedPages'
import WebDesign from '../../../js/pages/projects/web-design.vue'
import Seo from '../../../js/pages/projects/seo.vue'
import GoogleAds from '../../../js/pages/projects/google-ads.vue'

function recursiveLayouts(route) {
  if (route.children) {
    for (let i = 0; i < route.children.length; i++)
      route.children[i] = recursiveLayouts(route.children[i])

    return route
  }

  return setupLayouts([route])[0]
}

const requireAuth = (to, from, next) => {
  const authStore = useAuthStore()

  if (!authStore.isAuthenticated) {
    return next({ name: 'login' })
  }

  return next()
}

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to) {
    if (to.hash)
      return { el: to.hash, behavior: 'smooth', top: 60 }

    return { top: 0 }
  },
  extendRoutes: pages => [
    recursiveLayouts(
      {
        path: '/projects/web-design',
        name: 'web-design',
        component: WebDesign,
        meta: { layout: 'default' },
      },
    ),
    recursiveLayouts(
      {
        path: '/projects/seo',
        name: 'seo',
        component: Seo,
        meta: { layout: 'default' },
      },
    ),
    recursiveLayouts(
      {
        path: '/projects/google-ads',
        name: 'google-ads',
        component: GoogleAds,
        meta: { layout: 'default' },
      },
    ),
    ...pages.map(route => recursiveLayouts(route)),
  ],
})

router.beforeEach((to, from, next) => {
  if (adminAuthorizedPages.includes(to.name)) {
    return requireAuth(to, from, next)
  }

  const user = localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null

  if (to.name == 'login' && user) {
    return next({ name: 'root' })
  }

  return next()
})

export { router }
export default function (app) {
  app.use(router)
}

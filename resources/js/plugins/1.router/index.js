import { setupLayouts } from 'virtual:generated-layouts'
import { createRouter, createWebHistory } from 'vue-router/auto'
import { useAuthStore } from '@/store/auth'
import authenticatedPages from './authenticatedPages'
import adminAuthorizedPages from './adminAuthorizedPages'
import projectManagerAuthorizedPages from './projectManagerAuthorizedPages'
import staffAuthorizedPages from './staffAuthorizedPages'
import clientAuthorizedPages from './clientAuthorizedPages'
import WebDesignsList from '@/pages/projects/web-designs/index.vue'
import WebDesign from '@/pages/projects/web-designs/_partials/id.vue'
import Team from '@/pages/projects/team.vue'
import MileStones from '@/pages/projects/milestones.vue'
import TeamList from '@/pages/teams/index.vue'
import Calendar from '@/pages/projects/calendar.vue'
import Files from '@/pages/projects/files.vue'
import Chat from '@/pages/projects/chat.vue'
import Payments from '@/pages/projects/payments.vue'
import AddProjectTasks from '@/pages/projects/web-designs/_partials/addProjectTasks.vue'
import AuthorizationError from '@/pages/errors/authorization-error.vue'
import Roles from '@/pages/roles/index.vue'
import BucksSetting from '@/pages/projects/bucks.vue'
import NotificationList from '@/views/pages/received-notifications-list.vue'
import Templates from '@/pages/settings/templates/index.vue'
import ManageTemplates from '@/pages/settings/templates/_partials/manage-templates.vue'

import TwoFactorAuth from '@/pages/TwoFactorAuth.vue'

const requireAuth = () => {
  const authStore = useAuthStore()

  return authStore.isAuthenticated
}

const isAuthorized = to => {
  const authStore = useAuthStore()

  return (authStore.isAdmin && adminAuthorizedPages.includes(to.name)) ||
         (authStore.isManager && projectManagerAuthorizedPages.includes(to.name)) ||
         (authStore.isStaff && staffAuthorizedPages.includes(to.name)) ||
         (authStore.isClient && clientAuthorizedPages.includes(to.name))
}

function recursiveLayouts(route) {
  if (route.children) {
    for (let i = 0; i < route.children.length; i++)
      route.children[i] = recursiveLayouts(route.children[i])

    return route
  }

  return setupLayouts([route])[0]
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
        path: '/projects/web-designs',
        name: 'web-designs-list',
        component: WebDesignsList,
        meta: { layout: 'default' },
      },
    ),
    recursiveLayouts(
      {
        path: '/projects/web-designs/:id',
        name: 'web-design',
        component: WebDesign,
        meta: { layout: 'default' },
        props: true,
      },
    ),
    recursiveLayouts(
      {
        path: '/projects/:id/team',
        name: 'team',
        component: Team,
        meta: { layout: 'default' },
        props: true,
      },
    ),
    recursiveLayouts(
      {
        path: '/projects/:id/milestones',
        name: 'milestones',
        component: MileStones,
        meta: { layout: 'default' },
        props: true,
      },
    ),
    recursiveLayouts(
      {
        path: '/projects/:id/calendar',
        name: 'calendar',
        component: Calendar,
        meta: { layout: 'default' },
        props: true,
      },
    ),
    recursiveLayouts(
      {
        path: '/projects/:id/files',
        name: 'files',
        component: Files,
        meta: { layout: 'default' },
        props: true,
      },
    ),
    recursiveLayouts(
      {
        path: '/projects/:id/chat',
        name: 'chat',
        component: Chat,
        meta: { layout: 'default' },
        props: true,
      },
    ),
    recursiveLayouts(
      {
        path: '/projects/:id/tasks/add',
        name: 'add-project-tasks',
        component: AddProjectTasks,
        meta: { layout: 'default' },
        props: true,
      },
    ),
    recursiveLayouts(
      {
        path: '/projects/:id/payments',
        name: 'payment-setting',
        component: Payments,
        meta: { layout: 'default' },
        props: true,
      },
    ),
    recursiveLayouts(
      {
        path: '/members',
        name: 'members-list',
        component: TeamList,
        meta: { layout: 'default' },
      },
    ),
    recursiveLayouts(
      {
        path: '/roles',
        name: 'roles-setting',
        component: Roles,
        meta: { layout: 'default' },
      },
    ),
    recursiveLayouts(
      {
        path: '/projects/:id/bucks',
        name: 'bucks-setting',
        component: BucksSetting,
        meta: { layout: 'default' },
        props: true,
      },
    ),
    recursiveLayouts(
      {
        path: '/view/received/notifications',
        name: 'view-received-notifications',
        component: NotificationList,
        meta: { layout: 'default' },
        props: true,
      },
    ),
    recursiveLayouts(
      {
        path: '/settings/manage-templates',
        name: 'templates-setting',
        component: Templates,
        meta: { layout: 'default' },
        props: true,
      },
    ),
    recursiveLayouts(
      {
        path: '/settings/manage-templates/:id',
        name: 'manage-templates',
        component: ManageTemplates,
        meta: { layout: 'default' },
        props: true,
      },
    ),
    recursiveLayouts(
      {
        path: '/403',
        name: 'authorization-error',
        component: AuthorizationError,
        meta: { layout: 'default' },
      },
    ),
    ...pages.map(route => recursiveLayouts(route)),
    recursiveLayouts(
      {
        path: '/two-factor-auth/:email',
        name: 'two-factor-auth',
        component: TwoFactorAuth,
        meta: { layout: 'blank' },
      },
    ),
  ],
})

router.beforeEach((to, from, next) => {
  if (authenticatedPages.includes(to.name)) {
    if (!requireAuth()) {
      return next({ name: 'login' })
    }

    if (!isAuthorized(to)) {
      return next({ name: 'root' })
    }
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

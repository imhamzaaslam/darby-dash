import { useAuthStore } from '@/store/auth'

const isAdmin = useAuthStore().isAdmin

const routes = [
  {
    title: 'Home',
    to: { name: 'root' },
    icon: { icon: 'tabler-align-box-bottom-center' },
  },
  {
    title: 'Projects',
    icon: { icon: 'tabler-chart-histogram' },
    to: { name: 'web-designs-list' },
  },
  isAdmin && {
    title: 'Members',
    icon: { icon: 'tabler-users' },
    to: { name: 'members-list' },
  },
  
  // {
  //   title: 'Roles',
  //   icon: { icon: 'tabler-shield-check' },
  //   to: { name: 'roles' },
  // },
  {
    title: 'Logout',
    icon: { icon: 'tabler-logout' },
    to: { name: 'logout' },
  },
]

// Filter out any `false` values in the array
const filteredRoutes = routes.filter(Boolean)

export default filteredRoutes

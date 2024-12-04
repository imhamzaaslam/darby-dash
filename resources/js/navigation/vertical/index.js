import { useAuthStore } from '@/store/auth'

const isAdmin = useAuthStore().isAdmin
const isSuperAdmin = useAuthStore().isSuperAdmin
const companyUuid = useAuthStore().getCompanyUuid

const routes = [
  {
    title: 'Home',
    to: { name: 'root' },
    icon: { icon: 'tabler-align-box-bottom-center' },
  },
  isSuperAdmin && {
    title: 'Companies',
    icon: { icon: 'tabler-users' },
    to: { name: 'companies-list' },
  },
  !isSuperAdmin &&{
    title: 'Projects',
    icon: { icon: 'tabler-chart-histogram' },
    to: { name: 'web-designs-list' },
  },
  isAdmin && {
    title: 'Members',
    icon: { icon: 'tabler-users' },
    to: { name: 'members-list' },
  },
  isAdmin &&
  {
    title: 'Settings',
    icon: { icon: 'tabler-settings' },
    children: [
      companyUuid && {
        title: 'Company Setting',
        to: { name: 'manage-company-settings', params: { id: companyUuid } },
      },
      { title: 'Manage Roles', to: 'roles-setting' },
      { title: 'Manage Templates', to: 'templates-setting' },
      { title: 'Manage Services', to: 'services-setting' },
    ],
  },
]

const filteredRoutes = routes.filter(Boolean)

export default filteredRoutes

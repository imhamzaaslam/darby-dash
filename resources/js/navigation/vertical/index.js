import { useAuthStore } from '@/store/auth'

const authStore = useAuthStore()
const isAdmin = authStore.isAdmin
const isSuperAdmin = authStore.isSuperAdmin
const companyUuid = authStore.getCompanyUuid
const isTenant = authStore.isTenant ?? false

const settingsChildren = [
  companyUuid && isTenant && {
    title: 'Company Setting',
    to: { name: 'manage-company-settings', params: { id: companyUuid } },
  },
  { title: 'Manage Roles', to: 'roles-setting' },
  { title: 'Manage Templates', to: 'templates-setting' },
  { title: 'Manage Services', to: 'services-setting' },
].filter(Boolean)

const routes = [
  {
    title: 'Home',
    to: { name: 'root' },
    icon: { icon: 'tabler-align-box-bottom-center' },
  },

  !isSuperAdmin && {
    title: 'Projects',
    icon: { icon: 'tabler-chart-histogram' },
    to: { name: 'web-designs-list' },
  },

  isAdmin && {
    title: 'Members',
    icon: { icon: 'tabler-users' },
    to: { name: 'members-list' },
  },

  isAdmin && {
    title: 'Settings',
    icon: { icon: 'tabler-settings' },
    children: settingsChildren,
  },
].filter(Boolean)

export default routes

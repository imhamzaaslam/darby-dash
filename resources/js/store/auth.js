import router from '../router'
import { defineStore } from 'pinia'
import AuthService from '../services/AuthService'

const getUserFromLocalStorage = () => localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null

export const useAuthStore = defineStore('auth', {
  state: () => ({
    // initialize state from local storage to enable user to stay logged in
    user: getUserFromLocalStorage(),
    tenant: false,
    logo: null,
    favicon: null,
    title: null,
    generalSetting: [],
    authUser: null,
    authenticated: false,
    token: null,
    loginUserPermissions: [],
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async login(email, password) {
      this.authenticated = false
      this.error = null
      this.loadStatus = 1
      try {
        let res = await AuthService.login({ email, password })
        this.loadStatus = 2
        this.authenticated = true
        this.token = res.data.accessToken

        this.authUser = res.data

        this.loginUserPermissions = res.data.permissions

        return res
      } catch (error) {
        this.error = error.response
        this.loadStatus = 3
        this.authenticated = false

        return error.response
      }
    },
    async tenantInfo() {
      this.error = null
      this.loadStatus = 1
      try {
        let res = await AuthService.tenantInfo()
        this.loadStatus = 2
        if(res.data.status)
        {
          this.tenant = res.data.isTenant
          this.logo = this.getImageUrl(res.data.logo)
          this.favicon = this.getImageUrl(res.data.favicon)
          this.title = res.data.title
          this.generalSetting = res.data.general_setting
        }
      } catch (error) {
        this.error = error.response
        this.loadStatus = 3
      }
    },
    async logout() {
      this.authenticated = false
      this.error = null
      this.loadStatus = 0
      localStorage.removeItem('user')

      return await AuthService.logout(this.token).then(() => {
        this.token=null
        window.location.href = '/'
      }).catch(error => {
        console.error('Error during logout:', error)
        this.token=null
        window.location.href = '/'
      })
    },
    async getAuthUser()  {
      this.error = null
      this.loadStatus = 1

      try {
        const response = await AuthService.getAuthUser()
        const user = response.data.data

        localStorage.setItem('user', JSON.stringify(user))

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getUser error ', error)
      }
    },
    getImageUrl (path) {
      const baseUrl = import.meta.env.VITE_APP_URL
    
      return `${baseUrl}storage/${path}`
    },
    async verifyTwoFactorCode(email, code) {
      this.error = null
      this.loadStatus = 1
      try {
        const res = await AuthService.verify2FACode(email, code)
        if(res.data.success)
        {
          this.authenticated = true
          this.token = res.data.accessToken

          this.authUser = res.data

          this.loginUserPermissions = res.data.permissions
        }

        this.loadStatus = 2

        return res
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('verify 2fa code error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getUser: state => state.user,
    isAuthenticated: state => state.authenticated,
    isTenant: state => state.tenant,
    getLogo: state => state.logo,
    getCompanyUuid: state => state.authUser?.user?.company?.uuid,
    getFavicon: state => state.favicon,
    getTitle: state => state.title,
    getGeneralSetting: state => state.generalSetting,
    getErrors: state => state.error,
    isSuperAdmin: state => state.authUser?.user?.roles[0].name == 'Super Admin',
    isAdmin: state => state.authUser?.user?.roles[0].name == 'Admin',
    isManager: state => state.authUser?.user?.roles[0].name == 'Project Manager',
    isStaff: state => state.authUser?.user?.roles[0].name == 'Staff User',
    isClient: state => state.authUser?.user?.roles[0].name == 'Client User',
    getRole: state => state.authUser?.user?.roles[0],
    getUserFromLocalStorage: () => getUserFromLocalStorage(),
    hasPermission: state => permission => {
      return state.loginUserPermissions.includes(permission)
    },
  },
})

import router from '../router'
import AuthService from '../services/AuthService'

const getUserFromLocalStorage = () => localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null

export const useAuthStore = defineStore('auth', {
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        user: getUserFromLocalStorage(),
        authenticated: false,
        loadStatus: 0,
        error: null
    }),
    persist: true,
    actions: {
        async login(email, password) {
            this.authenticated = false
            this.error = null
            this.loadStatus = 1

            try {
                let res = await AuthService.login({email, password})
                this.loadStatus = 2
                this.authenticated = true
                return res
            } catch (error) {
                this.error = error.response
                this.loadStatus = 3
                this.authenticated = false
                return error.response
            }
        },
        async logout() {
            this.user = null
            this.authenticated = false
            this.error = null
            this.loadStatus = 0

            localStorage.removeItem('user')
            return await AuthService.logout();
        },
        async getAuthUser()  {
            this.error = null
            this.loadStatus = 1

            try {
                const response = await AuthService.getAuthUser()
                const user = response.data.data
                this.user = user
                localStorage.setItem('user', JSON.stringify(user))

                this.loadStatus = 2
            } catch (error) {
                this.error = error
                this.loadStatus = 3
                console.error('getUser error ', error)
            }
        },
    },
    getters: {
        getLoadStatus: state => state.loadStatus,
        getUser: state => state.user,
        isAuthenticated: state => state.authenticated,
        getErrors: state => state.error,
        isAdmin: state => state.user?.roles?.indexOf('admin') >= 0,
        isCustomer: state => state.user?.roles?.indexOf('customer') >= 0,
        getRole: state => state.user?.roles[0],
    }
})

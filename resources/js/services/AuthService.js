import axios from "axios"
import { useAuthStore } from "../store/auth"

const authClient = axios.create({
  baseURL: import.meta.env.VITE_APP_URL,
  withCredentials: true, // required to handle the CSRF token
})

/*
 * Add a response interceptor
 */
authClient.interceptors.response.use(
  response => {
    return response
  },
  function (error) {
    const authStore = useAuthStore()

    // If it's a bad response from the user or the session is no longer active,
    // we log out the user.
    const user = localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null

    if (error.response && [401, 419].includes(error.response.status) && user) {
      authStore.logout()
    }

    return Promise.reject(error)
  },
)

authClient.interceptors.request.use(config => {
  const authStore = useAuthStore()
  const token = authStore.token
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

const getCookie = () => {
  console.log('AuthService: sending cookie request')

  return authClient.get('/sanctum/csrf-cookie')
}

export default {
  login: async payload => {
    console.log('AuthService: about to start getCookie()')
    await getCookie()

    return await authClient.post('/api/auth/login', payload)
  },

  tenantInfo: async () => {
    return await authClient.get('/api/auth/check-tenant')
  },

  logout: async token => {
    return await authClient.post('/api/auth/logout', {}, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
  },

  forgotPassword: async email => {
    await getCookie()

    return await authClient.post('/api/forgot-password', {
      'email': email.email,
      'frontendUrl': window.location.origin,
    })
  },

  getAuthUser: () => {
    console.log('AuthService: getting user')

    return authClient.get('/api/v1/me')
  },

  resetPassword: async payload => {
    await getCookie()

    return await authClient.post('/api/reset-password', payload)
  },

  updatePassword: payload => {
    return authClient.put('/user/password', payload)
  },

  registerUser: payload => {
    getCookie().then(() => {
      return authClient.post('/register', payload)
    })
  },

  sendVerification: payload => {
    return authClient.post('/email/verification-notification', payload)
  },

  verify2FACode: (email, code) => {
    return authClient.post('/api/auth/verify-2fa-code', { email, code })
  },

  updateUser: (uuid, payload) => {
    return authClient.patch(`/api/v1/users/${uuid}`, payload)
  },

  validateResetToken: (email, token) => {
    return authClient.post('/api/v1/token/validate', { email, token })
  },
}

import axios from "axios";
import { useAuthStore } from "../store/auth"

const authClient = axios.create({
    baseURL: import.meta.env.VITE_APP_URL,
    withCredentials: true, // required to handle the CSRF token
})

/*
 * Add a response interceptor
 */
authClient.interceptors.response.use(
    (response) => {
        return response;
    },
    function (error) {
        const authStore = useAuthStore()

        // If it's a bad response from the user or the session is no longer active,
        // we log out the user.
        if (error.response && [401, 419].includes(error.response.status) && authStore.user) {
            authStore.logout()
        }
        return Promise.reject(error)
    }
);

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

    logout: () => {
        return authClient.post('/api/auth/logout')
    },

    forgotPassword: async email => {
        await getCookie()
        return await authClient.post('/forgot-password', email)
    },

    getAuthUser: () => {
        console.log('AuthService: getting user')
        return authClient.get('/api/v1/me')
    },

    resetPassword: async payload => {
        await getCookie()
        return await authClient.post('/reset-password', payload)
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

    updateUser: (uuid, payload) => {
        return authClient.patch(`/api/v1/users/${uuid}`, payload)
    },

    validateResetToken: (email, token) => {
        return authClient.post('/api/v1/token/validate', { email, token })
    },
}
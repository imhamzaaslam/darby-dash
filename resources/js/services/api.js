/*
 * This is the initial API interface
 * we set the base URL for the API.
 * This will be used for making authenticated requests.
 */

import axios from "axios"
import { useAuthStore } from "../store/auth"

export const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  withCredentials: true, // required to handle the CSRF token
})

export const generateQueryString = (search, orderBy, orderDirection) => {
  const queryParams = []

  if (search) {
    queryParams.push(`keyword=${search}`)
  }
  if (orderBy) {
    queryParams.push(`orderBy=${orderBy}`)
  }
  if (orderDirection) {
    queryParams.push(`order=${orderDirection}`)
  }

  return queryParams.length > 0 ? `&${queryParams.join('&')}` : ''
}

/*
 * Add a response interceptor
 */
apiClient.interceptors.response.use(
  response => {
    return response
  },
  function (error) {
    const store = useAuthStore()

    if (error.response && [401, 419].includes(error.response.status) && store.user) {
      store.logout()
    }

    return Promise.reject(error)
  },
)

apiClient.interceptors.request.use(config => {
  const store = useAuthStore()
  const token = store.token

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

import { apiClient, generateQueryString } from './api'

export default {
  getRoles() {
    return apiClient.get('/roles')
  },
}

import { apiClient, generateQueryString } from './api'

export default {
  getProjectTypes() {
    return apiClient.get('/project-types')
  },
}

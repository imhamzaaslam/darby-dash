import { apiClient, generateQueryString } from './api'

export default {
  getTemplates() {
    return apiClient.get(`/admin/templates`)
  },

  createTemplate(projectId, payload) {
    return apiClient.post(`/admin/projects/${projectId}/templates`, payload)
  },
}

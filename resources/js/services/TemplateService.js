import { apiClient, generateQueryString } from './api'

export default {
  getTemplates() {
    return apiClient.get(`/admin/templates`)
  },

  getTemplatesWithPagination(page = 1, perPage = 10, search = null) {
    const baseUrl = `/admin/templates/pagination?page=${page}&per_page=${perPage}`

    const queryParams = [
      search && `keyword=${search}`,
    ].filter(Boolean)

    const queryString = queryParams.length ? `&${queryParams.join('&')}` : ''

    return apiClient.get(baseUrl + queryString)
  },

  createTemplate(projectId, payload) {
    return apiClient.post(`/admin/projects/${projectId}/templates`, payload)
  },

  deleteTemplate(id) {
    return apiClient.delete(`admin/templates/${id}`)
  },
}

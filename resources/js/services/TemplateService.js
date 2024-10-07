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

  getTemplate(id) {
    return apiClient.get(`admin/templates/${id}`)
  },

  createTemplate(projectId, payload) {
    return apiClient.post(`/admin/projects/${projectId}/templates`, payload)
  },

  deleteTemplate(id) {
    return apiClient.delete(`admin/templates/${id}`)
  },

  //Manage Template Lists
  createTemplateList(templateId, payload) {
    return apiClient.post(`/admin/templates/${templateId}/list`, payload)
  },

  updateTemplateList(listUuid, payload) {
    return apiClient.patch(`admin/templates/list/${listUuid}`, payload)
  },

  deleteTemplateList(listId) {
    return apiClient.delete(`admin/templates/list/${listId}`)
  },

  saveSortedOrder(templateId, payload) {
    return apiClient.patch(`admin/templates/${templateId}/lists/sort`, payload)
  },

  //Manage Tamplate Lists Tasks
  createTemplateListTask(listId, payload) {
    return apiClient.post(`/admin/templates/list/${listId}/task`, payload)
  },

  updateTemplateListTask(taskUuid, payload) {
    return apiClient.patch(`admin/templates/task/${taskUuid}`, payload)
  },

  deleteTemplateListTask(taskId) {
    return apiClient.delete(`admin/templates/task/${taskId}`)
  },
}

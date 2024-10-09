import { apiClient, generateQueryString } from './api'

export default {
  getNotificationSettings() {
    return apiClient.get(`admin/settings/notifications`)
  },
  updateNotificationSetting(settingId, payload) {
    return apiClient.patch(`admin/settings/notifications/${settingId}/update`, payload)
  },

  // Manage Services
  getAllProjectServices(page = 1, perPage = 10, projectTypeId = null) {
    const baseUrl = `/admin/services?page=${page}&per_page=${perPage}`

    const queryParams = [
      projectTypeId && `projectTypeId=${projectTypeId}`,
    ].filter(Boolean)

    const queryString = queryParams.length ? `&${queryParams.join('&')}` : ''

    return apiClient.get(baseUrl + queryString)
  },
  getProjectServicesWithoutPagination() {
    return apiClient.get(`/admin/services/without-pagination`)
  },
  getProjectServicesByType(typeId) {
    return apiClient.get(`/admin/services/type/${typeId}`)
  },
  createProjectService(payload) {
    return apiClient.post(`/admin/services`, payload)
  },
  showProjectService(serviceId) {
    return apiClient.get(`/admin/services/${serviceId}`)
  },
  updateProjectService(serviceId, payload) {
    return apiClient.post(`admin/services/${serviceId}`, payload)
  },
  deleteProjectService(serviceId) {
    return apiClient.delete(`admin/services/${serviceId}`)
  },
  sortServiceOrder(payload) {
    return apiClient.patch(`admin/services/sort`, payload)
  },
}

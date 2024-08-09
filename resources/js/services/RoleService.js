import { apiClient, generateQueryString } from './api'

export default {
  getRoles() {
    return apiClient.get('/roles')
  },
  getPermissions(roleId) {
    return apiClient.get(`/roles/${roleId}/permissions`)
  },
  updateRolePermissions(roleId, permissions) {
    return apiClient.patch(`/roles/${roleId}/permissions`, { permissions })
  },
}

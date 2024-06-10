import { apiClient, generateQueryString } from './api'

export default {
  getAll(projectUuid) {
    return apiClient.get(`admin/projects/${projectUuid}/folders`)
  },
  create(projectUuid, payload) {
    return apiClient.post(`admin/projects/${projectUuid}/folders`, payload)
  },
  update(folderUuid, payload) {
    return apiClient.patch(`admin/folders/${folderUuid}`, payload)
  },
  delete(folderUuid) {
    return apiClient.delete(`admin/folders/${folderUuid}`)
  },
}
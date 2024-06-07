import { apiClient, generateQueryString } from './api'

export default {
  getAll(projectUuid) {
    return apiClient.get(`admin/projects/${projectUuid}/milestones`)
  },
  create(projectUuid, payload) {
    return apiClient.post(`admin/projects/${projectUuid}/milestones`, payload)
  },
  update(milestoneUuid, payload) {
    return apiClient.patch(`admin/milestones/${milestoneUuid}`, payload)
  },
  delete(milestoneUuid) {
    return apiClient.delete(`admin/milestones/${milestoneUuid}`)
  },
}

import { apiClient, generateQueryString } from './api'

export default {
  getAll(
    page = 1,
    perPage = 10,
    projectUuid,
  ) {
    return apiClient.get(`/admin/projects/${projectUuid}/milestones?page=${page}&per_page=${perPage}`)
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

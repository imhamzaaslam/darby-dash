import { apiClient } from './api'

export default {
  getBucks(projectUuid) {
    return apiClient.get(`admin/projects/${projectUuid}/bucks`)
  },
  updateProjectBucks(projectUuid, payload) {
    return apiClient.patch(`admin/projects/${projectUuid}/bucks`, payload)
  },
  fetchBucksTasks(projectUuid) {
    return apiClient.get(`admin/projects/${projectUuid}/bucks/tasks`)
  },
  updateTaskApproval(projectUuid, taskId, payload) {
    return apiClient.patch(`admin/projects/${projectUuid}/bucks/tasks/${taskId}`, payload)
  },
}

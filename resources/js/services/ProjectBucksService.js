import { apiClient } from './api'

export default {
  getBucks(projectUuid) {
    return apiClient.get(`admin/projects/${projectUuid}/bucks`)
  },
  updateProjectBucks(projectUuid, payload) {
    return apiClient.patch(`admin/projects/${projectUuid}/bucks`, payload)
  },
}

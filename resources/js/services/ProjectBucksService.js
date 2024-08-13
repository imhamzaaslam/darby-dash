import { apiClient } from './api'

export default {
  getBucks(projectUuid) {
    return apiClient.get(`admin/projects/${projectUuid}/bucks`)
  },
}

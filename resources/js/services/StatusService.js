import { apiClient, generateQueryString } from './api'

export default {
  getStatuses() {
    return apiClient.get('admin/statuses')
  },
}

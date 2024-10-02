import { apiClient, generateQueryString } from './api'

export default {
  getNotificationSettings() {
    return apiClient.get(`admin/settings/notifications`)
  },
  updateNotificationSetting(settingId, payload) {
    return apiClient.patch(`admin/settings/notifications/${settingId}/update`, payload)
  },
}

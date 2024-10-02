import { apiClient, generateQueryString } from './api'

export default {
  getNotifications() {
    return apiClient.get('admin/notifications')
  },
  markAsReadNotification(payload) {
    return apiClient.post('admin/notifications/mark-as-read', payload)
  },
  markAsUnreadNotification(payload) {
    return apiClient.post('admin/notifications/mark-as-unread', payload)
  },
  deleteNotification(notificationId) {
    return apiClient.delete(`admin/notifications/${notificationId}`)
  },
}

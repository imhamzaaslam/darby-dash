import { defineStore } from 'pinia'
import NotificationService from '../services/NotificationService'

export const useNotificationStore = defineStore('notifications', {
  state: () => ({
    notifications: [],
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll() {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await NotificationService.getNotifications()

        this.notifications = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getNotifications error ', error)
      }
    },
    async markAsRead(payload) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await NotificationService.markAsReadNotification(payload)

        this.notifications = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('markAsReadNotification error ', error)
      }
    },

    async markAsUnread(payload) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await NotificationService.markAsUnreadNotification(payload)

        this.notifications = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('markAsUnreadNotification error ', error)
      }
    },

    async delete(notificationId) {
      this.error = null
      this.loadStatus = 1
      try {
        await NotificationService.deleteNotification(notificationId)
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteNotification error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getNotifications: state => state.notifications,
    getErrors: state => state.error,
  },
})

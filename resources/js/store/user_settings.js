import { defineStore } from 'pinia'
import UserSettingService from '../services/UserSettingService'

export const useUserSettingStore = defineStore('user_settings', {
  state: () => ({
    notificationSettings: [],
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAllNotifications() {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserSettingService.getNotificationSettings()

        this.notificationSettings = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getSettings error ', error)
      }
    },
    async updateNotificationSetting(settingId, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserSettingService.updateNotificationSetting(settingId, payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateNotificationSetting error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getNotificationSettings: state => state.notificationSettings,
    getErrors: state => state.error,
  },
})

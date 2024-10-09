import { defineStore } from 'pinia'
import UserSettingService from '../services/UserSettingService'

export const useUserSettingStore = defineStore('user_settings', {
  state: () => ({
    notificationSettings: [],
    projectServices: [],
    projectServicesByType: [],
    projectServicesWithoutPagination: [],
    projectService: null,
    totalServicesCount: 0,
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

    async getAllServices(page = 1, perPage = 10, projectTypeId = null) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserSettingService.getAllProjectServices(page, perPage, projectTypeId)

        this.projectServices = response.data.data
        this.totalServicesCount = response.data.meta.total
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getAllProjectServices error ', error)
      }
    },

    async getServicesWithoutPagination() {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserSettingService.getProjectServicesWithoutPagination()

        this.projectServicesWithoutPagination = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getProjectServicesWithoutPagination error ', error)
      }
    },

    async getServicesByType(projectTypeId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserSettingService.getProjectServicesByType(projectTypeId)

        this.projectServicesByType = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getAllProjectServices error ', error)
      }
    },

    async createService(payload) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserSettingService.createProjectService(payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createProjectService error ', error)
      }
    },

    async updateService(serviceId, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserSettingService.updateProjectService(serviceId, payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateProjectService error ', error)
      }
    },

    async deleteService(serviceId, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserSettingService.deleteProjectService(serviceId)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteProjectService error ', error)
      }
    },

    async showService(serviceId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserSettingService.showProjectService(serviceId)

        this.projectService = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('showProjectService error ', error)
      }
    },

    async saveSortedOrder(services) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserSettingService.sortServiceOrder(services)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('sortServiceOrder error ', error)
      }
    },

  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getNotificationSettings: state => state.notificationSettings,
    getProjectServices: state => state.projectServices,
    getProjectServicesByType: state => state.projectServicesByType,
    getProjectService: state => state.projectService,
    getProjectServicesWithoutPagination: state => state.projectServicesWithoutPagination,
    getErrors: state => state.error,
  },
})

import { defineStore } from 'pinia'
import ProjectBucksService from '../services/ProjectBucksService'

export const useProjectBucksStore = defineStore('project_bucks', {
  state: () => ({
    bucksDetails: [],
    bucksTasks: [],
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getBucks(projectUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectBucksService.getBucks(projectUuid)

        this.bucksDetails = response.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getProjects error ', error)
      }
    },
    async updateProjectBucks(projectUuid, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectBucksService.updateProjectBucks(projectUuid, payload)

        this.bucksDetails = response.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateProjectBucks error ', error)
        
        return error
      }
    },
    async fetchBucksTasks(projectUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectBucksService.fetchBucksTasks(projectUuid)

        this.bucksTasks = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('fetchBucksTasks error ', error)
      }
    },
    async updateTaskApproval(projectUuid, taskId, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await ProjectBucksService.updateTaskApproval(projectUuid, taskId, payload)
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateTaskApproval error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getBucksDetails: state => state.bucksDetails,
    getBucksTasks: state => state.bucksTasks,
    getErrors: state => state.error,
  },
})

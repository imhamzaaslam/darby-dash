import { defineStore } from 'pinia'
import ProjectBucksService from '../services/ProjectBucksService'

export const useProjectBucksStore = defineStore('project_bucks', {
  state: () => ({
    bucksDetails: [],
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
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getBucksDetails: state => state.bucksDetails,
    getErrors: state => state.error,
  },
})

import { defineStore } from 'pinia'
import ProjectTypeService from '../services/ProjectTypeService'

export const useProjectTypeStore = defineStore('project_types', {
  state: () => ({
    projectTypes: [],
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll() {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectTypeService.getProjectTypes()

        this.projectTypes = response.data.data.map(type => ({ id: type.id, name: type.name }))
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getProjectsTypes error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getProjectTypes: state => state.projectTypes,
    getErrors: state => state.error,
  },
})

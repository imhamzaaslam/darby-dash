import { defineStore } from 'pinia'
import ProjectService from '../services/ProjectService'

export const useProjectStore = defineStore('projects', {
  state: () => ({
    projects: [],
    project: null,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getProjects() {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectService.getProjects()

        this.projects = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getProjects error ', error)
      }
    },
    async getProject(id) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectService.getProject(id)

        this.project = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getProject error ', error)
      }
    },
    async createProject(project) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectService.createProject(project)

        this.project = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createProject error ', error)
      }
    },
    async updateProject(project) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectService.updateProject(project)

        this.project = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateProject error ', error)
      }
    },
    async deleteProject(id) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectService.deleteProject(id)

        this.project = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteProject error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getProjects: state => state.projects,
    getProject: state => state.project,
    getErrors: state => state.error,
  },
})
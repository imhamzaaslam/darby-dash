import { defineStore } from 'pinia'
import ProjectService from '../services/ProjectService'

export const useProjectStore = defineStore('projects', {
  state: () => ({
    projects: [],
    projectsByType: [],
    project: null,
    usersCount: 0,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(search = null, projectTypeId = null, projectManagerId = null) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectService.getProjects(search, projectTypeId, projectManagerId)

        this.projects = response.data.data
        this.usersCount = response.data.meta.total
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getProjects error ', error)
      }
    },
    async getByType(id) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectService.getProjectsByType(id)

        this.projectsByType = response.data.data
        this.usersCount = response.data.meta.total
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getProjectsByType error ', error)
      }
    },
    async show(id) {
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
    async create(project) {
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
    async update(project) {
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
    async delete(id) {
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
    getProjectsByType: state => state.projectsByType,
    getProject: state => state.project,
    getErrors: state => state.error,
  },
})

import { defineStore } from 'pinia'
import ProjectService from '../services/ProjectService'

export const useProjectStore = defineStore('projects', {
  state: () => ({
    projects: [],
    projectsByType: [],
    activities: [],
    project: null,
    progress: [],
    upcomingTasks: [],
    projectsCount: 0,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(page = 1, perPage = 10, search = null, projectTypeId = null, projectManagerId = null) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectService.getProjects(page, perPage, search, projectTypeId, projectManagerId)

        this.projects = response.data.data
        this.projectsCount = response.data.meta.total
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

        // this.usersCount = response.data.meta.total
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getProjectsByType error ', error)
      }
    },
    async getActivities(uuid) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectService.getProjectActivities(uuid)

        this.activities = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getProjectActivities error ', error)
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

    async complete(project) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectService.projectComplete(project)

        this.project = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('complete project error ', error)
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
    async getProgress(uuid) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectService.getProgress(uuid)

        this.progress = response.data.data
        this.upcomingTasks = response.data.upcoming_tasks
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getProgress error ', error)
      }
    },
    async updateMember(project) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectService.updateMember(project)

        this.project = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateMember error ', error)
      }
    },
    async deleteMember(uuid, userUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        await ProjectService.deleteMember(uuid, userUuid)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteMember error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getProjects: state => state.projects,
    getProjectActivities: state => state.activities,
    getProjectsByType: state => state.projectsByType,
    getProjectProgress: state => state.progress,
    getProjectUpcomingTasks: state => state.upcomingTasks,
    getProject: state => state.project,
    getErrors: state => state.error,
  },
})

import { defineStore } from 'pinia'
import ProjectTaskService from '../services/ProjectTaskService'

export const useProjectTaskStore = defineStore('project_tasks', {
  state: () => ({
    projectTasks: [],
    projectTask: null,
    projectTasksCount: 0,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(projectId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectTaskService.getProjectTasks(projectId)

        this.projectTasks = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getLists error ', error)
      }
    },
    async create(task) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectTaskService.createProjectTask(task)

        this.projectTask = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createProjectTask error ', error)
      }
    },
    async update(task) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectTaskService.updateProjectTask(task)

        this.projectTask = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateProjectTask error ', error)
      }
    },
    async delete(task) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectTaskService.deleteProjectTask(task)

        this.projectTask = response.data.data
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
    getProjectTasks: state => state.projectTasks,
    getProjectTask: state => state.projectTask,
    getErrors: state => state.error,
  },
})

import { defineStore } from 'pinia'
import ProjectTaskService from '../services/ProjectTaskService'

export const useProjectTaskStore = defineStore('project_tasks', {
  state: () => ({
    projectTasks: [],
    projectAllTasks: [],
    projectTask: null,
    projectDueTasks: [],
    projectTasksCount: 0,
    taskFiles: [],
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(projectId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectTaskService.getAll(projectId)

        this.projectAllTasks = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getLists error ', error)
      }
    },
    async getDueTasks(projectId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectTaskService.getDueTasks(projectId)

        this.projectDueTasks = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getDueTasks error ', error)
      }
    },
    async getUnlistedTasks(projectId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectTaskService.getUnlistedTasks(projectId)

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
    async fetchFiles(taskUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectTaskService.fetchFiles(taskUuid)

        this.taskFiles = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('fetchFiles error ', error)
      }
    },
    async uploadFiles(taskUuid, files) {
      this.error = null
      this.loadStatus = 1
      try {
        await ProjectTaskService.uploadFiles(taskUuid, files)
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('uploadFiles error ', error)
      }
    },
    async updateTaskOrdering(task) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectTaskService.updateProjectTasksOrder(task)

        this.projectTask = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateProjectTasksOrder error ', error)
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
    async updateAttributes(taskUuid, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectTaskService.updateAttributes(taskUuid, payload)

        this.projectTask = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateAttributes error ', error)
      }
    },
    async assignTask(taskUuid, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await ProjectTaskService.assignTask(taskUuid, payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('assignTask error ', error)
      }
    },
    async removeAssignee(taskUuid, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await ProjectTaskService.removeAssignee(taskUuid, payload)
  
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('removeAssignee error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getProjectTasks: state => state.projectTasks,
    getProjectAllTasks: state => state.projectAllTasks,
    getProjectTask: state => state.projectTask,
    getProjectDueTasks: state => state.projectDueTasks,
    getTaskFiles: state => state.taskFiles,
    getErrors: state => state.error,
  },
})

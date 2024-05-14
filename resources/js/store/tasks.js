import { defineStore } from 'pinia'
import TaskService from '../services/TaskService'

export const useTaskStore = defineStore('tasks', {
  state: () => ({
    tasks: [],
    task: null,
    tasksCount: 0,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(projectId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await TaskService.getProjectTasks(projectId)

        this.tasks = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getTasks error ', error)
      }
    },
    async create(task) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await TaskService.createProjectTask(task)

        this.task = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createTask error ', error)
      }
    },
    async update(task) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await TaskService.updateProjectTask(task)

        this.task = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateTask error ', error)
      }
    },
    async delete(task) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await TaskService.deleteProjectTask(task)

        this.task = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteTask error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getProjectTasks: state => state.tasks,
    getProjectTask: state => state.task,
    getErrors: state => state.error,
  },
})

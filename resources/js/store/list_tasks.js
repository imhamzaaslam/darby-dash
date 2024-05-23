import { defineStore } from 'pinia'
import ListTaskService from '../services/ListTaskService'

export const useListTaskStore = defineStore('lists', {
  state: () => ({
    listTasks: [],
    listTask: null,
    listTasksCount: 0,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(listId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ListTaskService.getListTasks(listId)

        this.listTasks = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getListTasks error ', error)
      }
    },
    async create(task) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ListTaskService.createListTask(task)

        this.listTask = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createListTask error ', error)
      }
    },
    async update(task) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ListTaskService.updateListTask(task)

        this.listTask = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateListTask error ', error)
      }
    },
    async delete(task) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ListTaskService.deleteListTask(task)

        this.listTask = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteListTask error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getListTasks: state => state.listTasks,
    getListTask: state => state.listTask,
    getErrors: state => state.error,
  },
})

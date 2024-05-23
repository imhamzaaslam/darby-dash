import { defineStore } from 'pinia'
import ListService from '../services/ListService'

export const useListStore = defineStore('lists', {
  state: () => ({
    lists: [],
    list: null,
    listsCount: 0,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(projectId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ListService.getProjectLists(projectId)

        this.lists = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getLists error ', error)
      }
    },
    async create(list) {
        console.log('here inside create list')
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ListService.createProjectList(list)

        this.list = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createList error ', error)
      }
    },
    async update(list) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ListService.updateProjectList(list)

        this.list = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateList error ', error)
      }
    },
    async delete(list) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ListService.deleteProjectList(list)

        this.list = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteList error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getProjectLists: state => state.lists,
    getProjectList: state => state.list,
    getErrors: state => state.error,
  },
})

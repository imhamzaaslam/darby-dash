import { defineStore } from 'pinia'
import ProjectListService from '../services/ProjectListService'

export const useProjectListStore = defineStore('project_lists', {
  state: () => ({
    projectLists: [],
    projectListsForDropDown: [],
    projectList: null,
    projectListsCount: 0,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(projectId, filters) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectListService.getProjectLists(projectId, filters)

        this.projectLists = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getLists error ', error)
      }
    },
    async getWithoutMileStone(projectId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectListService.getWithoutMileStone(projectId)

        this.projectListsForDropDown = response.data.data.map(list => ({ label: list.name, value: list.id }))
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getLists error ', error)
      }
    },
    async create(list) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectListService.createProjectList(list)

        this.projectList = response.data.data
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
        const response = await ProjectListService.updateProjectList(list)

        this.projectList = response.data.data
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
        const response = await ProjectListService.deleteProjectList(list)

        this.projectList = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteList error ', error)
      }
    },
    async saveSortedOrder(projectId, lists) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ProjectListService.saveSortedOrder(projectId, lists)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('saveSortedOrder error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getProjectLists: state => state.projectLists,
    getProjectListsForDropDown: state => state.projectListsForDropDown,
    getProjectList: state => state.projectList,
    getErrors: state => state.error,
  },
})

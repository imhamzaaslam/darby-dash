import { defineStore } from 'pinia'
import StatusService from '../services/StatusService'

export const useStatusStore = defineStore('statuses', {
  state: () => ({
    statuses: [],
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll() {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await StatusService.getStatuses()

        this.statuses = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getStatuses error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getStatuses: state => state.statuses,
    getErrors: state => state.error,
  },
})

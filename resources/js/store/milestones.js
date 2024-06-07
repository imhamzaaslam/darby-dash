import { defineStore } from 'pinia'
import MileStoneService from '../services/MileStoneService'

export const useMileStoneStore = defineStore('milestones', {
  state: () => ({
    milestones: [],
    milestone: null,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(projectUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await MileStoneService.getAll(projectUuid)

        this.milestones = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getMilestones error ', error)
      }
    },
    async create(projectUuid, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await MileStoneService.create(projectUuid, payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createMilestone error ', error)
      }
    },
    async update(milestoneUuid, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await MileStoneService.update(milestoneUuid, payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateMilestone error ', error)
      }
    },
    async delete(milestoneUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        await MileStoneService.delete(milestoneUuid)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteMilestone error ', error)
      }
    },
  },

  getters: {
    getMilestones: state => state.milestones,
    getMilestone: state => state.milestone,
    getLoadStatus: state => state.loadStatus,
    getError: state => state.error,
  },
})

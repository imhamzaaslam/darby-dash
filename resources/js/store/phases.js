import { defineStore } from 'pinia'
import PhaseService from '../services/PhaseService'

export const usePhaseStore = defineStore('phases', {
  state: () => ({
    phases: [],
    phase: null,
    phasesCount: 0,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(projectId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await PhaseService.getProjectPhases(projectId)

        this.phases = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getPhases error ', error)
      }
    },
    async create(phase) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await PhaseService.createProjectPhase(phase)

        this.phase = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createPhase error ', error)
      }
    },
    async update(phase) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await PhaseService.updateProjectPhase(phase)

        this.phase = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updatePhase error ', error)
      }
    },
    async delete(phase) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await PhaseService.deleteProjectPhase(phase)

        this.phase = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deletePhase error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getProjectPhases: state => state.phases,
    getProjectPhase: state => state.phase,
    getErrors: state => state.error,
  },
})

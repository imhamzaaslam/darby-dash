import { defineStore } from 'pinia'
import RoleService from '../services/RoleService'

export const useRoleStore = defineStore('roles', {
  state: () => ({
    roles: [],
    rolesIds: [],
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll() {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await RoleService.getRoles()

        this.roles = response.data.data.map(role => ({ id: role.id, name: role.name }))
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getRoles error ', error)
      }
    },
    capitalizeFirstLetter(text) {
      return text.replace(/\b\w/g, l => l.toUpperCase())
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getRoles: state => state.roles,
    getErrors: state => state.error,
  },
})

import { defineStore } from 'pinia'
import RoleService from '../services/RoleService'

export const useRoleStore = defineStore('roles', {
  state: () => ({
    roles: [],
    rolesFullData: [],
    rolesIds: [],
    rolePermissions: [],
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
        this.rolesFullData = response.data.data
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
    async getPermissions(roleId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await RoleService.getPermissions(roleId)

        this.rolePermissions = response.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getPermissions error ', error)
      }
    },
    async updateRolePermissions(roleId, permissions) {
      this.error = null
      this.loadStatus = 1
      try {
        await RoleService.updateRolePermissions(roleId, permissions)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateRolePermissions error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getRoles: state => state.roles,
    getRolesFullData: state => state.rolesFullData,
    getRolePermissions: state => state.rolePermissions,
    getErrors: state => state.error,
  },
})

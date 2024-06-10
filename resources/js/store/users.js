import { defineStore } from 'pinia'
import UserService from '../services/UserService'

export const useUserStore = defineStore('users', {
  state: () => ({
    users: [],
    user: null,
    usersByProject: [],
    usersCount: 0,
    projectManagers: null,
    members: null,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(page = 1, perPage = 10, search = null, orderBy = null, orderDirection = null) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserService.getUsers(page, perPage, search, orderBy, orderDirection)

        this.users = response.data.data
        this.usersCount = response.data.meta.total
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getUsers error ', error)
      }
    },
    async getByProjects(projectUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserService.getByProjects(projectUuid)

        this.usersByProject = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getByProjects error ', error)
      }
    },
    async show(uuid) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserService.getUser(uuid)

        this.user = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getUser error ', error)
      }
    },
    async create(user) {
      this.error = null
      this.loadStatus = 1
      try {
        await UserService.createUser(user)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createUser error ', error)
      }
    },
    async update(user) {
      this.error = null
      this.loadStatus = 1
      try {
        await UserService.updateUser(user)

        this.loadStatus = 2
      } catch (error) {
        console.log('error is', error)
        this.error = error
        this.loadStatus = 3
        console.error('updateUser error ', error)
      }
    },
    async delete(id) {
      this.error = null
      this.loadStatus = 1
      try {
        await UserService.deleteUser(id)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteUser error ', error)
      }
    },
    async getProjectManagers() {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserService.getUsersByRole('project manager')

        this.projectManagers = response.data.data

        this.projectManagers = response.data.data.map(manager => ({ id: manager.id, name: manager.name_first+ " " + manager.name_last }))
        
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getProjectManagers error ', error)
      }
    },
    async getMembers() {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserService.getAll()

        // contatinate role
        this.members = response.data.data.map(member => ({ id: member.id, name: member.name_first+ " " + member.name_last+ " (" + member.role + ")" }))
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getMembers error ', error)
      }
    },
    async updateUserImage(avatar, uuid) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserService.updateUserImage(avatar, uuid)

        this.user = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateUserImage error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getUsers: state => state.users,
    getUser: state => state.user,
    getUsersByProjects: state => state.usersByProject,
    getProjectManagersList: state => state.projectManagers,
    getMembersList: state => state.members,
    getErrors: state => state?.error?.response?.data?.errors || state?.error?.response?.data?.message,
    getStatusCode: state => state.error?.response?.status,
  },
})

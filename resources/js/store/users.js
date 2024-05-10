import { defineStore } from 'pinia'
import UserService from '../services/UserService'

export const useUserStore = defineStore('users', {
  state: () => ({
    users: [],
    user: null,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(page, perPage) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserService.getUsers(page, perPage)

        this.users = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getUsers error ', error)
      }
    },
    async show(id) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserService.getUser(id)

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
        const response = await UserService.createUser(user)

        this.user = response.data.data
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
        const response = await UserService.updateUser(user)

        this.user = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateUser error ', error)
      }
    },
    async delete(id) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await UserService.deleteUser(id)

        this.user = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteUser error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getUsers: state => state.users,
    getUser: state => state.user,
    getErrors: state => state.error,
  },
})

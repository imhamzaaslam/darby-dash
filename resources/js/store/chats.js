import { defineStore } from 'pinia'
import ChatService from '../services/ChatService'

export const useChatStore = defineStore('chats', {
  state: () => ({
    contacts: [],
    chatsContacts: [],
    activeChat: null,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getProjectInbox(projectId, search='') {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ChatService.getChatsContacts(projectId, search)

        this.chatsContacts = response.data.chats
        this.contacts = response.data.contacts
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getChatsContacts error ', error)
      }
    },
    async getChat(payload) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ChatService.getChat(payload)

        this.activeChat = response.data.chat
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getChat error ', error)
      }
    },

    async sendMsg(payload) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ChatService.sendMessage(payload)

        this.activeChat = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('sendMessage error ', error)
      }
    },

    async updateMsg(payload) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ChatService.updateMessage(payload)

        this.activeChat = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateMessage error ', error)
      }
    },

    async deleteMsg(payload) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await ChatService.deleteMessage(payload)

        this.activeChat = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteMessage error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getActiveChat: state => state.activeChat,
    getContacts: state => state.contacts,
    getChatsContacts: state => state.chatsContacts,
    getErrors: state => state.error,
  },
})

import { apiClient, generateQueryString } from './api'

export default {
  getChatsContacts(projectId) {
    return apiClient.get(`/admin/projects/${projectId}/chat/chats-and-contacts`)
  },

  async getChat(payload) {
    return apiClient.get(`/admin/projects/${payload.projectId}/chat/${payload.userId}`)
  },

  async sendMessage(payload) {
    return apiClient.post(`/admin/projects/${payload.projectUuid}/chat/${payload.chatUuid}`, payload)
  },
}

import { apiClient, generateQueryString } from './api'

export default {
  getChatsContacts(projectId, search) {
    return apiClient.get(`/admin/projects/${projectId}/chat/chats-and-contacts`, {
      params: { search },
    })
  },

  async getChat(payload) {
    return apiClient.get(`/admin/projects/${payload.projectId}/chat/${payload.userId}`)
  },

  async sendMessage(payload) {
    return apiClient.post(`/admin/projects/${payload.projectId}/chat/${payload.userId}`, payload)
  },

  async updateMessage(payload) {
    return apiClient.post(`/admin/chat/${payload.chatUuid}/update-message`, payload)
  },

  async deleteMessage(payload) {
    return apiClient.post(`/admin/chat/${payload.chatUuid}/delete-message`, payload)
  },
}

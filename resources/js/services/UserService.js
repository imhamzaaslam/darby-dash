import { apiClient, generateQueryString } from './api'

export default {
  getUsers(page, perPage) {
    return apiClient.get(`admin/users?page=${page}&perPage=${perPage}`)
  },
  getUser(id) {
    return apiClient.get(`admin/users/${id}`)
  },
  createUser(user) {
    return apiClient.post('admin/users', user)
  },
  updateUser(user) {
    return apiClient.patch(`admin/users/${user.uuid}`, user)
  },
  deleteUser(id) {
    return apiClient.delete(`admin/users/${id}`)
  },
}

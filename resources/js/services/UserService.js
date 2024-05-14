import { apiClient, generateQueryString } from './api'

export default {
  getUsers: async (
    page = 1,
    perPage = 10,
    search = null,
    orderBy = null,
    orderDirection = null,
  ) => {
    const baseUrl = `/admin/users?page=${page}&perPage=${perPage}`
    const queryString = generateQueryString(search, orderBy, orderDirection)

    return await apiClient.get(baseUrl + queryString)
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
  getUsersByRole(role) {
    return apiClient.get(`admin/users/role/${role}`)
  },
}

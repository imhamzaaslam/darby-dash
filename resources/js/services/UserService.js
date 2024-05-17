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
  
  getUser(uuid) {
    return apiClient.get(`admin/users/${uuid}`)
  },
  createUser(user) {
    return apiClient.post('admin/users', user)
  },
  updateUser(user) {
    // delete user avatar
    let newUser = { ...user }
    if (user.avatar) {
      delete newUser.avatar
    }
    return apiClient.patch(`admin/users/${user.uuid}`, newUser)
  },
  deleteUser(id) {
    return apiClient.delete(`admin/users/${id}`)
  },
  getUsersByRole(role) {
    return apiClient.get(`admin/users/role/${role}`)
  },
}

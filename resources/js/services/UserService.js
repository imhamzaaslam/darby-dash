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

  getAll() {
    return apiClient.get('admin/users/all')
  },

  getByProjects: async projectUuid => {
    return await apiClient.get(`admin/projects/${projectUuid}/users`)
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

  getUsersByRole(role = null) {
    if (role) {
      return apiClient.get(`admin/users/role/${role}`)
    }

    return apiClient.get('admin/users/role')
  },

  updateUserImage: async (avatar, uuid) => {
    const formData = new FormData()

    formData.append('avatar', avatar)

    return await apiClient.post(`admin/users/${uuid}/avatar`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },

  updatePassword: async (uuid, payload) => {
    return await apiClient.patch(`admin/users/${uuid}/update-password`, payload)
  },
}

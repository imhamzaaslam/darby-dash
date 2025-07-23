import { apiClient, generateQueryString } from './api'

export default {
  getCompanies(page = 1, perPage = 10, search = null) {
    const baseUrl = `/super-admin/companies?page=${page}&per_page=${perPage}`

    const queryParams = [search && `keyword=${search}`].filter(Boolean)

    const queryString = queryParams.length ? `&${queryParams.join('&')}` : ''

    return apiClient.get(baseUrl + queryString)
  },
  createCompany(payload) {
    return apiClient.post(`/super-admin/companies`, payload)
  },
  showCompany(companyId) {
    return apiClient.get(`/super-admin/companies/${companyId}`)
  },
  updateCompany(companyId, payload) {
    return apiClient.patch(`/super-admin/companies/${companyId}`, payload)
  },
  deleteCompany(companyId) {
    return apiClient.delete(`/super-admin/companies/${companyId}`)
  },
  logo(payload, companyId) {
    return apiClient.post(`/super-admin/companies/${companyId}/upload/logo`, payload)
  },
  favicon(payload, companyId) {
    return apiClient.post(`/super-admin/companies/${companyId}/upload/favicon`, payload)
  },
  deleteAsset(fileId, companyId) {
    return apiClient.delete(`/super-admin/companies/${companyId}/delete-asset/${fileId}`)
  },
  saveCompanyDetails(payload, companyId) {
    return apiClient.post(`/super-admin/companies/${companyId}/save-details`, payload)
  },
  saveBucksDetails(payload, companyId) {
    return apiClient.post(`/super-admin/companies/${companyId}/save-bucks-details`, payload)
  },
  saveUIPrefrences(payload, companyId) {
    return apiClient.post(`/super-admin/companies/${companyId}/save-ui-prefrences`, payload)
  },
  saveColors(payload, companyId) {
    return apiClient.post(`/super-admin/companies/${companyId}/save-colors`, payload)
  },
  updateActiveState: async (companyId, payload) => {
    return await apiClient.patch(`super-admin/companies/${companyId}/update-active-state`, payload)
  },

  getAllUsers: async (companyId, page = 1, perPage = 10, searchName = null, searchEmail = null, roleId = null) => {
    const baseUrl = `/super-admin/companies/${companyId}/users/?page=${page}&per_page=${perPage}`

    const queryParams = [
      searchName && `name=${searchName}`,
      searchEmail && `email=${searchEmail}`,
      roleId && `roleId=${roleId}`,
    ].filter(Boolean)

    const queryString = queryParams.length ? `&${queryParams.join('&')}` : ''

    return apiClient.get(baseUrl + queryString)
  },
  createUser(companyId, payload) {
    return apiClient.post(`/super-admin/companies/${companyId}/users`, payload)
  },
  updateUser(companyId, userId, payload) {
    return apiClient.patch(`/super-admin/companies/${companyId}/users/${userId}`, payload)
  },
  deleteUser(companyId, userId) {
    return apiClient.delete(`/super-admin/companies/${companyId}/users/${userId}`)
  },
  updateActiveState: async (companyId, payload) => {
    return await apiClient.patch(`super-admin/companies/${companyId}/update-active-state`, payload)
  },
  wipeData: async companyId => {
    return await apiClient.delete(`super-admin/companies/${companyId}/wipe-data`)
  },
}

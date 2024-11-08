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
  updateCompany(companyId, payload) {
    return apiClient.patch(`/super-admin/companies/${companyId}`, payload)
  },

  deleteCompany(companyId) {
    return apiClient.delete(`/super-admin/companies/${companyId}`)
  },
}

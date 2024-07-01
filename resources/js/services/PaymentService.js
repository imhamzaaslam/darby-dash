import { apiClient, generateQueryString } from './api'

export default {
  getAll(
    projectUuid,
    page = 1,
    perPage = 10,
    search = null,
    orderBy = 'id',
    order = 'desc',
  ) {
    const baseUrl = `/admin/projects/${projectUuid}/payments?page=${page}&per_page=${perPage}`

    const queryParams = [
      search && `keyword=${search}`,
      orderBy && `orderBy=${orderBy}`,
      order && `order=${order}`,
    ].filter(Boolean)

    const queryString = queryParams.length ? `&${queryParams.join('&')}` : ''

    return apiClient.get(baseUrl + queryString)
  },
  create(projectUuid, payload) {
    return apiClient.post(`admin/projects/${projectUuid}/payments`, payload)
  },
  get(paymentUuid) {
    return apiClient.get(`admin/payments/${paymentUuid}`)
  },
  update(paymentUuid, payload) {
    return apiClient.patch(`admin/payments/${paymentUuid}`, payload)
  },
  delete(paymentUuid) {
    return apiClient.delete(`admin/payments/${paymentUuid}`)
  },
}
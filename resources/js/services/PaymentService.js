import { apiClient, generateQueryString } from './api'

export default {
  getAll(
    page = 1,
    perPage = 10,
  ) {
    return apiClient.get(`/admin/payments?page=${page}&per_page=${perPage}`)
  },
  create(payload) {
    return apiClient.post(`admin/payments`, payload)
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
import { defineStore } from 'pinia'
import PaymentService from '../services/PaymentService'

export const usePaymentStore = defineStore('payments', {
  state: () => ({
    payments: [],
    payment: null,
    loadStatus: 0,
    paymentsCount: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(projectUuid, page = 1, perPage = 10, search = null, orderBy = 'id', order = 'desc') {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await PaymentService.getAll(projectUuid, page, perPage, search, orderBy, order)
    
        this.payments = response.data.data
        this.paymentsCount = response.data.meta.total
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getPayments error ', error)
      }
    },
    async create(projectUuid, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await PaymentService.create(projectUuid, payload)
    
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createPayment error ', error)
      }
    },
    async get(paymentUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await PaymentService.get(paymentUuid)
        
        this.payment = response.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getPayment error ', error)
      }
    },
    async update(paymentUuid, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await PaymentService.update(paymentUuid, payload)
    
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updatePayment error ', error)
      }
    },
    async delete(paymentUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        await PaymentService.delete(paymentUuid)
    
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deletePayment error ', error)
      }
    },
  },

  getters: {
    getPayments: state => state.payments,
    getPayment: state => state.payment,
    getLoadStatus: state => state.loadStatus,
    getPaymentsCount: state => state.paymentsCount,
    getErrors: state => state.error,
    getStatusCode: state => state.error?.response?.status,
  },
})
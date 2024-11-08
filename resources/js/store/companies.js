import { defineStore } from 'pinia'
import CompanyService from '../services/CompanyService'

export const useCompanyStore = defineStore('companies', {
  state: () => ({
    companies: [],
    companiesCount: 0,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(page = 1, perPage = 10, search = null) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await CompanyService.getCompanies(page, perPage, search)

        this.companies = response.data.data
        this.companiesCount = response.data.meta.total
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getCompanies error ', error)
      }
    },
    async create(payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await CompanyService.createCompany(payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createCompany error ', error)
      }
    },
    async update(companyId, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await CompanyService.updateCompany(companyId, payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateCompany error ', error)
      }
    },
    async delete(companyId) {
      this.error = null
      this.loadStatus = 1
      try {
        await CompanyService.deleteCompany(companyId)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteCompany error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getCompanies: state => state.companies,
    getErrors: state => state?.error?.response?.data?.errors || state?.error?.response?.data?.message,
    getStatusCode: state => state.error?.response?.status,
  },
})

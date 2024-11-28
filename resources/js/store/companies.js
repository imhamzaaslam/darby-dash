import { defineStore } from 'pinia'
import CompanyService from '../services/CompanyService'

export const useCompanyStore = defineStore('companies', {
  state: () => ({
    companies: [],
    users: [],
    company: [],
    companiesCount: 0,
    usersCount: 0,
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
    async show(companyId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await CompanyService.showCompany(companyId)

        this.company = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('showCompany error ', error)
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
    async uploadLogo(payload, companyId) {
      this.error = null
      this.loadStatus = 1
      try {
        const res = await CompanyService.logo(payload, companyId)

        this.loadStatus = 2

        return res
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('logo error ', error)
      }
    },
    async uploadFavicon(payload, companyId) {
      this.error = null
      this.loadStatus = 1
      try {
        const res = await CompanyService.favicon(payload, companyId)

        this.loadStatus = 2
        
        return res
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('favicon error ', error)
      }
    },
    async deleteAsset(fileId, companyId) {
      this.error = null
      this.loadStatus = 1
      try {
        const res = await CompanyService.deleteAsset(fileId, companyId)

        this.loadStatus = 2
        
        return res
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteAsset error ', error)
      }
    },
    async saveCompanyDetails(payload, companyId) {
      this.error = null
      this.loadStatus = 1
      try {
        const res = await CompanyService.saveCompanyDetails(payload, companyId)

        this.loadStatus = 2

        return res
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('save company details error ', error)
      }
    },
    async saveColors(payload, companyId) {
      this.error = null
      this.loadStatus = 1
      try {
        const res = await CompanyService.saveColors(payload, companyId)

        this.loadStatus = 2

        return res
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('save color error ', error)
      }
    },
    async updateActiveState(companyId, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await CompanyService.updateActiveState(companyId, payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateActiveState error ', error)
      }
    },
    async getAllUsers(companyId, page = 1, perPage = 10, searchName = null, searchEmail= null, roleId=null) {
      this.error = null
      this.loadStatus = 1
      try {
        const res = await CompanyService.getAllUsers(companyId, page, perPage, searchName, searchEmail, roleId)

        this.users = res.data.data
        this.usersCount = res.data.meta.total

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getAllUsers error ', error)
      }
    },
    async createUser(companyId, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await CompanyService.createUser(companyId, payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createUser error ', error)
      }
    },
    async updateUser(companyId, userId, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await CompanyService.updateUser(companyId, userId, payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateUser error ', error)
      }
    },
    async deleteUser(companyId, userId) {
      this.error = null
      this.loadStatus = 1
      try {
        await CompanyService.deleteUser(companyId, userId)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteUser error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getCompanies: state => state.companies,
    getUsers: state => state.users,
    getCompany: state => state.company,
    getErrors: state => state?.error?.response?.data?.errors || state?.error?.response?.data?.message,
    getStatusCode: state => state.error?.response?.status,
  },
})

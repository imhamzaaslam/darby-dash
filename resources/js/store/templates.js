import { defineStore } from 'pinia'
import TemplateService from '../services/TemplateService'

export const useTemplateStore = defineStore('templates', {
  state: () => ({
    template: null,
    templates: [],
    templatesDropdown: [],
    loadStatus: 0,
    templatesCount: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll() {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await TemplateService.getTemplates()

        this.templatesDropdown = response.data.data.map(template => ({ id: template.id, name: template.template_name }))
        this.templates = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getTemplates error ', error)
      }
    },

    async getAllWithPagination(page = 1, perPage = 10, search = null) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await TemplateService.getTemplatesWithPagination(page, perPage, search)

        this.templates = response.data.data
        this.templatesCount = response.data.meta.total
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getTemplates error ', error)
      }
    },

    async show(id) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await TemplateService.getTemplate(id)

        this.template = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getTemplate error ', error)
      }
    },

    async create(projectId, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await TemplateService.createTemplate(projectId, payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createTemplate error ', error)
      }
    },

    async delete(id) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await TemplateService.deleteTemplate(id)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteTemplate error ', error)
      }
    },

    async createList(templateId, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await TemplateService.createTemplateList(templateId, payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createTemplate error ', error)
      }
    },


    async updateList(listUuid, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await TemplateService.updateTemplateList(listUuid, payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateTemplateList error ', error)
      }
    },

    async deleteList(listId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await TemplateService.deleteTemplateList(listId)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteTemplateList error ', error)
      }
    },

    async saveSortedOrder(templateId, lists) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await TemplateService.saveSortedOrder(templateId, lists)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('saveSortedOrder error ', error)
      }
    },

    async createTask(listId, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await TemplateService.createTemplateListTask(listId, payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createTemplateListTask error ', error)
      }
    },


    async updateTask(taskId, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await TemplateService.updateTemplateListTask(taskId, payload)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateTemplateListTask error ', error)
      }
    },

    async deleteTask(taskId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await TemplateService.deleteTemplateListTask(taskId)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteTemplateListTask error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getErrors: state => state.error,
    getTemplate: state => state.template,
    getTemplates: state => state.templates,
    getTemplatesDropdown: state => state.templatesDropdown,
  },
})

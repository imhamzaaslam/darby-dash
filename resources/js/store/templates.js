import { defineStore } from 'pinia'
import TemplateService from '../services/TemplateService'

export const useTemplateStore = defineStore('templates', {
  state: () => ({
    templates: [],
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll() {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await TemplateService.getTemplates()

        this.templates = response.data.data.map(template => ({ id: template.id, name: template.template_name }))
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getTemplates error ', error)
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
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getErrors: state => state.error,
    getTemplates: state => state.templates,
  },
})

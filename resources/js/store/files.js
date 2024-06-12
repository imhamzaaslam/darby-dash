import { defineStore } from 'pinia'
import FileService from '../services/FileService'

export const useFileStore = defineStore('files', {
  state: () => ({
    files: [],
    file: null,
    filesCount: 0,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(projectId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await FileService.getAll(projectId)
        
        this.files = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getFiles error ', error)
      }
    },
    async upload(files, projectUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await FileService.uploadFiles(files, projectUuid)
    
        this.file = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createFile error ', error)
      }
    },
    async update(file) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await FileService.updateFile(file)
    
        this.file = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateFile error ', error)
      }
    },
    async delete(fileUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        await FileService.delete(fileUuid)

        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteFile error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getError: state => state.error,
    getFiles: state => state.files,
    getFile: state => state.file,
  },
})
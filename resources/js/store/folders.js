import { defineStore } from 'pinia'
import FolderService from '../services/FolderService'

export const useFolderStore = defineStore('folders', {
  state: () => ({
    folders: [],
    folder: null,
    folderFiles: [],
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(projectUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await FolderService.getAll(projectUuid)
    
        this.folders = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getFolders error ', error)
      }
    },
    async create(projectUuid, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await FolderService.create(projectUuid, payload)
    
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createFolder error ', error)
      }
    },
    async update(folderUuid, payload) {
      this.error = null
      this.loadStatus = 1
      try {
        await FolderService.update(folderUuid, payload)
    
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateFolder error ', error)
      }
    },
    async delete(folderUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        await FolderService.delete(folderUuid)
    
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteFolder error ', error)
      }
    },
    async getFiles(folderUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await FolderService.getFiles(folderUuid)

        console.log('getFiles', response.data.data)
    
        this.folderFiles = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getFolder error ', error)
      }
    },
    async uploadFiles(files, folderUuid) {
      this.error = null
      this.loadStatus = 1
      try {
        await FolderService.uploadFiles(files, folderUuid)
    
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('uploadFiles error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getError: state => state.error,
    getFolders: state => state.folders,
    getFolder: state => state.folder,
    getFolderFiles: state => state.folderFiles,
  },
})
    
import { apiClient, generateQueryString } from './api'

export default {
  getAll(projectUuid) {
    return apiClient.get(`admin/projects/${projectUuid}/folders`)
  },
  create(projectUuid, payload) {
    return apiClient.post(`admin/projects/${projectUuid}/folders`, payload)
  },
  update(folderUuid, payload) {
    return apiClient.patch(`admin/folders/${folderUuid}`, payload)
  },
  delete(folderUuid) {
    return apiClient.delete(`admin/folders/${folderUuid}`)
  },
  getFiles(folderUuid) {
    return apiClient.get(`admin/folders/${folderUuid}/files`)
  },
  uploadFiles(files, folderUuid) {
    const formData = new FormData()

    files.forEach(file => {
      formData.append('files[]', file)
    })

    return apiClient.post(`admin/folders/${folderUuid}/files`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
}
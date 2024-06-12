import { apiClient, generateQueryString } from './api'

export default {
  getAll(projectUuid) {
    return apiClient.get(`admin/projects/${projectUuid}/files`)
  },
  uploadFiles(files, projectUuid) {
    const formData = new FormData()

    files.forEach(file => {
      formData.append('files[]', file)
    })

    return apiClient.post(`admin/projects/${projectUuid}/files`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

  },
  update(fileUuid, payload) {

  },
  delete(fileUuid) {
    return apiClient.delete(`admin/files/${fileUuid}`)
  },
}
import { apiClient, generateQueryString } from './api'

export default {
  getAll(projectUuid) {
    return apiClient.get(`admin/projects/${projectUuid}/files`)
  },
  uploadFiles(files, projectUuid, folderUuid = null) {
    const formData = new FormData()

    files.forEach(file => {
      formData.append('files[]', file)
    })

    if (folderUuid) {
      formData.append('folder_uuid', folderUuid)
    }

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
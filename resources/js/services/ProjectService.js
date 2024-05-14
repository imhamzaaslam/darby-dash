import { apiClient, generateQueryString } from './api'

export default {
  getProjects() {
    return apiClient.get('admin/projects')
  },
  getProject(id) {
    return apiClient.get(`admin/projects/${id}`)
  },
  createProject(project) {
    return apiClient.post('admin/projects', project)
  },
  updateProject(project) {
    return apiClient.patch(`admin/projects/${project.uuid}`, project)
  },
  deleteProject(id) {
    return apiClient.delete(`admin/projects/${id}`)
  },
}

import { apiClient } from './api'

export default {
  getProjects(search = null, projectTypeId = null, projectManagerId = null) {
    const queryParams = [
      search && `keyword=${search}`,
      projectTypeId && `projectTypeId=${projectTypeId}`,
      projectManagerId && `projectManagerId=${projectManagerId}`,
    ].filter(Boolean)
  
    const queryString = queryParams.length ? `?${queryParams.join('&')}` : ''

    return apiClient.get(`admin/projects${queryString}`)
  },
  getProjectsByType(id) {
    return apiClient.get(`admin/projects/type/${id}`)
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
  getProgress(uuid) {
    return apiClient.get(`admin/projects/${uuid}/progress`)
  },
  updateMember(project) {
    return apiClient.patch(`admin/projects/${project.uuid}/users`, project)
  },
}

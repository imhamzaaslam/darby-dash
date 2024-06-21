import { apiClient } from './api'

export default {
  getProjects(
    page = 1,
    perPage = 10,
    search = null,
    projectTypeId = null,
    projectManagerId = null,
  ) {
    const baseUrl = `/admin/projects?page=${page}&per_page=${perPage}`

    const queryParams = [
      search && `keyword=${search}`,
      projectTypeId && `projectTypeId=${projectTypeId}`,
      projectManagerId && `projectManagerId=${projectManagerId}`,
    ].filter(Boolean)
  
    const queryString = queryParams.length ? `&${queryParams.join('&')}` : ''

    return apiClient.get(baseUrl + queryString)
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
  deleteMember(uuid, userUuid) {
    return apiClient.delete(`admin/projects/${uuid}/user/${userUuid}`)
  },
}

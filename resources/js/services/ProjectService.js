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
  getProjectActivities(uuid) {
    return apiClient.get(`admin/projects/${uuid}/activities`)
  },
  createProject(project) {
    const formData = new FormData()

    for (const key in project) {
      if (key === 'project_logo' && project[key][0] instanceof File) {
        formData.append('project_logo', project[key][0])
      } else if (project[key] !== null && project[key] !== undefined) {
        formData.append(key, project[key])
      }
    }
    
    return apiClient.post('admin/projects', formData)
  },
  updateProject(project) {
    const formData = new FormData()
    
    formData.append('_method', 'PATCH') // Ensure Laravel understands it
    
    for (const key in project) {
      if (key === 'project_logo' && project[key][0] instanceof File) {
        formData.append('project_logo', project[key][0])
      } else if (project[key] !== null && project[key] !== undefined) {
        formData.append(key, project[key])
      }
    }
    
    return apiClient.post(`admin/projects/${project.uuid}`, formData)
  },
  projectComplete(project){
    return apiClient.patch(`admin/projects/${project.uuid}/complete`, project)
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

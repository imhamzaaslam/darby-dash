import { apiClient, generateQueryString } from './api'

export default {
  getProjectLists(projectId, filters) {
    return apiClient.get(`admin/projects/${projectId}/lists`, { params: filters })
  },
  getWithoutMileStone(projectId) {
    return apiClient.get(`admin/projects/${projectId}/lists-without-milestone`)
  },
  createProjectList(list) {
    return apiClient.post(`admin/projects/${list.project_uuid}/list`, list)
  },
  updateProjectList(list) {
    return apiClient.patch(`admin/projects/${list.project_uuid}/list/${list.uuid}`, list)
  },
  deleteProjectList(list) {
    return apiClient.delete(`admin/projects/${list.project_uuid}/list/${list.uuid}`)
  },
  saveSortedOrder(projectId, lists) {
    return apiClient.patch(`admin/projects/${projectId}/lists/sort`, { lists: lists })
  },
}

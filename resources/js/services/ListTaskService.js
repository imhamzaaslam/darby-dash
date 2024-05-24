import { apiClient, generateQueryString } from './api'

export default {
  getListTasks(projectId) {
    return apiClient.get(`admin/projects/${projectId}/lists`)
  },
  createListTask(task) {
    return apiClient.post(`admin/projects/${task.project_uuid}/list`, task)
  },
  updateListTask(task) {
    return apiClient.patch(`admin/projects/${task.project_uuid}/list/${task.uuid}`, task)
  },
  deleteListTask(task) {
    return apiClient.delete(`admin/projects/${task.project_uuid}/list/${task.uuid}`)
  },
}

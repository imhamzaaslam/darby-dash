import { apiClient, generateQueryString } from './api'

export default {
  getAll(projectId) {
    return apiClient.get(`admin/projects/${projectId}/allTasks`)
  },
  getUnlistedTasks(projectId) {
    return apiClient.get(`admin/projects/${projectId}/tasks`)
  },
  createProjectTask(task) {
    return apiClient.post(`admin/projects/${task.project_uuid}/task`, task)
  },
  updateProjectTask(task) {
    return apiClient.patch(`admin/projects/${task.project_uuid}/task/${task.uuid}`, task)
  },
  deleteProjectTask(task) {
    return apiClient.delete(`admin/projects/${task.project_uuid}/task/${task.uuid}`)
  },
}

import { apiClient, generateQueryString } from './api'

export default {
  getAll(projectId) {
    return apiClient.get(`admin/projects/${projectId}/allTasks`)
  },
  getDueTasks(projectId) {
    return apiClient.get(`admin/projects/${projectId}/dueTasks`)
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
  updateProjectTasksOrder(task) {
    return apiClient.post(`admin/projects/${task.project_uuid}/task/${task.uuid}/order`, task)
  },
  updateSubTaskOrdering(projectId, taskId, tasks) {
    return apiClient.post(`admin/projects/${projectId}/task/${taskId}/sort`, { tasks: tasks })
  },
  fetchFiles(taskUuid) {
    return apiClient.get(`admin/task/${taskUuid}/files`)
  },
  uploadFiles(taskUuid, files) {
    const formData = new FormData()

    files.forEach(file => {
      formData.append('files[]', file)
    })

    return apiClient.post(`admin/task/${taskUuid}/files`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
  deleteProjectTask(task) {
    return apiClient.delete(`admin/projects/${task.project_uuid}/task/${task.uuid}`)
  },
  updateAttributes(taskUuid, payload) {
    return apiClient.patch(`admin/task/${taskUuid}`, payload)
  },
  assignTask(taskUuid, payload) {
    return apiClient.post(`admin/task/${taskUuid}/assign`, payload)
  },
  removeAssignee(taskUuid, payload) {
    return apiClient.post(`admin/task/${taskUuid}/unassign`, payload)
  },
}

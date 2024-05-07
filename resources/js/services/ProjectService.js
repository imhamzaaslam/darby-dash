import { apiClient, generateQueryString } from './api'

export default {
  getProjects() {
    return apiClient.get('/api/v1/projects')
  },
  getProject(id) {
    return apiClient.get(`/api/v1/projects/${id}`)
  },
  createProject(project) {
    return apiClient.post('/api/v1/projects', project)
  },
  updateProject(project) {
    return apiClient.put(`/api/v1/projects/${project.id}`, project)
  },
  deleteProject(id) {
    return apiClient.delete(`/api/v1/projects/${id}`)
  },
  getProjectTasks(projectId) {
    return apiClient.get(`/api/v1/projects/${projectId}/tasks`)
  },
  createProjectTask(task) {
    return apiClient.post(`/api/v1/projects/${task.project_id}/tasks`, task)
  },
  updateProjectTask(task) {
    return apiClient.put(`/api/v1/projects/${task.project_id}/tasks/${task.id}`, task)
  },
  deleteProjectTask(task) {
    return apiClient.delete(`/api/v1/projects/${task.project_id}/tasks/${task.id}`)
  },
  getProjectTaskComments(taskId) {
    return apiClient.get(`/api/v1/tasks/${taskId}/comments`)
  },
  createProjectTaskComment(comment) {
    return apiClient.post(`/api/v1/tasks/${comment.task_id}/comments`, comment)
  },
  updateProjectTaskComment(comment) {
    return apiClient.put(`/api/v1/tasks/${comment.task_id}/comments/${comment.id}`, comment)
  },
  deleteProjectTaskComment(comment) {
    return apiClient.delete(`/api/v1/tasks/${comment.task_id}/comments/${comment.id}`)
  },
  getProjectTaskAttachments(taskId) {
    return apiClient.get(`/api/v1/tasks/${taskId}/attachments`)
  },
  createProjectTaskAttachment(attachment) {
    return apiClient.post(`/api/v1/tasks/${attachment.task_id}/attachments`, attachment)
  },
  deleteProjectTaskAttachment(attachment) {
    return apiClient.delete(`/api/v1/tasks/${attachment.task_id}/attachments/${attachment.id}`)
  },
  getProjectTaskChecklists(taskId) {
    return apiClient.get(`/api/v1/tasks/${taskId}/checklists`)
  },
  createProjectTaskChecklist(checklist) {
    return apiClient.post(`/api/v1/tasks/${checklist.task_id}/checklists`, checklist)
  },
  updateProjectTaskChecklist(checklist) {
    return apiClient.put(`/api/v1/tasks/${checklist.task_id}/checklists/${checklist.id}`, checklist)
  },
}

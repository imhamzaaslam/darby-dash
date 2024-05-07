import { apiClient, generateQueryString } from './api'

export default {
  getProjects() {
    return apiClient.get('/projects')
  },
  getProject(id) {
    return apiClient.get(`/projects/${id}`)
  },
  createProject(project) {
    return apiClient.post('/projects', project)
  },
  updateProject(project) {
    return apiClient.put(`/projects/${project.id}`, project)
  },
  deleteProject(id) {
    return apiClient.delete(`/projects/${id}`)
  },
  getProjectTasks(projectId) {
    return apiClient.get(`/projects/${projectId}/tasks`)
  },
  createProjectTask(task) {
    return apiClient.post(`/projects/${task.project_id}/tasks`, task)
  },
  updateProjectTask(task) {
    return apiClient.put(`/projects/${task.project_id}/tasks/${task.id}`, task)
  },
  deleteProjectTask(task) {
    return apiClient.delete(`/projects/${task.project_id}/tasks/${task.id}`)
  },
  getProjectTaskComments(taskId) {
    return apiClient.get(`/tasks/${taskId}/comments`)
  },
  createProjectTaskComment(comment) {
    return apiClient.post(`/tasks/${comment.task_id}/comments`, comment)
  },
  updateProjectTaskComment(comment) {
    return apiClient.put(`/tasks/${comment.task_id}/comments/${comment.id}`, comment)
  },
  deleteProjectTaskComment(comment) {
    return apiClient.delete(`/tasks/${comment.task_id}/comments/${comment.id}`)
  },
  getProjectTaskAttachments(taskId) {
    return apiClient.get(`/tasks/${taskId}/attachments`)
  },
  createProjectTaskAttachment(attachment) {
    return apiClient.post(`/tasks/${attachment.task_id}/attachments`, attachment)
  },
  deleteProjectTaskAttachment(attachment) {
    return apiClient.delete(`/tasks/${attachment.task_id}/attachments/${attachment.id}`)
  },
  getProjectTaskChecklists(taskId) {
    return apiClient.get(`/tasks/${taskId}/checklists`)
  },
  createProjectTaskChecklist(checklist) {
    return apiClient.post(`/tasks/${checklist.task_id}/checklists`, checklist)
  },
  updateProjectTaskChecklist(checklist) {
    return apiClient.put(`/tasks/${checklist.task_id}/checklists/${checklist.id}`, checklist)
  },
}
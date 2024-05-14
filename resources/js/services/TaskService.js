import { apiClient, generateQueryString } from './api'

export default {
  getProjectTasks(projectId) {
    return apiClient.get(`admin/project/${projectId}/tasks`)
  },
  createProjectTask(task) {
    return apiClient.post(`admin/project/${task.project_id}/tasks`, task)
  },
  updateProjectTask(task) {
    return apiClient.patch(`admin/project/${task.project_id}/tasks/${task.uuid}`, task)
  },
  deleteProjectTask(task) {
    return apiClient.delete(`admin/project/${task.project_id}/tasks/${task.uuid}`)
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

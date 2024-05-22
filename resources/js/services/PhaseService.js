import { apiClient, generateQueryString } from './api'

export default {
  getProjectPhases(projectId) {
    return apiClient.get(`admin/projects/${projectId}/phases`)
  },
  createProjectPhase(phase) {
    return apiClient.post(`admin/projects/${phase.project_uuid}/phase`, phase)
  },
  updateProjectPhase(phase) {
    return apiClient.patch(`admin/projects/${phase.project_uuid}/phase/${phase.uuid}`, phase)
  },
  deleteProjectPhase(phase) {
    return apiClient.delete(`admin/projects/${phase.project_uuid}/phase/${phase.uuid}`)
  },
}

import { apiClient, generateQueryString } from './api'

export default {
  getCalendarEvents(projectId) {
    return apiClient.get(`admin/projects/${projectId}/calendar-events`)
  },
  createCalendarEvent(calendarEvent) {
    return apiClient.post(`admin/projects/${calendarEvent.project_uuid}/calendar-event`, calendarEvent)
  },
  updateCalendarEvent(calendarEvent) {
    return apiClient.patch(`admin/projects/${calendarEvent.project_uuid}/calendar-event/${calendarEvent.uuid}`, calendarEvent)
  },
  deleteCalendarEvent(calendarEvent) {
    return apiClient.delete(`admin/projects/${calendarEvent.project_uuid}/calendar-event/${calendarEvent.uuid}`)
  },
}

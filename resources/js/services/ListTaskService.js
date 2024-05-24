import { apiClient, generateQueryString } from './api'

export default {
  getListTasks(listUuid) {
    return apiClient.get(`admin/list/${listUuid}/tasks`)
  },
  createListTask(task) {
    return apiClient.post(`admin/list/${task.list_uuid}/task`, task)
  },
  updateListTask(task) {
    return apiClient.patch(`admin/list/${task.list_uuid}/task/${task.uuid}`, task)
  },
  deleteListTask(task) {
    return apiClient.delete(`admin/list/${task.list_uuid}/task/${task.uuid}`)
  },
}

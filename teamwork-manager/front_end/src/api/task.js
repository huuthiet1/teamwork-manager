import api from './api'

export default {
  list(groupId) {
    return api.get('/tasks', {
      params: { group_id: groupId }
    })
  },

  create(data) {
    return api.post('/tasks', data)
  },

  update(taskId, data) {
    return api.put(`/tasks/${taskId}`, data)
  },

  cancel(taskId, reason) {
    return api.delete(`/tasks/${taskId}`, {
      data: { reason }
    })
  }
}

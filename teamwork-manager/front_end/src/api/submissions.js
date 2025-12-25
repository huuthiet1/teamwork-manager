import api from './api'

export default {
  submit(taskId, formData) {
    return api.post(`/tasks/${taskId}/submit`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  }
}

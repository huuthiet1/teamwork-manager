import api from './api'

export default {
  list(groupId, afterId = null) {
    return api.get('/messages', {
      params: {
        group_id: groupId,
        after_id: afterId
      }
    })
  },

  send(data) {
    return api.post('/messages', data)
  },

  remove(messageId) {
    return api.delete(`/messages/${messageId}`)
  }
}

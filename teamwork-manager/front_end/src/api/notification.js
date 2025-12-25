import api from './api'

export default {
  // Danh sách notification
  list() {
    return api.get('/notifications')
  },

  // Đánh dấu 1 notification đã đọc
  read(id) {
    return api.post(`/notifications/${id}/read`)
  },

  // Đánh dấu tất cả đã đọc
  readAll() {
    return api.post('/notifications/read-all')
  },

  // Đếm số chưa đọc (badge)
  unreadCount() {
    return api.get('/notifications/unread-count')
  },
}

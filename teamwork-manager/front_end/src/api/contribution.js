import api from './api'

export default {
  show(params) {
    return api.get('/contributions', { params })
  },

  // (OPTIONAL – nếu làm sau)
  recalculate(data) {
    return api.post('/contributions/recalculate', data)
  },
}

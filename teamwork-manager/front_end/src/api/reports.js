import api from './api'

export default {
  dashboard() {
    return api.get('/ai/dashboard')
  }
}

import api from './api'

export default {
  async login(data) {
    const res = await api.post('/login', data)
    if (res.data.token) {
      localStorage.setItem('token', res.data.token)
    }
    return res
  },

  register(data) {
    return api.post('/register', data)
  },

  me() {
    return api.get('/me')
  },

  async logout() {
    await api.post('/logout')
    localStorage.removeItem('token')
  },

  changePassword(data) {
    return api.post('/change-password', data)
  }
}

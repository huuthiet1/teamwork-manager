import api from './api'

const aiService = {
  /**
   * Gợi ý AI dashboard
   */
  async getDashboardSuggestions() {
    const res = await api.get('/ai/dashboard')
    return res.data.suggestions
  }
}

export default aiService

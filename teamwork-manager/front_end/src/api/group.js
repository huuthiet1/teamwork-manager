import api from './api'

export default {
  list() {
    return api.get('/groups')
  },

  create(data) {
    return api.post('/groups', data)
  },

  detail(groupId) {
    return api.get(`/groups/${groupId}`)
  },

  transferLeader(groupId, newLeaderId) {
    return api.post(`/groups/${groupId}/transfer-leader`, {
      new_leader_id: newLeaderId,
    })
  },

  close(groupId) {
    return api.delete(`/groups/${groupId}`)
  },

  // OTP
  generateInvite(groupId) {
    return api.post(`/groups/${groupId}/invite`)
  },

  joinByOtp(otp) {
    return api.post('/groups/join-by-otp', {
      otp_code: otp,
    })
  },

  // Join request
  sendJoinRequest(groupId) {
    return api.post(`/groups/${groupId}/join-request`)
  },

  getJoinRequests(groupId) {
    return api.get(`/groups/${groupId}/join-requests`)
  },

  approveJoinRequest(requestId) {
    return api.post(`/join-requests/${requestId}/approve`)
  },

  rejectJoinRequest(requestId) {
    return api.post(`/join-requests/${requestId}/reject`)
  },
}

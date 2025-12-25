<template>
  <nav class="navbar navbar-light bg-light px-4">
    <span class="navbar-brand">Group Task</span>

    <div v-if="user">
      <span class="me-3">{{ user.name }}</span>
      <button class="btn btn-sm btn-outline-danger" @click="logout">
        Logout
      </button>
    </div>
  </nav>
</template>

<script>
export default {
  props: ['user'],
  methods: {
    async logout() {
      await fetch('http://localhost:8000/logout', {
        method: 'POST',
        credentials: 'include'
      })
      location.reload()
    }
  }
}
</script>

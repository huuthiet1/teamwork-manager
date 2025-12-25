<template>
  <div class="bg-dark text-white p-3" style="width:220px;height:100vh">
    <h6 class="mb-4">GROUP TASK</h6>

    <ul class="nav flex-column">
      <!-- DASHBOARD -->
      <li class="nav-item mb-2">
        <router-link
          to="/dashboard"
          class="nav-link text-white"
          active-class="fw-bold"
        >
          Dashboard
        </router-link>
      </li>

      <!-- GROUP -->
      <li class="nav-item mb-2">
        <router-link
          to="/groups"
          class="nav-link text-white"
          active-class="fw-bold"
        >
          Nhóm
        </router-link>
      </li>

      <!-- TASK (chỉ bật khi đã chọn group) -->
      <li class="nav-item mb-2">
        <router-link
          to="/tasks"
          class="nav-link text-white"
          :class="{ disabled: !hasGroup }"
        >
          Tasks
        </router-link>
      </li>

      <!-- CHAT (chỉ bật khi đã chọn group) -->
      <li class="nav-item">
        <router-link
          to="/chat"
          class="nav-link text-white"
          :class="{ disabled: !hasGroup }"
        >
          Chat
        </router-link>
      </li>
    </ul>

    <!-- GROUP INFO -->
    <div v-if="hasGroup" class="mt-4 small text-muted">
      <div>Nhóm hiện tại:</div>
      <strong class="text-white">{{ currentGroup.name }}</strong>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    currentGroup() {
      return this.$store.state.currentGroup
    },
    hasGroup() {
      return !!this.currentGroup
    },
  },
}
</script>

<style scoped>
.nav-link.disabled {
  pointer-events: none;
  opacity: 0.5;
}
</style>

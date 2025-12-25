<template>
  <div class="trello-board" v-if="group">
    <!-- BOARD HEADER -->
    <div class="board-header">
      <div class="board-info">
        <h1>{{ group.name }}</h1>
        <p>{{ group.description || 'Không có mô tả' }}</p>

        <div class="board-meta">
          <span>👥 {{ membersCount }}</span>
          <span>🔑 {{ group.group_code }}</span>
        </div>
      </div>

      <!-- ACTIONS -->
      <div class="board-actions" v-if="isLeader">
        <button class="btn primary" @click="goCreateTask">
          + Thêm thẻ
        </button>
        <button class="btn ghost" @click="goSettings">
          ⚙️
        </button>
      </div>
    </div>

    <!-- BOARD NAV -->
    <div class="board-nav">
      <button @click="goTasks">Bảng</button>
      <button @click="goChat">Chat</button>
      <button @click="goMembers">Thành viên</button>
      <button @click="goReports">Báo cáo</button>
      <button @click="goAi">AI</button>
    </div>

    <!-- QUICK INFO (Trello-style nhỏ gọn) -->
    <div class="board-stats">
      <span>📋 {{ stats.totalTasks }} thẻ</span>
      <span>⏳ {{ stats.doing }} đang làm</span>
      <span>⚠️ {{ stats.late }} trễ</span>
    </div>
  </div>

  <!-- LOADING -->
  <div v-else class="loading">
    Đang tải board...
  </div>
</template>
<script> 
   import groupApi from '@/api/group'

export default {
  name: 'GroupDetail',

  data() {
    return {
      group: null,
      membersCount: 0,

      stats: {
        totalTasks: 0,
        doing: 0,
        late: 0,
      },

      currentUserId: null,
    }
  },

  async mounted() {
    this.currentUserId = Number(
      localStorage.getItem('user_id')
    )
    await this.fetchGroup()
  },

  methods: {
    async fetchGroup() {
      try {
        const groupId = this.$route.params.groupId
        const res = await groupApi.detail(groupId)

        this.group = res.data.group || res.data

        // Lưu context nhóm (giống Trello lưu board hiện tại)
        localStorage.setItem(
          'currentGroupId',
          this.group.id
        )

        // Thống kê cơ bản
        this.membersCount =
          this.group.members?.length ||
          this.group.stats?.active_members ||
          0

        this.stats.totalTasks =
          this.group.tasks?.length || 0

      } catch (e) {
        alert('Không thể tải nhóm')
        this.$router.push('/dashboard/groups')
      }
    },

    /* ===== NAVIGATION (Board Tabs) ===== */
    goTasks() {
      this.$router.push('/tasks')
    },

    goChat() {
      this.$router.push('/chat')
    },

    goMembers() {
      this.$router.push(
        `/dashboard/groups/${this.group.id}/members`
      )
    },

    goReports() {
      this.$router.push('/reports')
    },

    goAi() {
      this.$router.push('/ai')
    },

    goSettings() {
      this.$router.push(
        `/dashboard/groups/${this.group.id}/settings`
      )
    },

    goCreateTask() {
      this.$router.push('/tasks/create')
    },
  },

  computed: {
    isLeader() {
      return (
        this.group &&
        Number(this.group.leader_id) ===
          Number(this.currentUserId)
      )
    },
  },
}


</script>
<style scoped>
    .trello-board {
  background: #f4f5f7;
  min-height: 100vh;
  padding: 16px 24px;
}

/* HEADER */
.board-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 14px;
}

.board-info h1 {
  font-size: 20px;
  font-weight: 700;
}
.board-info p {
  font-size: 13px;
  color: #5e6c84;
  margin-top: 4px;
}

.board-meta {
  margin-top: 6px;
  display: flex;
  gap: 14px;
  font-size: 12px;
  color: #6b7280;
}

/* ACTIONS */
.board-actions {
  display: flex;
  gap: 8px;
}

/* BUTTON */
.btn {
  padding: 6px 12px;
  border-radius: 4px;
  border: none;
  font-weight: 600;
  cursor: pointer;
}
.primary {
  background: #026aa7;
  color: white;
}
.primary:hover {
  background: #055a8c;
}
.ghost {
  background: transparent;
  color: #026aa7;
}
.ghost:hover {
  background: rgba(2,106,167,.12);
}

/* NAV */
.board-nav {
  display: flex;
  gap: 6px;
  margin-bottom: 10px;
}
.board-nav button {
  background: transparent;
  border: none;
  padding: 6px 10px;
  font-size: 13px;
  font-weight: 600;
  color: #5e6c84;
  border-radius: 4px;
  cursor: pointer;
}
.board-nav button:hover {
  background: rgba(9,30,66,.08);
}

/* STATS */
.board-stats {
  display: flex;
  gap: 16px;
  font-size: 12px;
  color: #5e6c84;
  margin-bottom: 14px;
}

/* LOADING */
.loading {
  padding: 60px;
  text-align: center;
  color: #6b7280;
}

</style>
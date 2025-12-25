<template>
  <div class="ai-dashboard">
    <!-- HEADER -->
    <div class="header">
      <div>
        <h1>🤖 Trợ lý AI</h1>
        <p>Gợi ý & nhắc nhở thông minh cho hôm nay</p>
      </div>
    </div>

    <!-- EMPTY -->
    <div
      v-if="!loading && suggestions.length === 0"
      class="empty"
    >
      🎉 Hôm nay bạn không có việc cần nhắc nhở!
    </div>

    <!-- LOADING -->
    <div v-if="loading" class="loading">
      AI đang phân tích dữ liệu...
    </div>

    <!-- SUGGESTIONS -->
    <div class="list">
      <div
        v-for="(s, i) in suggestions"
        :key="i"
        class="card"
        :class="s.priority"
      >
        <div class="icon">
          {{ iconByType(s.type) }}
        </div>

        <div class="content">
          <p class="message">
            {{ s.message }}
          </p>

          <div class="actions">
            <button
              v-if="s.task_id"
              class="btn"
              @click="goTask(s.task_id)"
            >
              Xem nhiệm vụ
            </button>

            <button
              v-if="s.group_id && s.type === 'chat_unread'"
              class="btn outline"
              @click="goChat(s.group_id)"
            >
              Vào chat
            </button>
          </div>
        </div>

        <span class="priority">
          {{ s.priority.toUpperCase() }}
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/api/api'

export default {
  name: 'AiDashboard',

  data() {
    return {
      loading: false,
      suggestions: [],
    }
  },

  async mounted() {
    await this.fetchAiSuggestions()
  },

  methods: {
    async fetchAiSuggestions() {
      this.loading = true
      try {
        const res = await api.get('/ai/dashboard')
        this.suggestions = res.data.suggestions || []
      } catch (e) {
        alert('Không thể tải gợi ý AI')
      } finally {
        this.loading = false
      }
    },

    goTask(taskId) {
      this.$router.push(`/tasks/${taskId}`)
    },

    goChat(groupId) {
      // đảm bảo context nhóm
      localStorage.setItem('currentGroupId', groupId)
      this.$router.push('/chat')
    },

    iconByType(type) {
      switch (type) {
        case 'task_deadline':
          return '⏰'
        case 'task_late':
          return '⚠️'
        case 'task_split':
          return '🧩'
        case 'chat_unread':
          return '💬'
        default:
          return '🤖'
      }
    },
  },
}
</script>

<style scoped>
.ai-dashboard {
  max-width: 900px;
  margin: 0 auto;
}

/* HEADER */
.header {
  margin-bottom: 24px;
}
.header h1 {
  font-size: 26px;
  font-weight: 800;
}
.header p {
  color: #64748b;
}

/* LIST */
.list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

/* CARD */
.card {
  background: white;
  border-radius: 14px;
  padding: 16px;
  display: flex;
  gap: 14px;
  align-items: flex-start;
  position: relative;
}

/* PRIORITY */
.card.high {
  border-left: 4px solid #dc2626;
}
.card.normal {
  border-left: 4px solid #2563eb;
}
.card.low {
  border-left: 4px solid #64748b;
}

.priority {
  position: absolute;
  top: 14px;
  right: 14px;
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
}

/* ICON */
.icon {
  font-size: 26px;
}

/* CONTENT */
.content {
  flex: 1;
}
.message {
  font-size: 15px;
  margin-bottom: 10px;
}

/* ACTION */
.actions {
  display: flex;
  gap: 10px;
}
.btn {
  padding: 6px 12px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  background: #2563eb;
  color: white;
  font-size: 13px;
}
.btn.outline {
  background: white;
  border: 1px solid #2563eb;
  color: #2563eb;
}

/* EMPTY */
.empty {
  background: white;
  padding: 40px;
  border-radius: 14px;
  text-align: center;
  color: #64748b;
}

/* LOADING */
.loading {
  text-align: center;
  padding: 40px;
  color: #64748b;
}
</style>

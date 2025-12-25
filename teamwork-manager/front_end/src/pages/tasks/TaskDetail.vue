<template>
  <div class="task-detail" v-if="task">
    <h1>{{ task.title }}</h1>
    <p>{{ task.description }}</p>

    <div class="meta">
      <span>⏰ {{ format(task.deadline) }}</span>
      <span>🔥 Độ khó: {{ task.difficulty }}</span>
      <span class="badge" :class="task.status">
        {{ task.status }}
      </span>
    </div>

    <div class="card">
      <h3>Thành viên được giao</h3>

      <table>
        <thead>
          <tr>
            <th>Tên</th>
            <th>Trạng thái</th>
            <th v-if="isAssignee">Hành động</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="a in task.assignments" :key="a.user_id">
            <td>{{ a.user.name }}</td>
            <td>{{ a.status }}</td>

            <td v-if="isAssignee && a.user_id === currentUserId">
              <button
                v-if="a.status !== 'done'"
                class="btn small"
                @click="markDone"
              >
                Hoàn thành
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div v-else class="loading">
    Đang tải nhiệm vụ...
  </div>
</template>

<script>
import taskApi from '@/api/task'

export default {
  name: 'TaskDetail',

  data() {
    return {
      task: null,
      currentUserId: null,
    }
  },

  async mounted() {
    this.currentUserId = Number(localStorage.getItem('user_id'))
    await this.fetchTask()
  },

  methods: {
    async fetchTask() {
      try {
        const res = await taskApi.detail(
          this.$route.params.taskId
        )
        this.task = res.data.task || res.data
      } catch {
        alert('Không thể tải nhiệm vụ')
        this.$router.push('/tasks')
      }
    },

    async markDone() {
      try {
        await taskApi.markDone(this.task.id)
        alert('Đã hoàn thành')
        await this.fetchTask()
      } catch {
        alert('Không thể cập nhật')
      }
    },

    format(d) {
      return new Date(d).toLocaleString()
    },
  },

  computed: {
    isAssignee() {
      return this.task.assignments.some(
        a => a.user_id === this.currentUserId
      )
    },
  },
}
</script>

<style scoped>
.task-detail {
  max-width: 900px;
  margin: auto;
}
.meta {
  display: flex;
  gap: 20px;
  margin: 14px 0;
}
.card {
  background: white;
  padding: 20px;
  border-radius: 14px;
}
.badge {
  padding: 4px 10px;
  border-radius: 999px;
  color: white;
}
.badge.doing {
  background: #2563eb;
}
.badge.done {
  background: #16a34a;
}
.btn.small {
  padding: 6px 12px;
}
</style>

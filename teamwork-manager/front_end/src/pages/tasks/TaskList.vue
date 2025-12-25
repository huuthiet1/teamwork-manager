<template>
  <div class="task-list" v-if="groupId">
    <!-- HEADER -->
    <div class="header">
      <div>
        <h1>Nhiệm vụ</h1>
        <p>Danh sách nhiệm vụ của nhóm</p>
      </div>

      <button
        v-if="isLeader"
        class="btn primary"
        @click="goCreate"
      >
        + Tạo nhiệm vụ
      </button>
    </div>

    <!-- FILTER -->
    <div class="filters">
      <button
        v-for="s in statuses"
        :key="s.value"
        :class="['filter', { active: filter === s.value }]"
        @click="filter = s.value"
      >
        {{ s.label }}
      </button>
    </div>

    <!-- EMPTY -->
    <div v-if="!loading && filteredTasks.length === 0" class="empty">
      <p>Chưa có nhiệm vụ nào</p>
    </div>

    <!-- TASK TABLE -->
    <div v-else class="card">
      <table>
        <thead>
          <tr>
            <th>Tiêu đề</th>
            <th>Độ khó</th>
            <th>Deadline</th>
            <th>Trạng thái</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="task in filteredTasks"
            :key="task.id"
            @click="openTask(task)"
          >
            <td>{{ task.title }}</td>
            <td>{{ task.difficulty }}</td>
            <td>{{ formatDate(task.deadline) }}</td>
            <td>
              <span
                class="badge"
                :class="task.status"
              >
                {{ task.status }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- NO GROUP -->
  <div v-else class="empty">
    <p>Vui lòng chọn nhóm trước</p>
  </div>
</template>

<script>
import taskApi from '@/api/task'
import groupApi from '@/api/group'

export default {
  name: 'TaskList',

  data() {
    return {
      tasks: [],
      loading: false,
      filter: 'all',

      statuses: [
        { label: 'Tất cả', value: 'all' },
        { label: 'Đang làm', value: 'doing' },
        { label: 'Hoàn thành', value: 'done' },
        { label: 'Trễ hạn', value: 'late' },
        { label: 'Đã huỷ', value: 'cancelled' },
      ],

      groupId: null,
      currentUserId: null,
      leaderId: null,
    }
  },

  async mounted() {
    this.groupId = localStorage.getItem('currentGroupId')
    this.currentUserId = Number(
      localStorage.getItem('user_id')
    )

    if (this.groupId) {
      await this.fetchGroup()
      await this.fetchTasks()
    }
  },

  methods: {
    async fetchGroup() {
      const res = await groupApi.detail(this.groupId)
      this.leaderId = res.data.group?.leader_id || res.data.leader_id
    },

    async fetchTasks() {
      this.loading = true
      try {
        const res = await taskApi.list({
          group_id: this.groupId,
        })
        this.tasks = res.data.tasks || res.data
      } catch (e) {
        alert('Không thể tải nhiệm vụ')
      } finally {
        this.loading = false
      }
    },

    openTask(task) {
      this.$router.push(`/tasks/${task.id}`)
    },

    goCreate() {
      this.$router.push('/tasks/create')
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString()
    },
  },

  computed: {
    isLeader() {
      return (
        Number(this.currentUserId) ===
        Number(this.leaderId)
      )
    },

    filteredTasks() {
      if (this.filter === 'all') {
        return this.tasks
      }
      return this.tasks.filter(
        t => t.status === this.filter
      )
    },
  },
}
</script>

<style scoped>
.task-list {
  max-width: 1100px;
  margin: 0 auto;
}

/* HEADER */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}
.header h1 {
  font-size: 26px;
  font-weight: 800;
}
.header p {
  color: #64748b;
}

/* FILTER */
.filters {
  display: flex;
  gap: 10px;
  margin-bottom: 16px;
}
.filter {
  padding: 8px 14px;
  border-radius: 999px;
  border: 1px solid #cbd5e1;
  background: white;
  cursor: pointer;
}
.filter.active {
  background: #2563eb;
  color: white;
  border-color: #2563eb;
}

/* CARD */
.card {
  background: white;
  border-radius: 14px;
  padding: 20px;
}

/* TABLE */
table {
  width: 100%;
  border-collapse: collapse;
}
tr {
  cursor: pointer;
}
th,
td {
  padding: 14px 12px;
  border-bottom: 1px solid #e5e7eb;
}
th {
  font-size: 13px;
  color: #64748b;
}

/* BADGE */
.badge {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  color: white;
}
.badge.doing {
  background: #2563eb;
}
.badge.done {
  background: #16a34a;
}
.badge.late {
  background: #dc2626;
}
.badge.cancelled {
  background: #64748b;
}

/* EMPTY */
.empty {
  background: white;
  padding: 40px;
  border-radius: 14px;
  text-align: center;
  color: #64748b;
}
</style>

<template>
  <div class="contribution" v-if="groupId">
    <!-- HEADER -->
    <div class="header">
      <div>
        <h1>📊 Đóng góp nhóm</h1>
        <p>Thống kê mức độ đóng góp của từng thành viên</p>
      </div>

      <button
        v-if="isLeader"
        class="btn primary"
        @click="recalculate"
      >
        🔄 Tính lại điểm
      </button>
    </div>

    <!-- CURRENT SCORES -->
    <div class="card">
      <h3>Điểm hiện tại</h3>

      <table>
        <thead>
          <tr>
            <th>Thành viên</th>
            <th>Số nhiệm vụ</th>
            <th>Hoàn thành</th>
            <th>Trễ hạn</th>
            <th>Điểm</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="c in scores" :key="c.user_id">
            <td>{{ c.user.name }}</td>
            <td>{{ c.total_tasks }}</td>
            <td>{{ c.done_tasks }}</td>
            <td>{{ c.late_tasks }}</td>
            <td>
              <span class="score">
                {{ c.score }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- HISTORY -->
    <div class="card history">
      <h3>📜 Lịch sử chấm điểm</h3>

      <div
        v-if="histories.length === 0"
        class="empty"
      >
        Chưa có lịch sử
      </div>

      <ul v-else>
        <li
          v-for="h in histories"
          :key="h.id"
        >
          <strong>{{ h.user.name }}</strong>
          được {{ h.score }} điểm –
          <small>{{ format(h.created_at) }}</small>
        </li>
      </ul>
    </div>
  </div>

  <!-- NO GROUP -->
  <div v-else class="empty">
    ⚠️ Vui lòng chọn nhóm trước
  </div>
</template>

<script>
import contributionApi from '@/api/contribution'
import groupApi from '@/api/group'

export default {
  name: 'ContributionPage',

  data() {
    return {
      groupId: null,
      currentUserId: null,
      leaderId: null,

      scores: [],
      histories: [],
    }
  },

  async mounted() {
    this.groupId = localStorage.getItem('currentGroupId')
    this.currentUserId = Number(
      localStorage.getItem('user_id')
    )

    if (!this.groupId) return

    await this.fetchGroup()
    await this.fetchContribution()
  },

  methods: {
    async fetchGroup() {
      const res = await groupApi.detail(this.groupId)
      this.leaderId =
        res.data.group?.leader_id ||
        res.data.leader_id
    },

    async fetchContribution() {
      try {
        const res = await contributionApi.show({
          group_id: this.groupId,
        })

        this.scores =
          res.data.scores || res.data.current
        this.histories =
          res.data.histories || []
      } catch {
        alert('Không thể tải đóng góp')
      }
    },

    async recalculate() {
      if (!confirm('Tính lại điểm đóng góp?')) return

      try {
        await contributionApi.recalculate({
          group_id: this.groupId,
        })
        alert('Đã tính lại')
        await this.fetchContribution()
      } catch {
        alert('Không thể tính lại')
      }
    },

    format(t) {
      return new Date(t).toLocaleString()
    },
  },

  computed: {
    isLeader() {
      return (
        Number(this.currentUserId) ===
        Number(this.leaderId)
      )
    },
  },
}
</script>

<style scoped>
.contribution {
  max-width: 1000px;
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

/* CARD */
.card {
  background: white;
  border-radius: 14px;
  padding: 20px;
  margin-bottom: 20px;
}

/* TABLE */
table {
  width: 100%;
  border-collapse: collapse;
}
th,
td {
  padding: 12px;
  border-bottom: 1px solid #e5e7eb;
}
th {
  font-size: 13px;
  color: #64748b;
  text-transform: uppercase;
}

/* SCORE */
.score {
  font-weight: 800;
  color: #2563eb;
}

/* HISTORY */
.history ul {
  padding-left: 16px;
}
.history li {
  margin-bottom: 8px;
}

/* BUTTON */
.btn.primary {
  background: #2563eb;
  color: white;
  padding: 10px 16px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
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

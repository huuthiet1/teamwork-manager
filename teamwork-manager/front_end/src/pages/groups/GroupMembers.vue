<template>
  <div class="group-members" v-if="group">
    <!-- HEADER -->
    <div class="header">
      <div>
        <h1>Thành viên nhóm</h1>
        <p>{{ group.name }}</p>
      </div>

      <button class="btn outline" @click="goBack">
        ← Quay lại nhóm
      </button>
    </div>

    <!-- MEMBER LIST -->
    <div class="card">
      <table>
        <thead>
          <tr>
            <th>Họ tên</th>
            <th>Vai trò</th>
            <th>Trạng thái</th>
            <th v-if="isLeader">Hành động</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="m in members" :key="m.user_id">
            <td>{{ m.user.name }}</td>

            <td>
              <span
                class="role"
                :class="m.role"
              >
                {{ m.role === 'leader' ? 'Leader' : 'Member' }}
              </span>
            </td>

            <td>
              <span
                class="status"
                :class="{ active: m.is_active }"
              >
                {{ m.is_active ? 'Đang hoạt động' : 'Đã rời nhóm' }}
              </span>
            </td>

            <!-- ACTION -->
            <td v-if="isLeader">
              <button
                v-if="m.role !== 'leader' && m.is_active"
                class="btn small"
                @click="transferLeader(m.user_id)"
              >
                Chuyển quyền
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- LOADING -->
  <div v-else class="loading">
    Đang tải danh sách thành viên...
  </div>
</template>

<script>
import groupApi from '@/api/group'

export default {
  name: 'GroupMembers',

  data() {
    return {
      group: null,
      members: [],
      currentUserId: null,
    }
  },

  async mounted() {
    this.currentUserId = Number(localStorage.getItem('user_id'))
    await this.fetchGroup()
  },

  methods: {
    async fetchGroup() {
      try {
        const groupId = this.$route.params.groupId
        const res = await groupApi.detail(groupId)

        this.group = res.data.group || res.data
        this.members = this.group.members || []
      } catch (e) {
        alert('Không thể tải thành viên nhóm')
        this.$router.push('/dashboard/groups')
      }
    },

    async transferLeader(newLeaderId) {
      if (
        !confirm(
          'Bạn có chắc muốn chuyển quyền leader cho thành viên này?'
        )
      ) {
        return
      }

      try {
        await groupApi.transferLeader(
          this.group.id,
          newLeaderId
        )

        alert('Đã chuyển quyền leader')
        await this.fetchGroup()
      } catch (e) {
        alert('Chuyển quyền thất bại')
      }
    },

    goBack() {
      this.$router.push(
        `/dashboard/groups/${this.group.id}`
      )
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
.group-members {
  max-width: 1000px;
  margin: 0 auto;
}

/* HEADER */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
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
}

/* TABLE */
table {
  width: 100%;
  border-collapse: collapse;
}
th,
td {
  text-align: left;
  padding: 14px 12px;
  border-bottom: 1px solid #e5e7eb;
}
th {
  font-size: 13px;
  color: #64748b;
  text-transform: uppercase;
}

/* ROLE */
.role {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
}
.role.leader {
  background: #2563eb;
  color: white;
}
.role.member {
  background: #e5e7eb;
  color: #1e293b;
}

/* STATUS */
.status {
  font-size: 13px;
  color: #64748b;
}
.status.active {
  color: #16a34a;
  font-weight: 600;
}

/* BUTTON */
.btn {
  padding: 8px 14px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
}
.btn.small {
  font-size: 13px;
}
.outline {
  background: white;
  border: 1px solid #2563eb;
  color: #2563eb;
}

/* LOADING */
.loading {
  text-align: center;
  padding: 60px;
  color: #64748b;
}
</style>

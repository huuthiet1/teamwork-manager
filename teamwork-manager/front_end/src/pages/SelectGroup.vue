<template>
  <div class="select-group">
    <!-- HEADER -->
    <div class="header">
      <h1>👥 Chọn nhóm làm việc</h1>
      <p>
        Bạn cần chọn một nhóm trước khi tiếp tục
      </p>
    </div>

    <!-- ACTIONS -->
    <div class="actions">
      <button class="btn primary" @click="showCreate = true">
        ➕ Tạo nhóm
      </button>
      <button class="btn ghost" @click="showJoin = true">
        🔑 Tham gia bằng OTP
      </button>
    </div>

    <!-- GROUP LIST -->
    <div class="card">
      <div
        v-if="groups.length === 0"
        class="empty"
      >
        <p>😢 Bạn chưa tham gia nhóm nào</p>
        <p>Hãy tạo hoặc tham gia nhóm để bắt đầu</p>
      </div>

      <div
        v-for="g in groups"
        :key="g.id"
        class="group"
        @click="selectGroup(g)"
      >
        <div>
          <strong>{{ g.name }}</strong>
          <small>
            {{ g.description || 'Không có mô tả' }}
          </small>
        </div>

        <span
          class="role"
          :class="g.pivot.role"
        >
          {{ g.pivot.role }}
        </span>
      </div>
    </div>

    <!-- CREATE MODAL -->
    <div v-if="showCreate" class="modal">
      <div class="modal-card">
        <h3>Tạo nhóm mới</h3>

        <input
          v-model="form.name"
          placeholder="Tên nhóm"
        />
        <textarea
          v-model="form.description"
          placeholder="Mô tả"
        ></textarea>

        <div class="modal-actions">
          <button
            class="btn primary"
            @click="createGroup"
          >
            Tạo
          </button>
          <button
            class="btn ghost"
            @click="showCreate = false"
          >
            Huỷ
          </button>
        </div>
      </div>
    </div>

    <!-- JOIN MODAL -->
    <div v-if="showJoin" class="modal">
      <div class="modal-card">
        <h3>Tham gia nhóm</h3>

        <input
          v-model="otp"
          placeholder="Nhập OTP 6 số"
        />

        <div class="modal-actions">
          <button
            class="btn primary"
            @click="joinGroup"
          >
            Tham gia
          </button>
          <button
            class="btn ghost"
            @click="showJoin = false"
          >
            Huỷ
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import groupApi from '@/api/group'

export default {
  name: 'SelectGroup',

  data() {
    return {
      groups: [],
      showCreate: false,
      showJoin: false,
      otp: '',
      form: {
        name: '',
        description: '',
      },
    }
  },

  async mounted() {
    await this.fetchGroups()
  },

  methods: {
    async fetchGroups() {
      const res = await groupApi.list()
      this.groups = res.data.groups || res.data
    },

    selectGroup(group) {
      localStorage.setItem(
        'currentGroupId',
        group.id
      )
      localStorage.setItem(
        'groupRole',
        group.pivot.role
      )

      this.$router.push('/dashboard')
    },

    async createGroup() {
      if (!this.form.name) return

      try {
        await groupApi.create(this.form)
        this.showCreate = false
        this.form.name = ''
        this.form.description = ''
        await this.fetchGroups()
      } catch {
        alert('Không thể tạo nhóm')
      }
    },

    async joinGroup() {
      try {
        await groupApi.joinByOtp(this.otp)
        this.showJoin = false
        this.otp = ''
        await this.fetchGroups()
      } catch (e) {
        alert(
          e.response?.data?.message ||
            'OTP không hợp lệ'
        )
      }
    },
  },
}
</script>

<style scoped>
.select-group {
  max-width: 800px;
  margin: 0 auto;
}

/* HEADER */
.header {
  margin-bottom: 20px;
}
.header h1 {
  font-size: 26px;
  font-weight: 800;
}
.header p {
  color: #64748b;
}

/* ACTIONS */
.actions {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
}

/* CARD */
.card {
  background: white;
  border-radius: 14px;
  padding: 16px;
}

/* GROUP ITEM */
.group {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px;
  border-bottom: 1px solid #e5e7eb;
  cursor: pointer;
}
.group:last-child {
  border-bottom: none;
}
.group:hover {
  background: #f1f5ff;
}

/* ROLE */
.role {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
}
.role.leader {
  background: #fee2e2;
  color: #dc2626;
}
.role.member {
  background: #e0f2fe;
  color: #0284c7;
}

/* EMPTY */
.empty {
  text-align: center;
  padding: 40px;
  color: #64748b;
}

/* MODAL */
.modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.3);
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal-card {
  background: white;
  padding: 24px;
  border-radius: 16px;
  width: 360px;
}
.modal-card h3 {
  margin-bottom: 14px;
}
.modal-card input,
.modal-card textarea {
  width: 100%;
  padding: 10px;
  margin-bottom: 10px;
  border-radius: 10px;
  border: 1px solid #cbd5e1;
}

/* BUTTON */
.btn {
  padding: 10px 16px;
  border-radius: 10px;
  font-weight: 700;
  border: none;
  cursor: pointer;
}
.btn.primary {
  background: #2563eb;
  color: white;
}
.btn.ghost {
  background: #e5e7eb;
}
.modal-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}
</style>

<template>
  <div class="trello-page">
    <!-- TOP BAR -->
    <div class="top-bar">
      <h1>Danh sách nhóm </h1>

      <div class="actions">
        <button class="btn primary" @click="showCreate = true">
          Tạo nhóm
        </button>
        <button class="btn ghost" @click="showJoin = true">
          Tham gia
        </button>
      </div>
    </div>

    <!-- EMPTY -->
    <div v-if="!loading && groups.length === 0" class="empty">
      <p>Bạn chưa có board nào</p>
      <button class="btn primary" @click="showCreate = true">
        Tạo Nhóm đầu tiên
      </button>
    </div>

    <!-- BOARDS -->
    <div v-if="groups.length" class="board-grid">
      <div
        v-for="group in groups"
        :key="group.id"
        class="board"
        @click="openGroup(group)"
      >
        <div class="board-title">
          {{ group.name }}
        </div>

        <div class="board-desc">
          {{ group.description || 'Không có mô tả' }}
        </div>

        <div class="board-code">
          {{ group.group_code }}
        </div>
      </div>

      <!-- CREATE QUICK BOARD -->
      <div class="board create" @click="showCreate = true">
        + Tạo nhóm mới
      </div>
    </div>

    <!-- CREATE MODAL -->
    <div v-if="showCreate" class="modal">
      <div class="modal-card">
        <h3>Tạo nhóm</h3>

        <input v-model="createForm.name" placeholder="Tên board" />
        <textarea v-model="createForm.description" placeholder="Mô tả" />

        <div class="modal-actions">
          <button class="btn primary" @click="createGroup">
            Tạo
          </button>
          <button class="btn ghost" @click="showCreate = false">
            Huỷ
          </button>
        </div>
      </div>
    </div>

    <!-- JOIN MODAL -->
    <div v-if="showJoin" class="modal">
      <div class="modal-card">
        <h3>Tham gia nhóm</h3>

        <input v-model="joinCode" placeholder="Mã mời 6 số" />

        <div class="modal-actions">
          <button class="btn primary" @click="joinGroup">
            Tham gia
          </button>
          <button class="btn ghost" @click="showJoin = false">
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
  name: 'GroupList',

  data() {
    return {
      groups: [],
      loading: false,

      showCreate: false,
      showJoin: false,

      createForm: {
        name: '',
        description: '',
      },

      joinCode: '',
    }
  },

  async mounted() {
    await this.fetchGroups()
  },

  methods: {
    async fetchGroups() {
      this.loading = true
      try {
        const res = await groupApi.list()
        this.groups = res.data.groups || res.data.data || []
      } catch (e) {
        console.error('Load groups failed', e)
      } finally {
        this.loading = false
      }
    },

    async createGroup() {
      if (!this.createForm.name) return

      try {
        const res = await groupApi.create(this.createForm)
        const group = res.data.group || res.data.data || res.data

        this.groups.push(group)
        this.createForm.name = ''
        this.createForm.description = ''
        this.showCreate = false
      } catch (e) {
        alert('Tạo nhóm thất bại')
      }
    },

    async joinGroup() {
      if (!this.joinCode) return

      try {
        await groupApi.joinByOtp(this.joinCode)
        this.joinCode = ''
        this.showJoin = false
        await this.fetchGroups()
      } catch (e) {
        alert('Mã mời không hợp lệ hoặc đã hết hạn')
      }
    },

    openGroup(group) {
      this.$router.push(`/dashboard/groups/${group.id}`)
    },
  },
}
</script>

<style scoped>
.trello-page {
  background: #f4f5f7;
  min-height: 100vh;
  padding: 20px 24px;
}

/* TOP BAR */
.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}
.top-bar h1 {
  font-size: 22px;
  font-weight: 700;
}

/* BUTTON */
.btn {
  padding: 8px 14px;
  border-radius: 6px;
  font-weight: 600;
  border: none;
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
  background: rgba(2,106,167,.1);
}

/* BOARD GRID */
.board-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 16px;
}

/* BOARD CARD */
.board {
  background: #ffffff;
  border-radius: 6px;
  padding: 12px;
  cursor: pointer;
  min-height: 90px;
  display: flex;
  flex-direction: column;
  transition: background .15s;
}
.board:hover {
  background: #ebecf0;
}

.board-title {
  font-weight: 700;
  font-size: 15px;
  margin-bottom: 6px;
}
.board-desc {
  font-size: 13px;
  color: #5e6c84;
  flex: 1;
}
.board-code {
  font-size: 11px;
  color: #9ca3af;
  margin-top: 8px;
}

/* CREATE BOARD */
.board.create {
  background: rgba(9,30,66,.04);
  color: #5e6c84;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
}
.board.create:hover {
  background: rgba(9,30,66,.08);
}

/* EMPTY */
.empty {
  background: white;
  padding: 40px;
  border-radius: 6px;
  text-align: center;
}

/* MODAL */
.modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.35);
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal-card {
  background: white;
  width: 420px;
  padding: 24px;
  border-radius: 6px;
}
.modal-card input,
.modal-card textarea {
  width: 100%;
  margin-top: 12px;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #d1d5db;
}
.modal-actions {
  margin-top: 18px;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}


</style>

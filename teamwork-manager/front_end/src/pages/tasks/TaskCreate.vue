<template>
  <div class="task-create">
    <h1>Tạo nhiệm vụ mới</h1>

    <div class="card">
      <form @submit.prevent="submit">
        <div class="field">
          <label>Tiêu đề</label>
          <input v-model="form.title" required />
        </div>

        <div class="field">
          <label>Mô tả</label>
          <textarea v-model="form.description" />
        </div>

        <div class="row">
          <div class="field">
            <label>Độ khó (1–5)</label>
            <input
              type="number"
              min="1"
              max="5"
              v-model.number="form.difficulty"
            />
          </div>

          <div class="field">
            <label>Deadline</label>
            <input type="datetime-local" v-model="form.deadline" required />
          </div>
        </div>

        <div class="field">
          <label>Giao cho</label>
          <div class="assignees">
            <label
              v-for="m in members"
              :key="m.user_id"
            >
              <input
                type="checkbox"
                :value="m.user_id"
                v-model="form.assignees"
              />
              {{ m.user.name }}
            </label>
          </div>
        </div>

        <button class="btn primary">Tạo nhiệm vụ</button>
      </form>
    </div>
  </div>
</template>

<script>
import taskApi from '@/api/task'
import groupApi from '@/api/group'

export default {
  name: 'TaskCreate',

  data() {
    return {
      members: [],
      form: {
        title: '',
        description: '',
        difficulty: 1,
        deadline: '',
        assignees: [],
      },
      groupId: null,
    }
  },

  async mounted() {
    this.groupId = localStorage.getItem('currentGroupId')
    if (!this.groupId) {
      alert('Chưa chọn nhóm')
      this.$router.push('/dashboard/groups')
      return
    }

    const res = await groupApi.detail(this.groupId)
    this.members = res.data.group?.members || res.data.members
  },

  methods: {
    async submit() {
      try {
        await taskApi.create({
          group_id: this.groupId,
          ...this.form,
        })

        alert('Tạo nhiệm vụ thành công')
        this.$router.push('/tasks')
      } catch (e) {
        alert('Tạo nhiệm vụ thất bại')
      }
    },
  },
}
</script>

<style scoped>
.task-create {
  max-width: 800px;
  margin: auto;
}
.card {
  background: white;
  padding: 24px;
  border-radius: 14px;
}
.field {
  margin-bottom: 18px;
}
label {
  font-weight: 600;
  margin-bottom: 6px;
  display: block;
}
input,
textarea {
  width: 100%;
  padding: 10px;
}
.row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.assignees label {
  display: block;
  margin-bottom: 6px;
}
.btn.primary {
  background: #2563eb;
  color: white;
  padding: 10px 18px;
  border-radius: 10px;
  border: none;
}
</style>

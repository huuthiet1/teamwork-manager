<template>
  <div class="report-export" v-if="groupId">
    <!-- HEADER -->
    <div class="header">
      <div>
        <h1>📄 Xuất báo cáo nhóm</h1>
        <p>
          Tạo báo cáo PDF tổng hợp nhiệm vụ & đóng góp
        </p>
      </div>
    </div>

    <!-- GROUP INFO -->
    <div class="card">
      <h3>Thông tin nhóm</h3>

      <div class="info">
        <div>
          <strong>Tên nhóm:</strong>
          {{ group.name }}
        </div>
        <div>
          <strong>Mô tả:</strong>
          {{ group.description || '—' }}
        </div>
        <div>
          <strong>Số thành viên:</strong>
          {{ members.length }}
        </div>
      </div>
    </div>

    <!-- ACTION -->
    <div class="card action">
      <p>
        Hệ thống sẽ tạo file PDF bao gồm:
      </p>

      <ul>
        <li>📋 Danh sách nhiệm vụ</li>
        <li>👥 Phân công & trạng thái</li>
        <li>📊 Mức độ đóng góp</li>
        <li>🕒 Thời gian xuất báo cáo</li>
      </ul>

      <button
        class="btn primary"
        :disabled="loading"
        @click="exportPdf"
      >
        {{ loading ? 'Đang tạo PDF...' : 'Xuất báo cáo PDF' }}
      </button>
    </div>
  </div>

  <!-- NO GROUP -->
  <div v-else class="empty">
    ⚠️ Vui lòng chọn nhóm trước khi xuất báo cáo
  </div>
</template>

<script>
import groupApi from '@/api/group'
import api from '@/api/api'

export default {
  name: 'ReportExport',

  data() {
    return {
      groupId: null,
      group: {},
      members: [],
      loading: false,
    }
  },

  async mounted() {
    this.groupId =
      localStorage.getItem('currentGroupId')

    if (!this.groupId) return

    await this.fetchGroup()
  },

  methods: {
    async fetchGroup() {
      try {
        const res = await groupApi.detail(this.groupId)
        this.group = res.data.group || res.data
        this.members =
          res.data.members || []
      } catch {
        alert('Không thể tải thông tin nhóm')
      }
    },

    async exportPdf() {
      this.loading = true

      try {
        const res = await api.get(
          '/reports/export',
          {
            params: { group_id: this.groupId },
            responseType: 'blob',
          }
        )

        const blob = new Blob(
          [res.data],
          { type: 'application/pdf' }
        )

        const url =
          window.URL.createObjectURL(blob)

        const a =
          document.createElement('a')

        a.href = url
        a.download =
          `report_group_${this.groupId}.pdf`

        a.click()
        window.URL.revokeObjectURL(url)
      } catch (e) {
        alert(
          e.response?.data?.message ||
            'Không thể xuất báo cáo'
        )
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<style scoped>
.report-export {
  max-width: 900px;
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

/* CARD */
.card {
  background: white;
  border-radius: 14px;
  padding: 20px;
  margin-bottom: 20px;
}

/* INFO */
.info div {
  margin-bottom: 8px;
}

/* ACTION */
.action ul {
  padding-left: 20px;
  margin: 14px 0;
}
.action li {
  margin-bottom: 6px;
}

/* BUTTON */
.btn.primary {
  padding: 12px 20px;
  border-radius: 12px;
  background: #2563eb;
  color: white;
  border: none;
  font-weight: 800;
  cursor: pointer;
}
.btn.primary:disabled {
  opacity: 0.6;
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

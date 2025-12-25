<template>
  <div class="group-settings" v-if="group">
    <!-- HEADER -->
    <div class="header">
      <div>
        <h1>Cài đặt nhóm</h1>
        <p>{{ group.name }}</p>
      </div>

      <button class="btn outline" @click="goBack">
        ← Quay lại nhóm
      </button>
    </div>

    <!-- INVITE OTP -->
    <div class="card">
      <h3>🔑 Mời thành viên bằng mã OTP</h3>
      <p>
        Leader có thể tạo mã mời để thành viên khác
        tham gia nhóm (có hiệu lực 10 phút).
      </p>

      <div class="otp-box">
        <button class="btn primary" @click="generateOtp">
          Tạo mã mời
        </button>

        <div v-if="otp" class="otp">
          <strong>{{ otp }}</strong>
          <small>Hết hạn lúc {{ expiresAt }}</small>
        </div>
      </div>
    </div>

    <!-- DANGER ZONE -->
    <div class="card danger">
      <h3>⚠️ Vùng nguy hiểm</h3>
      <p>
        Đóng nhóm sẽ <b>không thể hoàn tác</b>.
        Tất cả dữ liệu vẫn được giữ lại cho báo cáo.
      </p>

      <button class="btn danger-btn" @click="closeGroup">
        Đóng nhóm
      </button>
    </div>
  </div>

  <!-- LOADING -->
  <div v-else class="loading">
    Đang tải cài đặt nhóm...
  </div>
</template>

<script>
import groupApi from '@/api/group'

export default {
  name: 'GroupSettings',

  data() {
    return {
      group: null,
      otp: null,
      expiresAt: null,
    }
  },

  async mounted() {
    await this.fetchGroup()
  },

  methods: {
    async fetchGroup() {
      try {
        const groupId = this.$route.params.groupId
        const res = await groupApi.detail(groupId)
        this.group = res.data.group || res.data
      } catch (e) {
        alert('Không thể tải nhóm')
        this.$router.push('/dashboard/groups')
      }
    },

    async generateOtp() {
      try {
        const res = await groupApi.generateInvite(
          this.group.id
        )

        const invite =
          res.data.invite || res.data

        this.otp = invite.otp_code
        this.expiresAt = new Date(
          invite.expires_at
        ).toLocaleTimeString()
      } catch (e) {
        alert('Không thể tạo mã mời')
      }
    },

    async closeGroup() {
      if (
        !confirm(
          'Bạn có chắc chắn muốn ĐÓNG nhóm này?'
        )
      ) {
        return
      }

      try {
        await groupApi.close(this.group.id)
        alert('Nhóm đã được đóng')
        this.$router.push('/dashboard/groups')
      } catch (e) {
        alert('Đóng nhóm thất bại')
      }
    },

    goBack() {
      this.$router.push(
        `/dashboard/groups/${this.group.id}`
      )
    },
  },
}
</script>

<style scoped>
.group-settings {
  max-width: 900px;
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
  margin-bottom: 20px;
}
.card h3 {
  margin-bottom: 6px;
}
.card p {
  color: #64748b;
  margin-bottom: 14px;
}

/* OTP */
.otp-box {
  display: flex;
  align-items: center;
  gap: 20px;
}
.otp {
  background: #f8fafc;
  padding: 12px 18px;
  border-radius: 12px;
  border: 1px dashed #2563eb;
}
.otp strong {
  font-size: 22px;
  color: #2563eb;
}
.otp small {
  display: block;
  margin-top: 4px;
  color: #64748b;
}

/* BUTTON */
.btn {
  padding: 10px 16px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
}
.primary {
  background: #2563eb;
  color: white;
}
.outline {
  background: white;
  border: 1px solid #2563eb;
  color: #2563eb;
}

/* DANGER */
.card.danger {
  border-left: 4px solid #dc2626;
}
.danger-btn {
  background: #dc2626;
  color: white;
}

/* LOADING */
.loading {
  text-align: center;
  padding: 60px;
  color: #64748b;
}
</style>

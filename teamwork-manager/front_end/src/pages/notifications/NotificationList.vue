<template>
  <div class="notification-page">
    <!-- HEADER -->
    <div class="header">
      <div>
        <h1>🔔 Thông báo</h1>
        <p>Theo dõi nhắc nhở & hoạt động của bạn</p>
      </div>

      <button
        v-if="notifications.length"
        class="btn ghost"
        @click="markAll"
      >
        Đánh dấu tất cả đã đọc
      </button>
    </div>

    <!-- LIST -->
    <div class="card">
      <div
        v-if="notifications.length === 0"
        class="empty"
      >
        🎉 Bạn không có thông báo nào
      </div>

      <div
        v-for="n in notifications"
        :key="n.id"
        class="notification"
        :class="{ unread: !n.is_read }"
        @click="markOne(n)"
      >
        <div class="left">
          <span class="dot" v-if="!n.is_read"></span>
          <div class="content">
            <p class="message">{{ n.message }}</p>
            <small>
              {{ format(n.created_at) }}
            </small>
          </div>
        </div>

        <span class="priority" :class="n.priority">
          {{ n.priority }}
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import notificationApi from '@/api/notification'

export default {
  name: 'NotificationList',

  data() {
    return {
      notifications: [],
    }
  },

  async mounted() {
    await this.fetch()
  },

  methods: {
    async fetch() {
      const res = await notificationApi.list()
      this.notifications =
        res.data.data || res.data
    },

    async markOne(n) {
      if (n.is_read) return

      await notificationApi.read(n.id)
      n.is_read = 1
    },

    async markAll() {
      if (!confirm('Đánh dấu tất cả đã đọc?')) return
      await notificationApi.readAll()
      this.notifications.forEach(
        n => (n.is_read = 1)
      )
    },

    format(t) {
      return new Date(t).toLocaleString()
    },
  },
}
</script>

<style scoped>
.notification-page {
  max-width: 900px;
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
  padding: 12px;
}

/* ITEM */
.notification {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px;
  border-bottom: 1px solid #e5e7eb;
  cursor: pointer;
}
.notification:last-child {
  border-bottom: none;
}

.notification.unread {
  background: #f1f5ff;
}

/* LEFT */
.left {
  display: flex;
  gap: 12px;
}
.dot {
  width: 8px;
  height: 8px;
  background: #2563eb;
  border-radius: 50%;
  margin-top: 6px;
}

.message {
  font-weight: 600;
}
small {
  color: #64748b;
}

/* PRIORITY */
.priority {
  font-size: 12px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 999px;
  text-transform: uppercase;
}
.priority.high {
  background: #fee2e2;
  color: #dc2626;
}
.priority.normal {
  background: #e0f2fe;
  color: #0284c7;
}
.priority.low {
  background: #ecfeff;
  color: #0f766e;
}

/* BUTTON */
.btn {
  padding: 10px 14px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
}
.btn.ghost {
  background: #e5e7eb;
}

/* EMPTY */
.empty {
  text-align: center;
  padding: 40px;
  color: #64748b;
}
</style>

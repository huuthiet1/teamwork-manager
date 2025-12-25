<template>
  <div class="submission-detail" v-if="submission">
    <!-- HEADER -->
    <div class="header">
      <div>
        <h1>📤 Bài nộp</h1>
        <p>
          Nhiệm vụ:
          <strong>{{ submission.task.title }}</strong>
        </p>
      </div>
    </div>

    <!-- INFO -->
    <div class="card">
      <div class="info">
        <div>
          <strong>Người nộp:</strong>
          {{ submission.user.name }}
        </div>
        <div>
          <strong>Deadline:</strong>
          {{ format(submission.task.deadline) }}
        </div>
        <div>
          <strong>Trạng thái:</strong>
          <span
            class="badge"
            :class="submission.is_late ? 'late' : 'on-time'"
          >
            {{ submission.is_late ? 'Trễ hạn' : 'Đúng hạn' }}
          </span>
        </div>
      </div>
    </div>

    <!-- VERSIONS -->
    <div class="card">
      <h3>📚 Lịch sử nộp bài</h3>

      <div
        v-for="v in submission.versions"
        :key="v.id"
        class="version"
      >
        <div class="v-header">
          <strong>Version {{ v.version_no }}</strong>
          <span>
            {{ format(v.created_at) }}
          </span>
        </div>

        <p v-if="v.content" class="note">
          {{ v.content }}
        </p>

        <!-- FILES -->
        <div class="files">
          <div
            v-for="f in v.files"
            :key="f.id"
            class="file"
          >
            <a
              :href="fileUrl(f.file_path)"
              target="_blank"
            >
              📎 {{ fileName(f.file_path) }}
            </a>
          </div>

          <div
            v-if="v.files.length === 0"
            class="empty"
          >
            Không có file đính kèm
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- EMPTY -->
  <div v-else class="empty">
    Không tìm thấy bài nộp
  </div>
</template>

<script>
import api from '@/api/api'

export default {
  name: 'SubmissionDetail',

  data() {
    return {
      submission: null,
    }
  },

  async mounted() {
    await this.fetch()
  },

  methods: {
    async fetch() {
      try {
        const res = await api.get(
          `/submissions/${this.$route.params.id}`
        )
        this.submission = res.data
      } catch {
        this.$router.push('/403')
      }
    },

    format(t) {
      return new Date(t).toLocaleString()
    },

    fileUrl(path) {
      return `http://localhost/my-project/backend/public/storage/${path}`
    },

    fileName(path) {
      return path.split('/').pop()
    },
  },
}
</script>

<style scoped>
.submission-detail {
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

/* BADGE */
.badge {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
}
.on-time {
  background: #dcfce7;
  color: #166534;
}
.late {
  background: #fee2e2;
  color: #991b1b;
}

/* VERSION */
.version {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 16px;
}
.v-header {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  margin-bottom: 10px;
}
.note {
  margin-bottom: 10px;
}

/* FILES */
.files {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.file a {
  text-decoration: none;
  color: #2563eb;
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

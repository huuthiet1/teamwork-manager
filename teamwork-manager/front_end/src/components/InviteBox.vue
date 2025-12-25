<template>
  <div>
    <button class="btn btn-warning" @click="createInvite">
      Tạo mã mời
    </button>

    <div v-if="otp" class="alert alert-success mt-2">
      Mã OTP: <strong>{{ otp }}</strong>
    </div>
  </div>
</template>

<script>
import { createInvite } from '@/api/group'

export default {
  props: {
    groupId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      otp: null,
    }
  },
  methods: {
    async createInvite() {
      try {
        const text = await createInvite(this.groupId)
        this.otp = text.match(/\d{6}/)[0]
      } catch {
        alert('Không thể tạo OTP')
      }
    },
  },
}
</script>

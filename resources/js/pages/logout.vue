<template>
  <Loader v-if="isLoading" />
</template>

<script>
import { ref, onMounted } from 'vue'
import { useAuthStore } from "@/store/auth"
import { useRouter } from 'vue-router'
import Loader from "@/components/Loader.vue"

export default {
  setup() {
    const isLoading = ref(false)
    const authStore = useAuthStore()
    const router = useRouter()

    onMounted(async () => {
      isLoading.value = true
      await logout()
      isLoading.value = false
    })

    async function logout() {
      let token = authStore.token
      let res = await authStore.logout(token)
      if(res.success) {
        router.push({ name: 'login' })
      }
    }

    return {
      isLoading,
    }
  },
}
</script>

<template>
  <RouterLink to="/">
    <div class="auth-logo d-flex align-center gap-x-3">
      <VNodeRenderer :nodes="themeConfig.app.logo" />
    </div>
  </RouterLink>
  <VRow
    no-gutters
    class="auth-wrapper bg-surface"
  >
    <VCol
      cols="12"
      class="d-flex align-center justify-center"
    >
      <VCard
        flat
        :max-width="500"
        class="mt-12 mt-sm-0 pa-4"
      >
        <VCardText>
          <VAlert
            v-if="errorMessage"
            density="compact"
            type="error"
            class="mb-2"
          >
            {{ errorMessage }}
          </VAlert>
          <h4 class="text-h4 mb-1">
            Two-Factor Authentication
          </h4>
          <p class="mb-0">
            Please enter the verification code sent to your email.
          </p>
        </VCardText>
        <VCardText>
          <VForm
            ref="refForm"
            @submit.prevent="submit"
          >
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="code"
                  label="Verification Code"
                  placeholder="Enter your code"
                  :rules="[requiredValidator]"
                />
              </VCol>

              <VCol cols="12">
                <VBtn
                  block
                  type="submit"
                  :disabled="loadStatus === 1"
                >
                  <VProgressCircular
                    v-if="loadStatus === 1"
                    indeterminate
                    size="16"
                    color="white"
                  />
                  <span
                    v-if="loadStatus === 1"
                    class="px-1"
                  >Processing...</span><span
                    v-else
                    class="px-1"
                  >Verify</span>
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<script setup>
import { ref } from 'vue'
import { themeConfig } from '@themeConfig'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { useAuthStore } from "../store/auth"
import { useRouter } from 'vue-router'

const code = ref('')
const errorMessage = ref('')
const authStore = useAuthStore()
const $router = useRouter()

const loadStatus = computed(() => authStore.getLoadStatus)

async function submit() {
  const email = $router.currentRoute.value.params.email
  const res = await authStore.verifyTwoFactorCode(email, code.value)

  if (!res.data.success) {
    errorMessage.value = res.data.message

    return
  }

  errorMessage.value = ''
  localStorage.setItem('user', JSON.stringify(res.data))
  $router.push('/')

  /* console.log */
}
</script>

<style lang="scss">
  @use "@core-scss/template/pages/page-auth.scss";
</style>

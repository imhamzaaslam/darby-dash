<template>
  <VRow
    no-gutters
    class="auth-wrapper bg-surface"
  >
    <VCol
      cols="12"
      class="d-flex flex-column align-center justify-center"
    >
      <RouterLink 
        to="/"
        class="mb-7"
      >
        <div class="d-flex align-center justify-center gap-x-3">
          <VNodeRenderer :nodes="themeConfig.app.loginPageLogo" />
        </div>
      </RouterLink>
      <VCard
        :max-width="500"
        width="100%"
        style="min-height: 400px;"
        class="mt-12 mt-sm-0 pa-4"
      >
        <VCardText>
          <h4 class="text-h4 mb-1">
            Reset Password
          </h4>
          <div class="mb-1 mt-4 bg-td-hover px-4 py-2 rounded">
            <div class="text-primary text-sm">
              <p class="mb-0">
                Your new password must be different from previously used passwords
              </p>
            </div>
          </div>
        </VCardText>

        <VCardText>
          <VForm
            ref="refForm"
            @submit.prevent="submit"
          >
            <VRow>
              <!-- password -->
              <VCol cols="12">
                <VTextField
                  v-model="form.newPassword"
                  autofocus
                  label="New Password"
                  placeholder="············"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />
              </VCol>

              <!-- Confirm Password -->
              <VCol cols="12">
                <VTextField
                  v-model="form.confirmPassword"
                  label="Confirm Password"
                  placeholder="············"
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                />
              </VCol>

              <!-- reset password -->
              <VCol cols="12">
                <VBtn
                  block
                  type="submit"
                  class="custom-btn-style"
                  :disabled="loadStatus"
                >
                  <VProgressCircular
                    v-if="loadStatus"
                    indeterminate
                    size="16"
                    color="white"
                  />
                  <span
                    v-if="loadStatus"
                    class="px-1"
                  >Processing...</span><span
                    v-else
                    class="px-1"
                  >Set New Password</span>
                </VBtn>
              </VCol>

              <!-- back to login -->
              <VCol cols="12">
                <RouterLink
                  class="d-flex align-center justify-center"
                  :to="{ name: 'login' }"
                >
                  <VIcon
                    icon="tabler-chevron-left"
                    size="20"
                    class="me-1 flip-in-rtl"
                  />
                  <span>Back to login</span>
                </RouterLink>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<script setup>
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import AuthService from '@/services/AuthService'
import { useToast } from "vue-toastification"
import { useRouter, useRoute } from 'vue-router'
  
definePage({ meta: { layout: 'blank' } })
useHead({ title: `${layoutConfig.app.title} | Reset Password` })

const $router = useRouter()
const route = useRoute()

const form = ref({
  newPassword: '',
  confirmPassword: '',
})

const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)

const refForm = ref(null)
const toast = useToast()
const loadStatus = ref(false)
  
async function submit() {
  refForm.value?.validate().then(async ({ valid: isValid }) => {
    if (isValid) {
      loadStatus.value = true
      let paylaod = {
        token: route.query.token,
        email: route.query.email,
        password: form.value.newPassword,
        password_confirmation: form.value.confirmPassword,
      }

      try {
        const response = await AuthService.resetPassword(paylaod)

        if (response.status === 200) {
          $router.push({
            name: 'login',
            query: { message: encodeURIComponent(response.data.message) },
          })
        } else {
          toast.error('Something went wrong, Please try again later.')
        }
      } catch (error) {
        toast.error(error.response?.data?.message || 'Something went wrong, Please try again later.')
      }finally{
        loadStatus.value = false
      }
    }
  })

}
</script>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>

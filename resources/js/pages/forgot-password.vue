<template>
  <RouterLink to="/">
    <div class="auth-logo d-flex align-center justify-center gap-x-3">
      <VNodeRenderer :nodes="themeConfig.app.loginPageLogo" />
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
        class="auth-card"
        max-width="500"
        :class="$vuetify.display.smAndUp ? 'pa-4' : 'pa-1'"
      >
        <VCardText>
          <h4 class="text-h4 mb-1">
            Forgot Password
          </h4>
          <div class="mb-1 mt-4 bg-td-hover px-4 py-2 rounded">
            <div class="text-primary text-sm">
              <p class="mb-0">
                Type your email address here. If the email address is known to us, you will receive an email to reset your password.
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
              <!-- email -->
              <VCol cols="12">
                <VTextField
                  v-model="form.email"
                  autofocus
                  label="Email"
                  type="email"
                  :rules="[requiredValidator, emailValidator]"
                  :error-messages="submitError.email"
                />
              </VCol>
  
              <!-- Submit Button -->
              <VCol cols="12">
                <VBtn
                  block
                  type="submit"
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
                  >Send Password Reset Link</span>
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
  
definePage({ meta: { layout: 'blank' } })
useHead({ title: `${layoutConfig.app.title} | Forgot Password` })
  
const form = ref({
  email: '',
})

const submitError = ref({
  email: '',
})
  
const refForm = ref(null)
const toast = useToast()
const loadStatus = ref(false)
  
async function submit() {
  refForm.value?.validate().then(async ({ valid: isValid }) => {
    loadStatus.value = true
    if (isValid) {
      let paylaod = {
        email: form.value.email,
      }

      try {
        const response = await AuthService.forgotPassword(paylaod)

        if (response.status === 200) {
          toast.success(response.data.message)
        }
      } catch (error) {
        if (error.response.status === 422) {
          toast.error(error.response.data.message)
        } else {
          toast.error('Something went wrong. Please try again later.')
        }
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
  
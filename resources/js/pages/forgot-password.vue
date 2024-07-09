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
          <h4 class="text-h4 mb-1">
            Reset Password
          </h4>
          <div class="mb-1 mt-4 bg-td-hover px-4 py-4 rounded">
            <div class="text-primary">
              <p>
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
                  placeholder="johndoe@email.com"
                  :rules="[requiredValidator, emailValidator]"
                  :error-messages="submitError.email"
                />
              </VCol>
  
              <!-- Submit Button -->
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
                  >Send Password Reset Link</span>
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
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import AuthService from '@/services/AuthService'
import { useToast } from "vue-toastification"
  
definePage({ meta: { layout: 'blank' } })
useHead({ title: `${layoutConfig.app.title} | Forgot Password` })
  
const form = ref({
  email: 'muzammilshahzad894@gmail.com',
})

const submitError = ref({
  email: '',
})
  
const refForm = ref(null)
const toast = useToast()
  
async function submit() {
  console.log('submit')
  // refForm.value?.validate().then(async ({ valid: isValid }) => {
  //   if (isValid) {
  //     let paylaod = {
  //       email: form.value.email,
  //     }
        
  //     try {
  //       const response = await AuthService.forgotPassword(paylaod)

        

  //       // if (response.status === 200) {
  //       //   toast.success(response.data.message)
  //       // }
  //     } catch (error) {
  //       if (error.response.status === 422) {
  //         toast.error(error.response.data.message)
  //       } else {
  //         toast.error('Something went wrong. Please try again later.')
  //       }
  //     }
  //   }
  // })
}
</script>
  
<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>
  
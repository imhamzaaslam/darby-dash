<template>
  <RouterLink to="/">
    <div class="auth-logo d-flex align-center gap-x-3">
      <VNodeRenderer :nodes="themeConfig.app.logo" />
    </div>
  </RouterLink>

  <VRow no-gutters class="auth-wrapper bg-surface">


    <VCol cols="12" class="d-flex align-center justify-center">
      <VCard flat :max-width="500" class="mt-12 mt-sm-0 pa-4">
        <VCardText>
          <h4 class="text-h4 mb-1">
            Welcome to <span class="text-capitalize"> {{ themeConfig.app.title }} </span>! 👋🏻
          </h4>
          <p class="mb-0">
            Please sign-in to your account and start the adventure
          </p>
        </VCardText>
        <VCardText>
          <VForm ref="refForm" @submit.prevent="submit">
            <VRow>
              <!-- email -->
              <VCol cols="12">
                <AppTextField v-model="form.email" autofocus label="Email" type="email" placeholder="johndoe@email.com"
                  :rules="[requiredValidator, emailValidator]" />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <AppTextField v-model="form.password" label="Password" placeholder="············"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'" :rules="[requiredValidator]"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible" />

                <div class="d-flex align-center flex-wrap justify-space-between mt-2 mb-4">
                  <VCheckbox v-model="form.remember" label="Remember me" />
                  <a class="text-primary ms-2 mb-1" href="#">
                    Forgot Password?
                  </a>
                </div>

                <VBtn block type="submit" :disabled="loadStatus === 1">
                  <VProgressCircular
                    indeterminate
                    color="white"
                    v-if="loadStatus === 1"
                  />
                  Login
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
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import { useAuthStore } from "../store/auth"
import { useRouter } from 'vue-router'

definePage({ meta: { layout: 'blank' } })

const form = ref({
  email: 'admin@demo.com',
  password: 'password',
  remember: false,
})

const refForm = ref(null)

const isPasswordVisible = ref(false)

const authStore = useAuthStore()
const $router = useRouter()
const loadStatus = computed(() => authStore.getLoadStatus)

async function submit() {
  refForm.value?.validate().then(async ({ valid: isValid }) => {
    if (isValid) {
      let { email, password } = form.value
      let res = await authStore.login(email, password)

      if (!res.data.success) {
        alert(res.data.message)
        return
      }

      localStorage.setItem('user', JSON.stringify(res.data.user))

      alert('Login successful')

      $router.push('/')
    }
  })
}

</script>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>
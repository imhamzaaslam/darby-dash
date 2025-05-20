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
        :max-width="500"
        width="100%"
        style="min-height: 400px;"
        class="mt-12 mt-sm-0 pa-4"
      >
        <VCardText>
          <h4 class="text-h4 mb-1">
            Welcome to <span class="text-capitalize"> {{ themeConfig.app.title }} </span>! 👋🏻
          </h4>
          <p class="mb-0">
            Please sign-in to your account and start the adventure
          </p>
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
                  label="Email"
                  type="email"
                  placeholder="johndoe@email.com"
                  :rules="[requiredValidator, emailValidator]"
                />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <VTextField
                  v-model="form.password"
                  label="Password"
                  placeholder="············"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  :rules="[requiredValidator]"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />

                <div class="d-flex align-center flex-wrap justify-space-between mt-2 mb-4">
                  <VCheckbox
                    v-model="form.remember"
                    label="Remember me"
                  />
                  <RouterLink
                    class="text-primary ms-2 mb-1"
                    :to="{ name: 'forgot-password' }"
                  >
                    Forgot Password?
                  </RouterLink>
                </div>

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
                  >Login</span>
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
import { themeConfig, setDynamicLogoAfterLogin } from '@themeConfig'
import { useAuthStore } from "../store/auth"
import { useRouter, useRoute } from 'vue-router'
import { useToast } from "vue-toastification"

definePage({ meta: { layout: 'blank' } })
useHead({ title: `${layoutConfig.app.title} | Login` })

const form = ref({
  email: '',
  password: '',
  remember: false,
})

const refForm = ref(null)
const isPasswordVisible = ref(false)
const authStore = useAuthStore()
const $router = useRouter()
const route = useRoute()
const toast = useToast()
const loadStatus = computed(() => authStore.getLoadStatus)

onBeforeMount(async () => {
  await getTenant()
})

onMounted(() => {
  const message = route.query.message
  if (message) {
    toast.success(decodeURIComponent(message))
  }
})

const getTenant = async () => {
  try {
    await authStore.tenantInfo()
  } catch (error) {
    toast.error('Error fetching tenant:', error)
  }
}

async function submit() {
  refForm.value?.validate().then(async ({ valid: isValid }) => {
    if (isValid) {
      let { email, password } = form.value
      let res = await authStore.login(email, password)

      if (!res.data.success) {
        toast.error(res.data.message)

        return
      }

      if (res.data.requires_2fa) {
        $router.push({ name: 'two-factor-auth', params: { email } })

        return
      }

      localStorage.setItem('user', JSON.stringify(res.data))
      
      setLogoAfterLogin()

      $router.push('/')
    }
  })
}
</script>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>

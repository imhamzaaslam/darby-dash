<template>
  <VRow>
    <!-- SECTION: Change Password -->
    <VCol cols="7">
      <VCard title="Change Password">
        <VForm
          ref="passRefForm"
          @submit.prevent="submit"
        >
          <VCardText class="pt-0">
            <!-- 👉 Current Password -->
            <VRow>
              <VCol
                cols="12"
                md="12"
              >
                <!-- 👉 current password -->
                <VTextField
                  v-model="currentPassword"
                  variant="outlined"
                  :type="isCurrentPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isCurrentPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  label="Current Password"
                  autocomplete="on"
                  placeholder="············"
                  :rules="[requiredValidator]"
                  :error-messages="resetPassErrors.current_password"
                  @click:append-inner="isCurrentPasswordVisible = !isCurrentPasswordVisible"
                />
              </VCol>
            </VRow>

            <!-- 👉 New Password -->
            <VRow>
              <VCol
                cols="12"
                md="6"
              >
                <!-- 👉 new password -->
                <VTextField
                  v-model="newPassword"
                  :type="isNewPasswordVisible ? 'text' : 'password'"
                  variant="outlined"
                  :append-inner-icon="isNewPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  label="New Password"
                  autocomplete="on"
                  placeholder="············"
                  :rules="[requiredValidator]"
                  :error-messages="resetPassErrors.new_password"
                  @click:append-inner="isNewPasswordVisible = !isNewPasswordVisible"
                />
              </VCol>

              <VCol
                cols="12"
                md="6"
              >
                <!-- 👉 confirm password -->
                <VTextField
                  v-model="confirmPassword"
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  variant="outlined"
                  :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  label="Confirm New Password"
                  autocomplete="on"
                  placeholder="············"
                  :rules="[requiredValidator, confirmedValidator(confirmPassword, newPassword)]"
                  :error-messages="resetPassErrors.confirm_password"
                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                />
              </VCol>
            </VRow>
          </VCardText>

          <!-- 👉 Password Requirements -->
          <VCardText>
            <h6 class="text-base font-weight-medium mb-3">
              Password Requirements:
            </h6>

            <VList class="card-list">
              <VListItem
                v-for="item in passwordRequirements"
                :key="item"
                :title="item"
                class="text-medium-emphasis"
              >
                <template #prepend>
                  <VIcon
                    size="8"
                    icon="tabler-circle"
                    class="me-3"
                  />
                </template>
              </VListItem>
            </VList>
          </VCardText>

          <!-- 👉 Action Buttons -->
          <VCardText class="d-flex flex-wrap gap-4">
            <VBtn 
              type="submit"
              :disabled="getLoadStatus === 1"
            >
              <span v-if="getLoadStatus === 1">
                Loading...
                <VProgressCircular
                  :size="20"
                  width="3"
                  indeterminate
                />
              </span>
              <span v-else>
                Save
              </span>
            </VBtn>
          </VCardText>
        </VForm>
      </VCard>
    </VCol>
    <!-- !SECTION -->

    <!-- SECTION Two-steps verification -->
    <VCol cols="5">
      <VCard title="Two-steps verification">
        <VCardText>
          <p>
            Two-factor authentication adds an additional layer of security to your account by
            requiring more than just a password to log in.
            <a
              href="javascript:void(0)"
              class="text-decoration-none"
            >Learn more.</a>
          </p>

          <VBtn @click="isOneTimePasswordDialogVisible = true">
            Enable 2FA
          </VBtn>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>

  <!-- SECTION Enable One time password -->
  <TwoFactorAuthDialog v-model:isDialogVisible="isOneTimePasswordDialogVisible" />
  <!-- !SECTION -->
</template>

<script setup>
import { useUserStore } from "@/store/users"
import { useToast } from "vue-toastification"

const userStore = useUserStore()
const toast = useToast()
const isCurrentPasswordVisible = ref(false)
const isNewPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')
const passRefForm = ref(null)
const isOneTimePasswordDialogVisible = ref(false)

const resetPassErrors = ref({
  current_password: null,
  new_password: null,
  confirm_password: null,
})

const passwordRequirements = [
  'Minimum 8 characters long - the more, the better',
  'At least one lowercase character',
  'Password and confirm password should match',
]

async function submit() {
  passRefForm.value?.validate().then(async ({ valid: isValid }) => {
    if (isValid) {
      resetErrors()

      const uuid = userStore.getUser?.uuid

      const payload = {
        current_password: currentPassword.value,
        new_password: newPassword.value,
        confirm_password: confirmPassword.value,
      }

      await userStore.updatePassword(uuid, payload)
      if(getErrors.value) {
        showError()
      } else {
        toast.success('Password changed successfully')
        resetFormFields()
      }
    }
  })
}

const showError = () => {
  if (getStatusCode === 500) {
    toast.error('Something went wrong. Please try again later.')
  } else {
    // set errors according to the response
    resetPassErrors.value = getErrors.value
  }
}

const resetErrors = () => {
  for (const key in resetPassErrors.value) {
    resetPassErrors.value[key] = null
  }
}

const getErrors = computed(() => {
  return userStore.getErrors
})

const getLoadStatus = computed(() => {
  return userStore.getLoadStatus
})

const getStatusCode = computed(() => {
  return userStore.getStatusCode
})
</script>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 5px;
}

.server-close-btn {
  inset-inline-end: 0.5rem;
}
</style>

<template>
  <VDialog
    v-model="props.isModalOpen"
    persistent
    max-width="800"
    scrollable
  >
    <VCard>
      <!-- 👉 Header -->
      <AppDrawerHeaderSection
        title="Add Member Details"
        @cancel="$emit('update:isModalOpen', false)"
      />

      <VDivider />

      <PerfectScrollbar :options="{ wheelPropagation: false }" class="h-100">
        <VCardText>
          <VForm ref="addMemberForm" @submit.prevent="submitAddMemberForm">
            <VRow>
              <!-- All your form fields remain the same -->
              <VCol cols="6">
                <AppTextField
                  ref="focusInput"
                  v-model="newMemberDetails.name_first"
                  label="First Name *"
                  :rules="[requiredValidator]"
                  :error-messages="addingErrors.name_first"
                  placeholder="First Name"
                />
              </VCol>

              <!-- Last Name -->
              <VCol cols="6">
                <AppTextField
                  v-model="newMemberDetails.name_last"
                  label="Last Name *"
                  :rules="[requiredValidator]"
                  :error-messages="addingErrors.name_last"
                  placeholder="Last Name"
                />
              </VCol>

              <!-- Email -->
              <VCol cols="6">
                <AppTextField
                  v-model="newMemberDetails.email"
                  label="Email *"
                  :rules="[requiredValidator, emailValidator]"
                  :error-messages="addingErrors.email"
                  placeholder="Email"
                />
              </VCol>
              
              <VCol cols="6">
                <AppTextField
                  v-model="newMemberDetails.company_name"
                  label="Company Name"
                  placeholder="Company Name"
                />
              </VCol>

              <!-- Phone Mask Field -->
              <VCol cols="6">
                <AppTextField
                  v-model="newMemberDetails.phone"
                  v-mask="'(###) ###-####'"
                  label="Phone *"
                  :rules="[requiredValidator]"
                  :error-messages="addingErrors.phone"
                  placeholder="(123) 456-7890"
                />
              </VCol>

              <!-- Role -->
              <VCol cols="6">
                <AppSelect
                  v-model="newMemberDetails.role"
                  label="Select Role *"
                  placeholder="Select Role"
                  :items="props.getRoles"
                  item-title="name"
                  item-value="name"
                  :rules="[requiredValidator]"
                  :error-messages="addingErrors.role"
                />
              </VCol>

              <!-- Password -->
              <VCol cols="6">
                <AppTextField
                  v-model="newMemberDetails.password"
                  label="Password *"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  :rules="[requiredValidator]"
                  :error-messages="addingErrors.password"
                  placeholder="Password"
                  autocomplete="new-password"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />
              </VCol>

              <!-- Confirm Password -->
              <VCol cols="6">
                <AppTextField
                  v-model="newMemberDetails.confirmPassword"
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  label="Confirm Password *"
                  :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  :rules="[requiredValidator, confirmedValidator(newMemberDetails.confirmPassword, newMemberDetails.password)]"
                  :error-messages="addingErrors.confirmPassword"
                  placeholder="Confirm Password"
                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                />
              </VCol>
              
              <VCol v-if="newMemberDetails.role == 'Client User'" cols="6">
                <label class="text-sm font-medium mb-1 d-block">Company Logo</label>
                <VFileInput
                  v-model="newMemberDetails.company_logo"
                  :error-messages="newMemberDetails.company_logo"
                  accept="image/*"
                  variant="filled"
                  label="Company Logo"
                >
                  <template #selection="{ fileNames }">
                    <div v-if="fileNames?.length">
                      <span 
                        v-for="(file, index) in fileNames" 
                        :key="index"
                      >
                        {{ truncateFileName(file) }}
                      </span>
                    </div>
                  </template>
                </VFileInput>
              </VCol>
              <!-- Replace cancel button logic -->
              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2 custom-btn-style"
                    :disabled="getLoadStatus === 1"
                    @click="addMemberForm?.validate()"
                  >
                    <span v-if="getLoadStatus === 1">
                      <VProgressCircular :size="16" width="3" indeterminate />
                      Loading...
                    </span>
                    <span v-else>Save</span>
                  </VBtn>
                  <VBtn 
                    color="error" 
                    class="error-btn-customer-style" 
                    @click="resetFormFields"
                  >
                    Cancel
                  </VBtn>
                </div>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </PerfectScrollbar>
    </VCard>
  </VDialog>
</template>

<script setup>
import { ref, nextTick, watch } from 'vue'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'
import { useToast } from "vue-toastification"
import { useUserStore } from "../../../store/users"

const props = defineProps({
  isModalOpen: Boolean,
  fetchMembers: Function,
  getRoles: Object,
  getErrors: Object,
  getStatusCode: Object,
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isModalOpen'])

const toast = useToast()
const userStore = useUserStore()

const focusInput = ref(null)
const addMemberForm = ref()
const isLoading = ref(false)
const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)

const newMemberDetails = ref({
  name_first: '',
  name_last: '',
  email: '',
  company_name: '',
  role: null,
  phone: '',
  password: '',
  confirmPassword: '',
  company_logo: '',
})

const addingErrors = ref({
  name_first: '',
  name_last: '',
  email: '',
  role: '',
  phone: '',
  password: '',
  confirmPassword: '',
})

watch(() => props.isModalOpen, val => {
    console.log('Modal state changed:', val)
  if (val) {
    console.log('Modal opened')
    nextTick(() => {
      const inputEl = focusInput.value?.$el?.querySelector('input')
      if (inputEl) inputEl.focus()
    })
  }
})

const submitAddMemberForm = async () => {
  addMemberForm.value?.validate().then(async ({ valid }) => {
    if (valid) {
      try {
        resetErrors()
        await userStore.create(newMemberDetails.value)

        if (props.getErrors) {
          showError()
        } else {
          isLoading.value = true
          emit('update:isModalOpen', false)
          toast.success('Member added successfully', { timeout: 1000 })
          await props.fetchMembers()
          resetFormFields()
          isLoading.value = false
        }
      } catch (error) {
        toast.error('Failed to add member')
      }
    }
  })
}

const resetFormFields = () => {
  addMemberForm.value?.reset()
  assignEmptyValues()
  resetErrors()
  emit('update:isModalOpen', false)
}

const assignEmptyValues = () => {
  Object.assign(newMemberDetails.value, {
    name_first: '',
    name_last: '',
    email: '',
    role: null,
    phone: '',
    password: '',
    confirmPassword: '',
    company_logo: '',
  })
}

const resetErrors = () => {
  addingErrors.value = Object.fromEntries(Object.keys(addingErrors.value).map(key => [key, '']))
}

const showError = () => {
  if (props.getStatusCode === 500) {
    toast.error('Something went wrong. Please try again later.')
  } else {
    addingErrors.value = props.getErrors
  }
}

const truncateFileName = name => {
  const maxLength = 20
  if (name.length <= maxLength) return name
  const ext = name.substring(name.lastIndexOf('.'))
  const base = name.substring(0, maxLength - ext.length - 3)
  return `${base}...${ext}`
}
</script>

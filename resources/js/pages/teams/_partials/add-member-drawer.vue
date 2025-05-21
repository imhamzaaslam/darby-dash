<template>
  <VNavigationDrawer
    :model-value="props.isDrawerOpen"
    temporary
    location="end"
    width="600"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Add Member Details"
      @cancel="$emit('update:isDrawerOpen', false)"
    />

    <VDivider />

    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm
            ref="addMemberForm"
            @submit.prevent="submitAddMemberForm"
          >
            <VRow>
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

              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2"
                    :disabled="getLoadStatus === 1"
                    @click="addMemberForm?.validate()"
                  >
                    <span v-if="getLoadStatus === 1">
                      <VProgressCircular
                        :size="16"
                        width="3"
                        indeterminate
                      />
                      Loading...
                    </span>
                    <span v-else>
                      Save
                    </span>
                  </VBtn>
                  <VBtn
                    color="error"
                    variant="tonal"
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
  </VNavigationDrawer>
</template>

<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'
import { ref, nextTick } from 'vue'
import { useToast } from "vue-toastification"
import { useUserStore } from "../../../store/users"

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchMembers: Function,
  getRoles: Object,
  getErrors: Object,
  getStatusCode: Object,
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isDrawerOpen'])
const toast = useToast()
const userStore = useUserStore()

const focusInput = ref(null)
const addMemberForm = ref()
const isLoading= ref(false)
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

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}

const resetFormFields = () => {
  addMemberForm.value?.reset()
  assignEmptyValeus()
  resetErrors()
  emit('update:isDrawerOpen', false)
}

async function submitAddMemberForm() {
  addMemberForm.value?.validate().then(async ({ valid: isValid }) => {
    console.log('validating')
    if(isValid){
      try {
        resetErrors()
        await userStore.create(newMemberDetails.value)
        if(props.getErrors)
        {
          showError()
        }
        else{
          isLoading.value = true
          emit('update:isDrawerOpen', false)
          toast.success('Member added successfully', { timeout: 1000 })
          await props.fetchMembers()
          resetFormFields()
          isLoading.value = false
        }
      } catch (error) {
        toast.error('Failed to add member:', error)
      }
    }
  })
}

const assignEmptyValeus = () => {
  Object.assign(newMemberDetails.value, {
    name_first: '',
    name_last: '',
    email: '',
    role: null,
    phone: '',
    password: '',
    confirmPassword: '',
  })
}

const showError = () => {
  if (props.getStatusCode === 500) {
    toast.error('Something went wrong. Please try again later.')
  } else {
    addingErrors.value = props.getErrors
  }
}

const resetErrors = () => {
  addingErrors.value = Object.fromEntries(Object.keys(addingErrors.value).map(key => [key, '']))
}

const truncateFileName = name => {
  const maxLength = 20
  if (name.length <= maxLength) return name

  const ext = name.substring(name.lastIndexOf('.'))
  const base = name.substring(0, maxLength - ext.length - 3)

  return `${base}...${ext}`
}

watch(() => props.isDrawerOpen, val => {
  if (val) {
    nextTick(() => {
      const inputEl = focusInput.value.$el.querySelector('input')
      if (inputEl) {
        inputEl.focus()
      }
    })
  }
})
</script>

  <style lang="scss">
  .v-navigation-drawer__content {
    overflow-y: hidden !important;
  }
  </style>

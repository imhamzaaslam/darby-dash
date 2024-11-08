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
      title="Add Comoany Details"
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
            ref="addCompanyForm"
            @submit.prevent="submitAddCompanyForm"
          >
            <VRow>
              <VCol
                md="12"
                cols="12"
              >
                <AppTextField
                  v-model="newCompanyDetails.name"
                  label="Name*"
                  :rules="[requiredValidator]"
                  placeholder="Name"
                />
              </VCol>
              <VCol cols="6">
                <AppTextField
                  ref="focusInput"
                  v-model="newCompanyDetails.name_first"
                  label="First Name *"
                  :rules="[requiredValidator]"
                  :error-messages="addingErrors.name_first"
                  placeholder="First Name"
                />
              </VCol>

              <!-- Last Name -->
              <VCol cols="6">
                <AppTextField
                  v-model="newCompanyDetails.name_last"
                  label="Last Name *"
                  :rules="[requiredValidator]"
                  :error-messages="addingErrors.name_last"
                  placeholder="Last Name"
                />
              </VCol>

              <!-- Email -->
              <VCol cols="6">
                <AppTextField
                  v-model="newCompanyDetails.email"
                  label="Email *"
                  :rules="[requiredValidator, emailValidator]"
                  :error-messages="addingErrors.email"
                  placeholder="Email"
                />
              </VCol>

              <!-- Phone Mask Field -->
              <VCol cols="6">
                <AppTextField
                  v-model="newCompanyDetails.phone"
                  v-mask="'(###) ###-####'"
                  label="Phone *"
                  :rules="[requiredValidator]"
                  :error-messages="addingErrors.phone"
                  placeholder="(123) 456-7890"
                />
              </VCol>

              <!-- Password -->
              <VCol cols="6">
                <AppTextField
                  v-model="newCompanyDetails.password"
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
                  v-model="newCompanyDetails.confirmPassword"
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  label="Confirm Password *"
                  :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  :rules="[requiredValidator, confirmedValidator(newCompanyDetails.confirmPassword, newCompanyDetails.password)]"
                  :error-messages="addingErrors.confirmPassword"
                  placeholder="Confirm Password"
                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                />
              </VCol>
              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2"
                    :disabled="props.getLoadStatus === 1"
                    @click="addCompanyForm?.validate()"
                  >
                    <span v-if="props.getLoadStatus === 1">
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
                    @click="resetForm"
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
import { useCompanyStore } from "@/store/companies"

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchCompanies: Function,
  getErrors: Object,
  getStatusCode: Object,
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isDrawerOpen'])
const toast = useToast()
const companyStore = useCompanyStore()

const focusInput = ref(null)
const addCompanyForm = ref()
const isLoading= ref(false)
const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)

const newCompanyDetails = ref({
  name: null,
  name_first: '',
  name_last: '',
  email: '',
  phone: '',
  password: '',
  confirmPassword: '',
})

const addingErrors = ref({
  name: '',
  name_first: '',
  name_last: '',
  email: '',
  phone: '',
  password: '',
  confirmPassword: '',
})

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}

const resetFormFields = () => {
  addCompanyForm.value?.reset()
  assignEmptyValeus()
  resetErrors()
  emit('update:isDrawerOpen', false)
}

async function submitAddCompanyForm() {
  addCompanyForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        resetErrors()
        await companyStore.create(newCompanyDetails.value)
        if(props.getErrors)
        {
          showError()
        }
        else{
          isLoading.value = true
          emit('update:isDrawerOpen', false)
          toast.success('Company added successfully', { timeout: 1000 })
          await props.fetchCompanies()
          resetFormFields()
          isLoading.value = false
        }
      } catch (error) {
        toast.error('Failed to add company:', error)
      }
    }
  })
}

const assignEmptyValeus = () => {
  Object.assign(newCompanyDetails.value, {
    name: null,
    name_first: '',
    name_last: '',
    email: '',
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

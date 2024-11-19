<template>
  <VNavigationDrawer
    :model-value="props.isEditDrawerOpen"
    temporary
    location="end"
    width="600"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Edit Company Details"
      @cancel="$emit('update:isEditDrawerOpen', false)"
    />

    <VDivider />

    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm
            ref="editCompanyForm"
            @submit.prevent="submitEditCompanyForm"
          >
            <VRow>
              <!-- Company Name -->
              <VCol cols="12">
                <AppTextField
                  ref="focusInput"
                  v-model="props.editCompanyDetails.name"
                  label="Company Name *"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.name"
                  placeholder="Company Name"
                />
              </VCol>
              <VCol
                cols="12"
                class="pb-0 mb-0"
              >
                <h3 class="text-high-emphasis text-primary">
                  Admin Details
                </h3>
              </VCol>
              <!-- First Name -->
              <VCol cols="6">
                <AppTextField
                  v-model="props.editCompanyDetails.name_first"
                  label="First Name *"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.name_first"
                  placeholder="First Name"
                />
              </VCol>

              <!-- Last Name -->
              <VCol cols="6">
                <AppTextField
                  v-model="props.editCompanyDetails.name_last"
                  label="Last Name *"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.name_last"
                  placeholder="Last Name"
                />
              </VCol>

              <!-- Email -->
              <VCol cols="6">
                <AppTextField
                  v-model="props.editCompanyDetails.email"
                  label="Email *"
                  :rules="[requiredValidator, emailValidator]"
                  :error-messages="editErrors.email"
                  placeholder="Email"
                />
              </VCol>

              <!-- Phone -->
              <VCol cols="6">
                <AppTextField
                  v-model="props.editCompanyDetails.phone"
                  v-mask="'(###) ###-####'"
                  label="Phone *"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.phone"
                  placeholder="(123) 456-7890"
                />
              </VCol>

              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2"
                    :disabled="getLoadStatus === 1"
                    @click="editCompanyForm?.validate()"
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
                      Update
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
import { useCompanyStore } from "@/store/companies"

const props = defineProps({
  isEditDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchCompanies: Function,
  getErrors: Object,
  getStatusCode: Object,
  editCompanyDetails: Object,
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isEditDrawerOpen'])
const toast = useToast()
const companyStore = useCompanyStore()

const focusInput = ref(null)
const editCompanyForm = ref()
const isLoading= ref(false)

const editErrors = ref({
  name: '',
  name_first: '',
  name_last: '',
  email: '',
  phone: '',
})

const handleDrawerModelValueUpdate = val => {
  emit('update:isEditDrawerOpen', val)
}

const resetFormFields = () => {
  editCompanyForm.value?.reset()
  emit('update:isEditDrawerOpen', false)
}

async function submitEditCompanyForm() {
  return
  editCompanyForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        resetForm()
        await companyStore.update(props.editCompanyDetails.uuid, props.editCompanyDetails)
        if(props.getErrors)
        {
          showError()
        }
        else{
          isLoading.value = true
          emit('update:isEditDrawerOpen', false)
          toast.success('Company details updated successfully', { timeout: 1000 })
          await props.fetchCompanies()
          isLoading.value = false
        }
      } catch (error) {
        toast.error('Failed to update company:', error.message || error)
      }
    }
  })
}

const showError = () => {
  if (props.getStatusCode === 500) {
    toast.error('Something went wrong. Please try again later.')
  } else {
    editErrors.value = props.getErrors
  }
}

const resetForm = () => {
  editErrors.value = Object.fromEntries(Object.keys(editErrors.value).map(key => [key, '']))
}

watch(() => props.isEditDrawerOpen, val => {
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

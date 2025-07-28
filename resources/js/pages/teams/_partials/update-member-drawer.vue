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
      title="Edit Member Details"
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
            ref="editMemberForm"
            @submit.prevent="submitEditMemberForm"
          >
            <VRow>
              <!-- First Name -->
              <VCol cols="6">
                <AppTextField
                  ref="focusInput"
                  v-model="props.editMemberDetails.name_first"
                  label="First Name *"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.name_first"
                  placeholder="First Name"
                />
              </VCol>

              <!-- Last Name -->
              <VCol cols="6">
                <AppTextField
                  v-model="props.editMemberDetails.name_last"
                  label="Last Name *"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.name_last"
                  placeholder="Last Name"
                />
              </VCol>

              <!-- Email -->
              <VCol cols="6">
                <AppTextField
                  v-model="props.editMemberDetails.email"
                  label="Email *"
                  :rules="[requiredValidator, emailValidator]"
                  :error-messages="editErrors.email"
                  placeholder="Email"
                />
              </VCol>
              
              <VCol cols="6">
                <AppTextField
                  v-model="props.editMemberDetails.company_name"
                  label="Company Name"
                  placeholder="Company Name"
                />
              </VCol>

              <!-- Phone -->
              <VCol cols="6">
                <AppTextField
                  v-model="props.editMemberDetails.phone"
                  v-mask="'(###) ###-####'"
                  label="Phone *"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.phone"
                  placeholder="(123) 456-7890"
                />
              </VCol>

              <!-- Role -->
              <VCol cols="6">
                <AppSelect
                  v-model="props.editMemberDetails.role"
                  label="Select Role *"
                  placeholder="Select Role"
                  :items="getRoles"
                  item-title="name"
                  item-value="name"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.role"
                />
              </VCol>
              
              <VCol v-if="props.editMemberDetails.role == 'Client User'" cols="6">
                <label class="text-sm font-medium mb-1 d-block">Company Logo</label>
                <VFileInput
                  v-model="props.editMemberDetails.company_logo"
                  :error-messages="editErrors.company_logo"
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

                <VImg
                  v-if="props.editMemberDetails.companyLogo"
                  :src="props.editMemberDetails.companyLogo"
                  class="rounded-lg mt-1"
                  width="60"
                  height="60"
                />
              </VCol>
              
              <VCol
                cols="6"
                class="pt-8"
              >
                <VSwitch
                  v-model="props.editMemberDetails.state"
                  color="primary"
                  label="Status"
                  true-value="active"
                  false-value="inactive"
                />
              </VCol>

              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2 custom-btn-style"
                    :disabled="getLoadStatus === 1"
                    @click="editMemberForm?.validate()"
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
  </VNavigationDrawer>
</template>

<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'
import { ref, nextTick } from 'vue'
import { useToast } from "vue-toastification"
import { useUserStore } from "../../../store/users"

const props = defineProps({
  isEditDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchMembers: Function,
  getRoles: Object,
  getErrors: Object,
  getStatusCode: Object,
  editMemberDetails: Object,
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isEditDrawerOpen'])
const toast = useToast()
const userStore = useUserStore()

const focusInput = ref(null)
const editMemberForm = ref()
const isLoading= ref(false)

const editErrors = ref({
  name_first: '',
  name_last: '',
  email: '',
  role: '',
  phone: '',
  company_logo: '',
})

const handleDrawerModelValueUpdate = val => {
  emit('update:isEditDrawerOpen', val)
}

const resetFormFields = () => {
  editMemberForm.value?.reset()
  emit('update:isEditDrawerOpen', false)
}

async function submitEditMemberForm() {
  editMemberForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        resetForm()
        await userStore.update(props.editMemberDetails)
        if(props.getErrors)
        {
          showError()
        }
        else{
          isLoading.value = true
          emit('update:isEditDrawerOpen', false)
          toast.success('Member updated successfully', { timeout: 1000 })
          await props.fetchMembers()
          isLoading.value = false
        }
      } catch (error) {
        toast.error('Failed to update member:', error.message || error)
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

const truncateFileName = name => {
  const maxLength = 20
  if (name.length <= maxLength) return name

  const ext = name.substring(name.lastIndexOf('.'))
  const base = name.substring(0, maxLength - ext.length - 3)

  return `${base}...${ext}`
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

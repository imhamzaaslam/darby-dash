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
                  v-model="newMemberDetails.name_first"
                  label="First Name *"
                  :rules="[requiredValidator]"
                  :error-messages="addingErrors.name_first"
                />
              </VCol>

              <!-- Last Name -->
              <VCol cols="6">
                <AppTextField
                  v-model="newMemberDetails.name_last"
                  label="Last Name *"
                  :rules="[requiredValidator]"
                  :error-messages="addingErrors.name_last"
                />
              </VCol>

              <!-- Email -->
              <VCol cols="6">
                <AppTextField
                  v-model="newMemberDetails.email"
                  label="Email *"
                  :rules="[requiredValidator, emailValidator]"
                  :error-messages="addingErrors.email"
                />
              </VCol>

              <!-- Phone Mask Field -->
              <VCol cols="6">
                <AppTextField
                  v-model="newMemberDetails.phone"
                  type="number"
                  label="Phone *"
                  :rules="[requiredValidator]"
                  :error-messages="addingErrors.phone"
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

              <!-- State -->
              <VCol cols="6">
                <AppSelect
                  v-model="newMemberDetails.state"
                  label="Select Status *"
                  placeholder="Select Status"
                  :items="[ { title: 'Active', value: 'active' }, { title: 'Inactive', value: 'inactive' } ]"
                  item-title="title"
                  item-value="value"
                  :rules="[requiredValidator]"
                  :error-messages="addingErrors.state"
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
                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                />
              </VCol>

              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2"
                    @click="addMemberForm?.validate()"
                  >
                    Save
                  </VBtn>
                  <VBtn
                    color="error"
                    variant="tonal"
                    @click="resetFormFields"
                  >
                    Reset
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
import { ref } from 'vue'
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
})

const emit = defineEmits(['update:isDrawerOpen'])
const toast = useToast()
const userStore = useUserStore()

const addMemberForm = ref()
const isLoading= ref(false)
const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)

const newMemberDetails = ref({
  name_first: '',
  name_last: '',
  email: '',
  role: '',
  phone: '',
  password: '',
  confirmPassword: '',
  state: '',
})

const addingErrors = ref({
  name_first: '',
  name_last: '',
  email: '',
  role: '',
  phone: '',
  password: '',
  confirmPassword: '',
  state: '',
})

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}

const resetFormFields = () => {
  addMemberForm.value?.reset()
  emit('update:isDrawerOpen', false)
}

async function submitAddMemberForm() {
  addMemberForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        resetForm('addingErrors')
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
          resetForm('addingForm')
          isLoading.value = false
        }
      } catch (error) {
        toast.error('Failed to add member:', error)
      }
    }
  })
}

const showError = () => {
  if (props.getStatusCode === 500) {
    toast.error('Something went wrong. Please try again later.')
  } else {
    addingErrors.value = props.getErrors
  }
}

const resetForm = formType => {
  if(formType === 'addErrors') {
    addingErrors.value = Object.fromEntries(Object.keys(addingErrors.value).map(key => [key, '']))
  }
  if(formType === 'addingForm') {
    newMemberDetails.value = Object.fromEntries(Object.keys(newMemberDetails.value).map(key => [key, '']))
  }
}
</script>

  <style lang="scss">
  .v-navigation-drawer__content {
    overflow-y: hidden !important;
  }
  </style>
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

              <!-- State -->
              <VCol cols="6">
                <AppSelect
                  v-model="props.editMemberDetails.state"
                  label="Select Status *"
                  placeholder="Select Status"
                  :items="[ { title: 'Active', value: 'active' }, { title: 'Inactive', value: 'inactive' } ]"
                  item-title="title"
                  item-value="value"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.state"
                />
              </VCol>

              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2"
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
import { ref } from 'vue'
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

const editMemberForm = ref()
const isLoading= ref(false)

const editErrors = ref({
  name_first: '',
  name_last: '',
  email: '',
  role: '',
  phone: '',
  state: '',
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
</script>

<style lang="scss">
.v-navigation-drawer__content {
overflow-y: hidden !important;
}
</style>

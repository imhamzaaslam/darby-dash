<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'userData',
])

const isFormValid = ref(false)
const refForm = ref()
const fullName = ref('')
const userName = ref('')
const email = ref('')
const company = ref('')
const country = ref()
const contact = ref('')
const role = ref()
const plan = ref()
const status = ref()

// 👉 drawer close
const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()
  })
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      emit('userData', {
        id: 0,
        fullName: fullName.value,
        company: company.value,
        role: role.value,
        country: country.value,
        contact: contact.value,
        email: email.value,
        currentPlan: plan.value,
        status: status.value,
        avatar: '',
        billing: 'Auto Debit',
      })
      emit('update:isDrawerOpen', false)
      nextTick(() => {
        refForm.value?.reset()
        refForm.value?.resetValidation()
      })
    }
  })
}

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}
</script>

<template>
  <VNavigationDrawer
    temporary
    :width="400"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Title -->
    <AppDrawerHeaderSection
      title="Add New User"
      @cancel="closeNavigationDrawer"
    />

    <VDivider />

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <!-- 👉 Form -->
          <VForm
            ref="refForm"
            v-model="isFormValid"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 Full name -->
              <VCol cols="12">
                <AppTextField
                  v-model="fullName"
                  :rules="[requiredValidator]"
                  label="Full Name"
                  placeholder="John Doe"
                />
              </VCol>

              <!-- 👉 Username -->
              <VCol cols="12">
                <AppTextField
                  v-model="userName"
                  :rules="[requiredValidator]"
                  label="Username"
                  placeholder="Johndoe"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol cols="12">
                <AppTextField
                  v-model="email"
                  :rules="[requiredValidator, emailValidator]"
                  label="Email"
                  placeholder="johndoe@email.com"
                />
              </VCol>

              <!-- 👉 company -->
              <VCol cols="12">
                <AppTextField
                  v-model="company"
                  :rules="[requiredValidator]"
                  label="Company"
                  placeholder="PixInvent"
                />
              </VCol>

              <!-- 👉 Country -->
              <VCol cols="12">
                <AppSelect
                  v-model="country"
                  label="Select Country"
                  placeholder="Select Country"
                  :rules="[requiredValidator]"
                  :items="['USA', 'UK', 'India', 'Australia']"
                />
              </VCol>

              <!-- 👉 Contact -->
              <VCol cols="12">
                <AppTextField
                  v-model="contact"
                  type="number"
                  :rules="[requiredValidator]"
                  label="Contact"
                  placeholder="+1-541-754-3010"
                />
              </VCol>

              <!-- 👉 Role -->
              <VCol cols="12">
                <AppSelect
                  v-model="role"
                  label="Select Role"
                  placeholder="Select Role"
                  :rules="[requiredValidator]"
                  :items="['Admin', 'Author', 'Editor', 'Maintainer', 'Subscriber']"
                />
              </VCol>

              <!-- 👉 Plan -->
              <VCol cols="12">
                <AppSelect
                  v-model="plan"
                  label="Select Plan"
                  placeholder="Select Plan"
                  :rules="[requiredValidator]"
                  :items="['Basic', 'Company', 'Enterprise', 'Team']"
                />
              </VCol>

              <!-- 👉 Status -->
              <VCol cols="12">
                <AppSelect
                  v-model="status"
                  label="Select Status"
                  placeholder="Select Status"
                  :rules="[requiredValidator]"
                  :items="[{ title: 'Active', value: 'active' }, { title: 'Inactive', value: 'inactive' }, { title: 'Pending', value: 'pending' }]"
                />
              </VCol>

              <!-- 👉 Submit and Cancel -->
              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-3 custom-btn-style"
                >
                  Submit
                </VBtn>
                <VBtn
                  type="reset"
                  color="error"
                  class="error-btn-customer-style"
                  @click="closeNavigationDrawer"
                >
                  Cancel
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>

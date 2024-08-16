<script setup>
import Stepper from '@/components/bucks-settings/Stepper.vue'

const numberedSteps = [
  {
    title: 'Super Admin',
    subtitle: '',
  },
  {
    title: 'Project Manager',
    subtitle: '',
  },
  {
    title: 'Staff User',
    subtitle: '',
  },
  {
    title: 'Client User',
    subtitle: '',
  },
]

const currentStep = ref(0)
const isPasswordVisible = ref(false)
const isCPasswordVisible = ref(false)

const formData = ref({
  username: '',
  email: '',
  password: '',
  cPassword: '',
  firstName: '',
  lastName: '',
  country: undefined,
  language: undefined,
  twitter: '',
  facebook: '',
  googlePlus: '',
  linkedIn: '',
})

const onSubmit = () => {
  console.log(formData.value)
}
</script>

<template>
  <VCard>
    <VRow>
      <VCol
        cols="12"
        md="4"
        :class="$vuetify.display.smAndDown ? 'border-b' : 'border-e'"
      >
        <VCardText>
          <!-- 👉 Stepper -->
          <Stepper
            v-model:current-step="currentStep"
            direction="vertical"
            :items="numberedSteps"
          />
        </VCardText>
      </VCol>
      <!-- 👉 stepper content -->
      <VCol
        cols="12"
        md="8"
      >
        <VCardText>
          <VForm>
            <VWindow
              v-model="currentStep"
              class="disable-tab-transition"
            >
              <VWindowItem>
                <VRow>
                  <VCol cols="12">
                    <h6 class="text-h6 font-weight-medium">
                      Super Admin
                    </h6>
                    <p class="mb-0">
                      Manage Bucks for Super Admin
                    </p>
                  </VCol>
                </VRow>
                <VRow class="d-flex align-center">
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="TasK Name"
                      label="Task Name"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="$10"
                      label="Bucks Amount"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                    class="d-flex align-center mt-4"
                  >
                    <VBtn
                      color="success"
                      class="mr-2"
                    >
                      Approve
                    </VBtn>
                    <VBtn color="error">
                      Reject
                    </VBtn>
                  </VCol>
                </VRow>
                <VRow class="d-flex align-center">
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="TasK Name"
                      label="Task Name"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="$10"
                      label="Bucks Amount"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                    class="d-flex align-center mt-4"
                  >
                    <VBtn
                      color="success"
                      class="me-2"
                    >
                      Approve
                    </VBtn>
                    <VBtn color="error">
                      Reject
                    </VBtn>
                  </VCol>
                </VRow>
              </VWindowItem>

              <VWindowItem>
                <VRow>
                  <VCol cols="12">
                    <h6 class="text-h6 font-weight-medium">
                      Project Manager
                    </h6>
                    <p class="mb-0">
                      Manage Bucks for Project Manager
                    </p>
                  </VCol>
                </vrow>
                <VRow class="d-flex align-center">
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="TasK Name"
                      label="Task Name"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="$10"
                      label="Bucks Amount"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                    class="d-flex align-center mt-4"
                  >
                    <VBtn
                      color="success"
                      class="me-2"
                    >
                      Approve
                    </VBtn>
                    <VBtn color="error">
                      Reject
                    </VBtn>
                  </VCol>
                </VRow>
                <VRow class="d-flex align-center">
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="TasK Name"
                      label="Task Name"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="$10"
                      label="Bucks Amount"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                    class="d-flex align-center mt-4"
                  >
                    <VBtn
                      color="success"
                      class="me-2"
                    >
                      Approve
                    </VBtn>
                    <VBtn color="error">
                      Reject
                    </VBtn>
                  </VCol>
                </VRow>
              </VWindowItem>

              <VWindowItem>
                <VRow>
                  <VCol cols="12">
                    <h6 class="text-h6 font-weight-medium">
                      Staff User
                    </h6>
                    <p class="mb-0">
                      Manage Bucks for Staff User
                    </p>
                  </VCol>
                </VRow>
                <VRow class="d-flex align-center">
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="TasK Name"
                      label="Task Name"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="$10"
                      label="Bucks Amount"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                    class="d-flex align-center mt-4"
                  >
                    <VBtn
                      color="success"
                      class="me-2"
                    >
                      Approve
                    </VBtn>
                    <VBtn color="error">
                      Reject
                    </VBtn>
                  </VCol>
                </VRow>
                <VRow class="d-flex align-center">
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="TasK Name"
                      label="Task Name"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="$10"
                      label="Bucks Amount"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                    class="d-flex align-center mt-4"
                  >
                    <VBtn
                      color="success"
                      class="me-2"
                    >
                      Approve
                    </VBtn>
                    <VBtn color="error">
                      Reject
                    </VBtn>
                  </VCol>
                </VRow>
              </VWindowItem>

              <VWindowItem>
                <VRow>
                  <VCol cols="12">
                    <h6 class="text-h6 font-weight-medium">
                      Client User
                    </h6>
                    <p class="mb-0">
                      Manage Bucks for Client User
                    </p>
                  </VCol>
                </VRow>
                <VRow class="d-flex align-center">
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="TasK Name"
                      label="Task Name"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="$10"
                      label="Bucks Amount"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                    class="d-flex align-center mt-4"
                  >
                    <VBtn
                      color="success"
                      class="me-2"
                    >
                      Approve
                    </VBtn>
                    <VBtn color="error">
                      Reject
                    </VBtn>
                  </VCol>
                </VRow>
                <VRow class="d-flex align-center">
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="TasK Name"
                      label="Task Name"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                  >
                    <AppTextField
                      value="$10"
                      label="Bucks Amount"
                      readonly
                      disabled
                      class="me-2" 
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="4"
                    class="d-flex align-center mt-4"
                  >
                    <VBtn
                      color="success"
                      class="me-2"
                    >
                      Approve
                    </VBtn>
                    <VBtn color="error">
                      Reject
                    </VBtn>
                  </VCol>
                </VRow>
              </VWindowItem>
            </VWindow>
          </VForm>
        </VCardText>
      </VCol>
    </VRow>
  </VCard>
</template>

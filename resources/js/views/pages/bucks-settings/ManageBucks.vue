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
                      Account Details
                    </h6>
                    <p class="mb-0">
                      Manage Bucks for Super Admin
                    </p>
                  </VCol>
                </VRow>
                <VRow class="d-flex justify-center align-center">
                  <VCol
                    cols="12"
                    md="5"
                  >
                    <AppTextField
                      value="First Step"
                      label="Step Name"
                      readonly
                      disabled
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="5"
                  >
                    <AppTextField
                      label="Allocate Bucks"
                      type="number"
                      placeholder="0"
                      min="0"
                    />
                  </VCol>

                  <VCol
                    cols="12"
                    md="2"
                    class="mt-4"
                  >
                    <VBtn color="primary">
                      Save
                    </VBtn>
                  </VCol>
                </VRow>
                <VRow class="d-flex justify-center align-center">
                  <VCol
                    cols="12"
                    md="5"
                  >
                    <AppTextField
                      value="First Step"
                      label="Step Name"
                      readonly
                      disabled
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="5"
                  >
                    <AppTextField
                      label="Allocate Bucks"
                      type="number"
                      placeholder="0"
                      min="0"
                    />
                  </VCol>

                  <VCol
                    cols="12"
                    md="2"
                    class="mt-4"
                  >
                    <VBtn color="primary">
                      Save
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

                  <VRow class="d-flex justify-center align-center">
                    <VCol
                      cols="12"
                      md="5"
                    >
                      <AppTextField
                        value="First Step"
                        label="Step Name"
                        readonly
                        disabled
                      />
                    </VCol>
                    <VCol
                      cols="12"
                      md="5"
                    >
                      <AppTextField
                        label="Allocate Bucks"
                        type="number"
                        placeholder="0"
                        min="0"
                      />
                    </VCol>

                    <VCol
                      cols="12"
                      md="2"
                      class="mt-4"
                    >
                      <VBtn color="primary">
                        Save
                      </VBtn>
                    </VCol>
                  </VRow>
                  <VRow class="d-flex justify-center align-center">
                    <VCol
                      cols="12"
                      md="5"
                    >
                      <AppTextField
                        value="First Step"
                        label="Step Name"
                        readonly
                        disabled
                      />
                    </VCol>
                    <VCol
                      cols="12"
                      md="5"
                    >
                      <AppTextField
                        label="Allocate Bucks"
                        type="number"
                        placeholder="0"
                        min="0"
                      />
                    </VCol>

                    <VCol
                      cols="12"
                      md="2"
                      class="mt-4"
                    >
                      <VBtn color="primary">
                        Save
                      </VBtn>
                    </VCol>
                  </VRow>
                </vrow>
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
                <VRow class="d-flex justify-center align-center">
                  <VCol
                    cols="12"
                    md="5"
                  >
                    <AppTextField
                      value="First Step"
                      label="Step Name"
                      readonly
                      disabled
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="5"
                  >
                    <AppTextField
                      label="Allocate Bucks"
                      type="number"
                      placeholder="0"
                      min="0"
                    />
                  </VCol>

                  <VCol
                    cols="12"
                    md="2"
                    class="mt-4"
                  >
                    <VBtn color="primary">
                      Save
                    </VBtn>
                  </VCol>
                </VRow>
                <VRow class="d-flex justify-center align-center">
                  <VCol
                    cols="12"
                    md="5"
                  >
                    <AppTextField
                      value="First Step"
                      label="Step Name"
                      readonly
                      disabled
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="5"
                  >
                    <AppTextField
                      label="Allocate Bucks"
                      type="number"
                      placeholder="0"
                      min="0"
                    />
                  </VCol>

                  <VCol
                    cols="12"
                    md="2"
                    class="mt-4"
                  >
                    <VBtn color="primary">
                      Save
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
                <VRow class="d-flex justify-center align-center">
                  <VCol
                    cols="12"
                    md="5"
                  >
                    <AppTextField
                      value="First Step"
                      label="Step Name"
                      readonly
                      disabled
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="5"
                  >
                    <AppTextField
                      label="Allocate Bucks"
                      type="number"
                      placeholder="0"
                      min="0"
                    />
                  </VCol>

                  <VCol
                    cols="12"
                    md="2"
                    class="mt-4"
                  >
                    <VBtn color="primary">
                      Save
                    </VBtn>
                  </VCol>
                </VRow>
                <VRow class="d-flex justify-center align-center">
                  <VCol
                    cols="12"
                    md="5"
                  >
                    <AppTextField
                      value="First Step"
                      label="Step Name"
                      readonly
                      disabled
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    md="5"
                  >
                    <AppTextField
                      label="Allocate Bucks"
                      type="number"
                      placeholder="0"
                      min="0"
                    />
                  </VCol>

                  <VCol
                    cols="12"
                    md="2"
                    class="mt-4"
                  >
                    <VBtn color="primary">
                      Save
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

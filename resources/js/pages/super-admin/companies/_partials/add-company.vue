<template>
  <VCard flat>
    <VCardText>
      <VRow>
        <VCol cols="12">
          <h3 class="text-h5">
            Add Company Details
          </h3>
          <p class="text-body-1 text-muted">
            Manage company's essential information to keep profile complete and professional.
          </p>
        </VCol>
      </VRow>
      <VForm
        ref="addCompanyForm"
        @submit.prevent="submitAddCompanyForm"
      >
        <VRow>
          <VCol
            md="4"
            cols="12"
          >
            <AppTextField
              v-model="newCompanyDetails.name"
              label="Company Name*"
              :rules="[requiredValidator]"
              placeholder="Company Name"
              autofocus
            />
          </VCol>
          <VCol cols="12">
            <h3 class="text-high-emphasis text-primary">
              Company Site Address
            </h3>
          </VCol>
          <VCol cols="12">
            <VRadioGroup
              v-model="selectedDomainType"
              inline
            >
              <VRadio
                value="subdomain"
                label="Subdomain"
                class="me-4"
              />
              <VRadio
                value="custom"
                label="Custom Domain"
              />
            </VRadioGroup>
          </VCol>
  
          <VCol
            md="6"
            cols="12"
          >
            <!-- Subdomain Field -->
            <AppTextField
              v-if="selectedDomainType === 'subdomain'"
              :value="generateSubdomain"
              prepend-inner-icon=""
              label="Subdomain"
              class="cursor-not-allowed"
              disabled
            >
              <template #prepend-inner>
                <span>{{ prependText }}</span>
              </template>
            </AppTextField>
  
            <!-- Custom Domain Field -->
            <AppTextField
              v-if="selectedDomainType === 'custom'"
              v-model="newCompanyDetails.customDomain"
              prepen-inner-icon=""
              label="Custom Domain"
              :rules="[customDomainValidator]"
              placeholder="Type your custom domain"
            >
              <template #prepend-inner>
                <span>{{ prependText }}</span>
              </template>
            </AppTextField>
          </VCol>
          <VCol
            v-if="selectedDomainType === 'custom'"
            cols="12"
          >
            <VAlert
              color="primary"
              variant="outlined"
              prominent
            >
              <p><strong>Please follow these instructions to point your domain to the client access area:</strong></p>
              <ol class="ms-8">
                <li> Create a 'CNAME' record using your desired hostname (e.g., clients.yourdomain.com).</li>
                <li> Point the CNAME to '<strong>cname.clientseoreport.com</strong>'.</li>
                <li> Wait an hour or more for it to propagate.</li>
                <li> Once complete, accessing this domain should redirect to your client access area.</li>
              </ol>
              <p class="mt-2">
                <strong>Note:</strong> Cloudflare routing is incompatible with custom domains. Please disable Cloudflare for your custom domain if you're using it.
              </p>
            </VAlert>
          </VCol>
          <VCol
            cols="12"
            class="pb-0 mb-0"
          >
            <h3 class="text-high-emphasis text-primary">
              Admin Details
            </h3>
          </VCol>
          <VCol cols="6">
            <AppTextField
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
                :disabled="getLoadStatus === 1"
                @click="addCompanyForm?.validate()"
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
                @click="resetForm"
              >
                Cancel
              </VBtn>
            </div>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>
  
<script setup>
import { VForm } from 'vuetify/components/VForm'
import { ref, nextTick } from 'vue'
import { useToast } from "vue-toastification"
import { useCompanyStore } from "@/store/companies"
import { useRouter } from 'vue-router'
  
const router = useRouter()
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
  
const selectedDomainType = ref('subdomain')
  
const resetFormFields = () => {
  addCompanyForm.value?.reset()
  assignEmptyValeus()
  resetErrors()
}
  
async function submitAddCompanyForm() {
  addCompanyForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        resetErrors()
        company = await companyStore.create(newCompanyDetails.value)
        if(getErrors)
        {
          showError()
        }
        else{
          isLoading.value = true
          toast.success('Company added successfully', { timeout: 1000 })
          resetFormFields()
          isLoading.value = false
          router.push({ name: 'companies-list' })
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
  if (getStatusCode === 500) {
    toast.error('Something went wrong. Please try again later.')
  } else {
    addingErrors.value = getErrors
  }
}
  
const resetErrors = () => {
  addingErrors.value = Object.fromEntries(Object.keys(addingErrors.value).map(key => [key, '']))
}
  
const generateSubdomain = computed(() => {
  const companyName = newCompanyDetails.value.name || ''
  const slug = companyName.trim().toLowerCase().replace(/\s+/g, '-')
  const domainHost = import.meta.env.VITE_APP_HOST
  
  return `${slug}.${domainHost}`
})
  
const customDomainValidator = value => {
  const domainRegex = /^(https?:\/\/)?([a-zA-Z\d-]+\.)+[a-zA-Z]{2,}$/
    
  return domainRegex.test(value) || 'Enter a valid custom domain'
}
  
const prependText = computed(() => {
  const host = import.meta.env.VITE_APP_HOST 
    
  return host === 'localhost' ? 'http://' : 'https://'
})

const getErrors = computed(() => {
  return companyStore.getErrors
})

const getLoadStatus = computed(() => {
  return companyStore.getLoadStatus
})

const getStatusCode = computed(() => {
  return companyStore.getStatusCode
})
</script>
  
<template>
  <Loader v-if="isLoading" />
  <VRow>
    <VCol cols="12">
      <VCard title="Profile Details">
        <VCardText class="d-flex">
          <!-- 👉 Avatar -->
          <VAvatar
            v-if="loggedInUser?.info?.avatar"
            rounded
            size="100"
            class="me-6"
            :image="getImageUrl(loggedInUser?.info?.avatar.path)"
          />

          <VAvatar
            v-else
            rounded
            size="100"
            class="me-6"
            :image="defaultAvatar"
          />

          <!-- 👉 Upload Photo -->
          <form class="d-flex flex-column justify-center gap-4">
            <div class="d-flex flex-wrap gap-2">
              <VBtn
                color="primary"
                @click="refInputEl?.click()"
              >
                <VIcon
                  icon="tabler-cloud-upload"
                  class="d-sm-none"
                />
                <span class="d-none d-sm-block">Upload new photo</span>
              </VBtn>

              <input
                ref="refInputEl"
                type="file"
                name="file"
                accept=".jpeg,.png,.jpg,GIF"
                hidden
                @input="changeAvatar"
              >
            </div>

            <p class="text-body-1 mb-0">
              Allowed JPG, JPEG, GIF or PNG. Max size of 1MB
            </p>
          </form>
        </VCardText>

        <VDivider />

        <VCardText class="pt-2">
          <!-- 👉 Form -->
          <VForm 
            ref="updateMemberForm"
            class="mt-6"
            @submit.prevent="submitUpdateMemberForm"
          >
            <VRow>
              <!-- 👉 First Name -->
              <VCol
                md="6"
                cols="12"
              >
                <AppTextField
                  v-model="accountData.name_first"
                  placeholder="John"
                  variant="outlined"
                  label="First Name *"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.name_first"
                />
              </VCol>

              <!-- 👉 Last Name -->
              <VCol
                md="6"
                cols="12"
              >
                <AppTextField
                  v-model="accountData.name_last"
                  placeholder="Doe"
                  label="Last Name *"
                  variant="outlined"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.name_last"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol
                cols="12"
                md="6"
              >
                <AppTextField
                  v-model="accountData.email"
                  label="E-mail"
                  placeholder="johndoe@gmail.com"
                  type="email *"
                  variant="outlined"
                  :rules="[requiredValidator, emailValidator]"
                  :error-messages="editErrors.email"
                />
              </VCol>

              <!-- 👉 Phone -->
              <VCol
                cols="12"
                md="6"
              >
                <AppTextField
                  v-model="accountData.phone"
                  v-mask="'(###) ###-####'"
                  label="Phone *"
                  placeholder="(123) 456-7890"
                  variant="outlined"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.phone"
                />
              </VCol>

              <!-- 👉 Address -->
              <VCol
                cols="12"
                md="6"
              >
                <AppTextField
                  v-model="accountData.address"
                  label="Address *"
                  placeholder="123 Main St, New York, NY 10001"
                  variant="outlined"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.address"
                />
              </VCol>

              <!-- 👉 City -->
              <VCol
                cols="12"
                md="6"
              >
                <AppTextField
                  v-model="accountData.city"
                  label="City *"
                  placeholder="New York"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.city"
                />
              </VCol>

              <!-- 👉 Zip Code -->
              <VCol
                cols="12"
                md="6"
              >
                <AppTextField
                  v-model="accountData.zip"
                  label="Zip Code *"
                  placeholder="10001"
                  variant="outlined"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.zip"
                />
              </VCol>

              <!-- 👉 Form Actions -->
              <VCol
                cols="12"
                class="d-flex flex-wrap gap-4"
              >
                <VBtn
                  type="submit"
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
                    Save
                  </span>
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<script setup>
import defaultAvatar from '@images/avatars/default-avatar.png'
import Loader from '@/components/Loader.vue'
import { useUserStore } from "@/store/users"
import { useToast } from "vue-toastification"
import { computed, onMounted, ref } from 'vue'

onMounted(() => {
  getUser()
  setUserDetails()
})

const userStore = useUserStore()
const toast = useToast()
const isLoading = ref(false)

const accountData = ref({
  avatar: '',
  name_first: '',
  name_last: '',
  email: '',
  phone: '',
  city: '',
  address: '',
  zip: '',
})

const editErrors = ref({
  name_first: '',
  name_last: '',
  email: '',
  phone: '',
  city: '',
  address: '',
  zip: '',
})

const refInputEl = ref()
const updateMemberForm = ref()

async function changeAvatar(file) {
  if (!['image/jpeg', 'image/jpg', 'image/png', 'image/gif'].includes(file.target.files[0].type) || file.target.files[0].size > 1024 * 1024) {
    toast.error('Please upload a valid image file.')

    return
  }

  isLoading.value = true

  const fileReader = new FileReader()
  const { files } = file.target
  if (files && files.length) {
    fileReader.readAsDataURL(files[0])
    fileReader.onload = () => {
      if (typeof fileReader.result === 'string')
        accountData.value.avatar = fileReader.result
    }
  }
  await userStore.updateUserImage(files[0], userStore.getUser?.uuid)
  if(getErrors.value){
    toast.error('Something went wrong. Please try again later.')
  }else{
    await getUser()
    toast.success('Image uploaded successfully', { timeout: 1000 })
  }
}

async function submitUpdateMemberForm() {
  updateMemberForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        const userUuid = userStore.getUser?.uuid
        const userRole = userStore.getUser?.role
        const userState = userStore.getUser?.state

        await userStore.update({ ...accountData.value, uuid: userUuid, role: userRole, state: userState })
        if(getErrors.value)
        {
          showError()
        }
        else{
          toast.success('Record updated successfully', { timeout: 1000 })
        }
      } catch (error) {
        toast.error('Failed to add member:', error)
      }
    }
  })
}

const getUser = async () => {
  isLoading.value = true
  let user = JSON.parse(localStorage.getItem('user'))
  const uuid = user.user?.uuid

  await userStore.show(uuid)
  isLoading.value = false
}

const setUserDetails = async () => {
  let userDetails = userStore.getUser

  // accountData.value.avatar = userDetails?.info?.avatar
  accountData.value.name_first = userDetails?.name_first
  accountData.value.name_last = userDetails?.name_last
  accountData.value.email = userDetails?.email
  accountData.value.phone = userDetails?.info?.phone
  accountData.value.city = userDetails?.info?.city
  accountData.value.address = userDetails?.info?.address
  accountData.value.zip = userDetails?.info?.zip
}

const showError = () => {
  if(getStatusCode.value == 500 || getStatusCode.value == 404){
    toast.error('Something went wrong. Please try again later.')
  }
  else{
    editErrors.value = getErrors.value
  }
}

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

const getLoadStatus = computed(() => {
  return userStore.getLoadStatus
})

const getStatusCode = computed(() => {
  return userStore.getStatusCode
})

const getErrors = computed(() => {
  return userStore.getErrors
})

const loggedInUser = computed(() => {
  return userStore.getUser
})

watch(() => userStore.getUser, () => {
  setUserDetails()
}, { immediate: true })
</script>
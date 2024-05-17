<template>
  <VRow>
    <VCol cols="12">
      <VCard title="Profile Details">
        <VCardText class="d-flex">
          <!-- 👉 Avatar -->
          <VAvatar
            rounded
            size="100"
            class="me-6"
            :image="accountData.avatar"
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

              <VBtn
                type="reset"
                color="secondary"
                variant="tonal"
                @click="resetAvatar"
              >
                <span class="d-none d-sm-block">Reset</span>
                <VIcon
                  icon="tabler-refresh"
                  class="d-sm-none"
                />
              </VBtn>
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
                  label="Phone Number *"
                  placeholder="+1 (917) 543-9876"
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

                <!-- <VBtn
                  color="secondary"
                  variant="tonal"
                  type="reset"
                  @click.prevent="resetForm"
                >
                  Reset
                </VBtn> -->
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<script setup>
import avatar1 from '@images/avatars/avatar-1.png'
import { useUserStore } from "@/store/users"
import { useToast } from "vue-toastification"
import { computed, onMounted, ref } from 'vue'

onMounted(() => {
  setUserDetails()
})

const userStore = useUserStore()
const toast = useToast()

const accountData = ref({
  avatar: avatar1,
  name_first: '',
  name_last: '',
  email: 'm',
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

const changeAvatar = file => {
  const fileReader = new FileReader()
  const { files } = file.target
  if (files && files.length) {
    fileReader.readAsDataURL(files[0])
    fileReader.onload = () => {
      if (typeof fileReader.result === 'string')
        accountData.value.avatar = fileReader.result
    }
  }
}

// reset avatar image
const resetAvatar = () => {
  accountData.value.avatar = avatar1
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

// const resetForm = () => {
//   accountData.value = Object.fromEntries(Object.keys(accountData.value).map(key => [key, '']))
// }

const showError = () => {
  if(getStatusCode.value == 500 || getStatusCode.value == 404){
    toast.error('Something went wrong. Please try again later.')
  }
  else{
    editErrors.value = getErrors.value
  }
}

const setUserDetails = async () => {
  const user = JSON.parse(localStorage.getItem('user'))
  const loggedInUserUuid = user.user.uuid

  const users = userStore.getUsers
  const userIndex = users.findIndex(user => user.uuid === loggedInUserUuid)
  const userDetails = users[userIndex]

  accountData.value = {
    ...accountData.value,
    name_first: userDetails?.name_first,
    name_last: userDetails?.name_last,
    email: userDetails?.email,
    phone: userDetails?.info?.phone,
    city: userDetails?.info?.city,
    address: userDetails?.info?.address,
    zip: userDetails?.info?.zip,
  }
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
</script>
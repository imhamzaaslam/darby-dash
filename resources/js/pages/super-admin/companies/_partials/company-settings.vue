<template>
  <VContainer fluid>
    <!-- Dashboard Header -->
    <VRow class="dashboard-header">
      <VCol cols="12">
        <h3 class="text-h5">
          Company Settings
        </h3>
        <p class="text-body-1 text-muted">
          Manage company's details, branding, users, and settings with ease.
        </p>
      </VCol>
    </VRow>
    <VRow>
      <!-- Company Info Section -->
      <VCol
        cols="12"
        md="4"
      >
        <VCard class="mb-4">
          <VCardTitle class="d-flex justify-space-between align-center">
            <h6 class="text-h6">
              Company Details
            </h6>
            <VSwitch
              v-model="isActive"
              label="Active"
            />
          </VCardTitle>

          <VCardText class="px-5 pb-3">
            <AppTextField
              v-model="companyDetails.name"
              label="Name"
              outlined
              dense
              :rules="[nameRule]"
            />
            <div class="mt-3">
              <label class="text-sm text-high-emphasis">Domain Address</label>
              <a
                href="https://example.com"
                target="_blank"
                rel="noopener noreferrer"
                class="text-primary text-decoration-underline d-block"
              >
                https://example.com
              </a>
            </div>
          </VCardText>
          <VCardActions class="px-5 py-3 d-flex justify-end">
            <VBtn
              color="primary"
              :loading="isLoading"
              size="small"
              variant="flat"
              @click="updateCompanyInfo"
            >
              Save
            </VBtn>
          </VCardActions>
        </VCard>
      </VCol>
  
      <!-- Branding Section -->
      <VCol
        cols="12"
        md="4"
      >
        <VCard
          style="height: 234px;"
          @mouseover="onLogoHover(true)"
          @mouseleave="onLogoHover(false)"
        >
          <VCardTitle class="d-flex justify-space-between align-center">
            <h5 class="text-h6 mt-2">
              Company Logo
            </h5>
            <div
              v-if="isLogoCardHovered"
              class="d-flex gap-2"
            >
              <VTooltip location="top">
                <template #activator="{ props }">
                  <VBtn
                    v-bind="props"
                    variant="tonal"
                    icon="tabler-download"
                    size="x-small"
                    @click="downloadQrCode"
                  />
                </template>
                <span>Download</span>
              </VTooltip>
              <VTooltip location="top">
                <template #activator="{ props }">
                  <VBtn
                    v-bind="props"
                    variant="tonal"
                    size="x-small"
                    icon="tabler-upload"
                    @click="uploadQrCode"
                  />
                </template>
                <span>Upload</span>
              </VTooltip>
              <VTooltip location="top">
                <template #activator="{ props }">
                  <VBtn
                    v-bind="props"
                    variant="tonal"
                    size="x-small"
                    icon="tabler-trash"
                    @click="deleteQrCode"
                  />
                </template>
                <span>Delete</span>
              </VTooltip>
            </div>
          </VCardTitle>
          <VCardText class="text-center">
            <VImg
              v-if="logo"
              :src="logo"
              max-height="140"
              class="mt-4"
              alt="Company Logo"
            />
            <template v-else>
              <div class="text-center py-15">
                <p class="text-body-2 text-high-emphasis">
                  No company logo uploaded yet.
                </p>
              </div>
            </template>
          </VCardText>
        </VCard>
      </VCol>

      <VCol
        cols="12"
        md="4"
      >
        <VCard
          style="height: 234px;"
          @mouseover="onFaviconHover(true)"
          @mouseleave="onFaviconHover(false)"
        >
          <VCardTitle class="d-flex justify-space-between align-center">
            <h5 class="text-h6 mt-2">
              Favicon
            </h5>
            <div
              v-if="isFaviconCardHovered"
              class="d-flex gap-2"
            >
              <VTooltip location="top">
                <template #activator="{ props }">
                  <VBtn
                    v-bind="props"
                    variant="tonal"
                    icon="tabler-download"
                    size="x-small"
                    @click="downloadQrCode"
                  />
                </template>
                <span>Download</span>
              </VTooltip>
              <VTooltip location="top">
                <template #activator="{ props }">
                  <VBtn
                    v-bind="props"
                    variant="tonal"
                    size="x-small"
                    icon="tabler-upload"
                    @click="uploadQrCode"
                  />
                </template>
                <span>Upload</span>
              </VTooltip>
              <VTooltip location="top">
                <template #activator="{ props }">
                  <VBtn
                    v-bind="props"
                    variant="tonal"
                    size="x-small"
                    icon="tabler-trash"
                    @click="deleteQrCode"
                  />
                </template>
                <span>Delete</span>
              </VTooltip>
            </div>
          </VCardTitle>

          <VCardText class="text-center">
            <VImg
              v-if="qrCode"
              :src="qrCode"
              max-height="140"
              class="mt-4"
              alt="Favicon"
            />
            <template v-else>
              <div class="text-center py-15">
                <p class="text-body-2 text-high-emphasis">
                  No favicon uploaded yet.
                </p>
              </div>
            </template>
          </VCardText>
        </VCard>
      </VCol>

      <!-- Theme Customization Section -->
      <VCol
        cols="12"
        md="6"
      >
        <VCard>
          <VCardTitle>
            <h5 class="text-h6 mt-2">
              Theme Customization
            </h5>
          </VCardTitle>
          <VCardText>
            <div class="d-flex justify-space-around">
              <VColorPicker
                v-model="primaryColor"
                :swatches="swatches"
                class="ma-2"
                width="400"
                show-swatches
              />
            </div>
          </VCardText>
          <VCardActions class="px-5 d-flex justify-end">
            <VBtn
              color="primary"
              :loading="isLoading"
              size="small"
              variant="flat"
              @click="saveColor"
            >
              Save
            </VBtn>
          </VCardActions>
        </VCard>
      </VCol>
  
      <!-- Users & Roles Section -->
      <VCol
        cols="12"
        md="6"
      >
        <VCard>
          <VCardTitle class="d-flex mb-4 justify-space-between align-center">
            <h5 class="text-h6 mt-2">
              Manage Admins
            </h5>
            <VBtn
              variant="elevated"
              color="primary"
              size="x-small"
              prepend-icon="tabler-plus"
              @click="isAddAdminDialogVisible = true"
            >
              New Admin
            </VBtn>
          </VCardTitle>

          <VCardText class="mt-6">
            <VList class="card-list">
              <VListItem
                v-for="admin in admins"
                :key="admin.name"
              >
                <template #prepend>
                  <VBadge
                    dot
                    location="top end"
                    offset-x="1"
                    offset-y="1"
                    color="warning"
                  >
                    <VAvatar
                      size="34"
                      :class="admin.avatar ? '' : 'text-white bg-primary'"
                      :variant="!admin.avatar ? 'tonal' : ''"
                    >
                      <span>{{ avatarText(admin.name) }}</span>
                    </VAvatar>
                  </VBadge>
                </template>

                <VListItemTitle class="text-sm text-high-emphasis font-weight-semibold">
                  {{ admin.name }}
                </VListItemTitle>
                <VListItemSubtitle>
                  {{ admin.email }}
                </VListItemSubtitle>

                <template #append>
                  <div class="d-flex align-center">
                    <VBtn
                      variant="tonal"
                      size="x-small"
                      icon="tabler-edit"
                      class="me-2"
                      @click="editAdmin(admin)"
                    />
                    <VBtn
                      variant="tonal"
                      size="x-small"
                      color="error"
                      icon="tabler-trash"
                      @click="deleteAdmin(admin)"
                    />
                  </div>
                </template>
              </VListItem>
            </VList>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </VContainer>
  <VDialog
    v-model="isAddAdminDialogVisible"
    persistent
    class="v-dialog-sm"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="isAddAdminDialogVisible = !isAddAdminDialogVisible" />

    <!-- Dialog Content -->
    <VCard title="Add Admin Details">
      <VForm
        ref="addAdminForm"
        @submit.prevent=""
      >
        <VCardText>
          <VRow>
            <VCol cols="6">
              <AppTextField
                v-model="newAdminDetails.name_first"
                label="First Name *"
                :rules="[requiredValidator]"
                placeholder="First Name"
              />
            </VCol>

            <!-- Last Name -->
            <VCol cols="6">
              <AppTextField
                v-model="newAdminDetails.name_last"
                label="Last Name *"
                :rules="[requiredValidator]"
                placeholder="Last Name"
              />
            </VCol>

            <!-- Email -->
            <VCol cols="6">
              <AppTextField
                v-model="newAdminDetails.email"
                label="Email *"
                :rules="[requiredValidator, emailValidator]"
                placeholder="Email"
              />
            </VCol>

            <!-- Phone Mask Field -->
            <VCol cols="6">
              <AppTextField
                v-model="newAdminDetails.phone"
                v-mask="'(###) ###-####'"
                label="Phone *"
                :rules="[requiredValidator]"
                placeholder="(123) 456-7890"
              />
            </VCol>

            <!-- Password -->
            <VCol cols="6">
              <AppTextField
                v-model="newAdminDetails.password"
                label="Password *"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                :rules="[requiredValidator]"
                placeholder="Password"
                autocomplete="new-password"
                @click:append-inner="isPasswordVisible = !isPasswordVisible"
              />
            </VCol>

            <!-- Confirm Password -->
            <VCol cols="6">
              <AppTextField
                v-model="newAdminDetails.confirmPassword"
                :type="isConfirmPasswordVisible ? 'text' : 'password'"
                label="Confirm Password *"
                :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                :rules="[requiredValidator, confirmedValidator(newAdminDetails.confirmPassword, newAdminDetails.password)]"
                placeholder="Confirm Password"
                @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
              />
            </VCol>
          </VRow>
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            @click="isAddAdminDialogVisible = false"
          >
            Cancel
          </VBtn>
          <VBtn
            type="submit"
            @click="addAdminForm?.validate()"
          >
            Save
          </VBtn>
        </VCardText>
      </VForm>
    </VCard>
  </VDialog>
  <VDialog
    v-model="isEditAdminDialogVisible"
    persistent
    class="v-dialog-sm"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="isEditAdminDialogVisible = !isEditAdminDialogVisible" />

    <!-- Dialog Content -->
    <VCard title="Edit Admin Details">
      <VForm
        ref="editAdminForm"
        @submit.prevent=""
      >
        <VCardText>
          <VRow>
            <VCol cols="6">
              <AppTextField
                v-model="editAdminDetails.name_first"
                label="First Name *"
                :rules="[requiredValidator]"
                placeholder="First Name"
              />
            </VCol>

            <!-- Last Name -->
            <VCol cols="6">
              <AppTextField
                v-model="editAdminDetails.name_last"
                label="Last Name *"
                :rules="[requiredValidator]"
                placeholder="Last Name"
              />
            </VCol>

            <!-- Email -->
            <VCol cols="6">
              <AppTextField
                v-model="editAdminDetails.email"
                label="Email *"
                :rules="[requiredValidator, emailValidator]"
                placeholder="Email"
              />
            </VCol>

            <!-- Phone Mask Field -->
            <VCol cols="6">
              <AppTextField
                v-model="editAdminDetails.phone"
                v-mask="'(###) ###-####'"
                label="Phone *"
                :rules="[requiredValidator]"
                placeholder="(123) 456-7890"
              />
            </VCol>

            <!-- Password -->
            <VCol cols="6">
              <AppTextField
                v-model="editAdminDetails.password"
                label="Password *"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                :rules="[requiredValidator]"
                placeholder="Password"
                autocomplete="new-password"
                @click:append-inner="isPasswordVisible = !isPasswordVisible"
              />
            </VCol>

            <!-- Confirm Password -->
            <VCol cols="6">
              <AppTextField
                v-model="editAdminDetails.confirmPassword"
                :type="isConfirmPasswordVisible ? 'text' : 'password'"
                label="Confirm Password *"
                :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                :rules="[requiredValidator, confirmedValidator(editAdminDetails.confirmPassword, editAdminDetails.password)]"
                placeholder="Confirm Password"
                @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
              />
            </VCol>
          </VRow>
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            @click="isEditAdminDialogVisible = false"
          >
            Cancel
          </VBtn>
          <VBtn
            type="submit"
            @click="editAdminForm?.validate()"
          >
            Save
          </VBtn>
        </VCardText>
      </VForm>
    </VCard>
  </VDialog>
</template>
  
<script setup>
import { ref, computed } from 'vue'
import Swal from 'sweetalert2'
  
const isLoading = ref(false)
  
const companyDetails = ref({
  name: '',
  email: '',
})
  
const logo = ref(null)
const isActive = ref(true)
const qrCode = ref(null)
const isLogoCardHovered = ref(false)
const isFaviconCardHovered = ref(false)
const primaryColor = ref("#a12592")
const isEditAdminDialogVisible = ref(false)
const editAdminForm = ref('')
const isAddAdminDialogVisible = ref(false)
const addAdminForm = ref('')
const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const editAdminDetails = ref({})

const newAdminDetails = ref({
  name: null,
  name_first: '',
  name_last: '',
  email: '',
  phone: '',
  password: '',
  confirmPassword: '',
})

const swatches = ref([
  ['#FF0000', '#AA0000', '#550000'],
  ['#FFFF00', '#AAAA00', '#555500'],
  ['#00FF00', '#00AA00', '#005500'],
  ['#00FFFF', '#00AAAA', '#005555'],
  ['#0000FF', '#0000AA', '#000055'],
])

const admins = [
  {
    avatar: null,
    name: 'Admin 1',
    email: 'admin1@gmail.com',
  },
  {
    avatar: null,
    name: 'Admin 2',
    email: 'admin2@gmail.com',
  },
  {
    avatar: null,
    name: 'Admin 3',
    email: 'admin3@gmail.com',
  },
  {
    avatar: null,
    name: 'Admin 4',
    email: 'admin4@gmail.com',
  },
  {
    avatar: null,
    name: 'Admin 5',
    email: 'admin5@gmail.com',
  },
  {
    avatar: null,
    name: 'Admin 6',
    email: 'admin6@gmail.com',
  },
  {
    avatar: null,
    name: 'Admin 7',
    email: 'admin7@gmail.com',
  },
  {
    avatar: null,
    name: 'Admin 8',
    email: 'admin8@gmail.com',
  },
]
  
const nameRule = [v => !!v || "Company name is required"]

const editAdmin = admin => {
  editAdminDetails.value = { ...admin }
  isEditAdminDialogVisible.value = true
}

const onLogoHover = state => {
  isLogoCardHovered.value = state
}

const onFaviconHover = state => {
  isFaviconCardHovered.value = state
}
  
const updateCompanyInfo = () => {
  isLoading.value = true
  console.log('Company Info updated:', companyDetails.value)
  isLoading.value = false
}
  
const saveColor = () => {
  isLoading.value = true
  console.log('Primary color saved:', primaryColor.value)
  isLoading.value = false
}
  
const sendCredentials = () => {
  isLoading.value = true
  console.log('Sending credentials...')
  isLoading.value = false
}
  
const addAdmin = () => {
  console.log('Adding admin...')
}
  
const viewUsers = () => {
  console.log('Viewing users...')
}
  
const viewEmailHistory = () => {
  console.log('Viewing email history...')
}

const downloadQrCode = () => {
  console.log("Downloading QR Code...")
}

const uploadQrCode = () => {
  console.log("Uploading QR Code...")
}

const deleteQrCode = () => {
  qrCode.value = null
  console.log("QR Code deleted.")
}

const deleteAdmin = async user => {
  try {

    const confirmDelete = await Swal.fire({
      title: "Are you sure?",
      html: `
        <div>
        <p>
            Do you want to delete <strong>${user.name}</strong>?
            <br>
            <small>This action will also delete all associated tasks.</small>
        </p>
        </div>
      `,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#a12592",
      cancelButtonColor: "#808390",
      confirmButtonText: "Yes, delete it!",
      didOpen: () => {
        document.querySelector('.swal2-confirm').blur()
      },
    })
  } catch (error) {
    console.log('Failed to delete project list:', error)
  }
}
</script>
  
<style scoped>
.v-img {
display: block;
margin: auto;
}
.card-list {
  --v-card-list-gap: 19px;
}
</style>
  
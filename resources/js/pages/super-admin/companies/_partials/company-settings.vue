<template>
  <VContainer fluid>
    <!-- Dashboard Header -->
    <VRow class="pb-0">
      <VCol cols="12">
        <h3 class="text-h5">
          Company Settings
        </h3>
        <p class="text-body-1 text-muted">
          Manage company's details, branding, users, and settings with ease.
        </p>
      </VCol>
    </VRow>
    <VRow class="pt-0 mt-0">
      <VCol
        cols="12"
        md="2"
        class="custom-tabs"
      >
        <ul>
          <li
            v-for="(tab, index) in tabs"
            :key="index"
            :class="{ active: activeTab === index }"
            @click="selectTab(index)"
          >
            {{ tab }}
          </li>
        </ul>
      </VCol>
      <VCol
        cols="12"
        md="10"
      >
        <VRow
          v-if="activeTab === 0"
          class="ms-1"
        >
          <!-- Company Info Section -->
          <VCol
            cols="12"
            md="12"
          >
            <VCard class="px-3 py-2">
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
                  :style="{ width: '300px' }"
                />
                <div class="mt-3">
                  <div class="d-flex align-items-center justify-content-between">
                    <label class="text-sm text-high-emphasis">
                      Domain Address
                      <VIcon
                        v-if="!isCopied"
                        class="ms-1 text-primary cursor-pointer"
                        icon="tabler-copy"
                        size="20"
                        @click="copyToClipboard"
                      />
                      <span
                        v-else
                        class="ms-1"
                      >
                        <VIcon
                          class="text-success"
                          icon="tabler-check"
                          size="20"
                        />
                        <span class="text-xs font-weight-bold">Copied</span>
                      </span>
                    </label>
                  </div>
                  <a
                    href="https://example.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-primary text-decoration-underline d-block mt-1"
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
            md="6"
          >
            <VCard
              style="height: 241px;"
              class="px-3 py-2"
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
            md="6"
          >
            <VCard
              style="height: 241px;"
              class="px-3 py-2"
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
        </VRow>
        <VRow
          v-if="activeTab === 1"
          class="ms-1"
        >
          <!-- Theme Customization Section -->
          <VCol
            cols="12"
            md="12"
          >
            <VCard class="px-3 py-2">
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
                    width="800"
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
        </VRow>
        <VRow
          v-if="activeTab === 2"
          class="ms-1"
        >
          <!-- Users & Roles Section -->
          <VCol
            cols="12"
            md="12"
          >
            <VCard class="px-3 py-2">
              <VCardTitle class="d-flex mb-4 justify-space-between align-center">
                <h5 class="text-h6">
                  Manage Admins
                </h5>
                <VBtn
                  variant="elevated"
                  color="primary"
                  size="small"
                  prepend-icon="tabler-plus"
                  @click="isAddAdminDialogVisible = true"
                >
                  New Admin
                </VBtn>
              </VCardTitle>

              <VCardText class="mt-7">
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
                          icon
                          color="td-hover"
                          class="ma-2"
                          size="small"
                          rounded="pills"
                          @click.prevent
                        >
                          <VIcon icon="tabler-dots" />
                          <VMenu activator="parent">
                            <VList>
                              <VListItem
                                value="edit"
                                @click="editAdmin(admin)"
                              >
                                Edit
                              </VListItem>
                              <VListItem
                                value="delete"
                                @click="deleteAdmin(admin)"
                              >
                                Delete
                              </VListItem>
                            </VList>
                          </VMenu>
                        </VBtn>
                      </div>
                    </template>
                  </VListItem>
                </VList>
              </VCardText>
            </VCard>
          </VCol>
        </VRow>
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
const isCopied = ref(false)
const activeTab = ref(0)
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
const tabs = ref(["Basic Setting", "Theme Setting", "Users & Roles"])

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

const selectTab = tab => {
  activeTab.value = tab
}

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

const copyToClipboard = () => {
  const domain = "https://example.com"

  navigator.clipboard.writeText(domain).then(() => {
    isCopied.value = true
    setTimeout(() => {
      isCopied.value = false 
    }, 1500)
  })
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
.custom-tabs {
  background-color: white; 
  margin-top: 12px;
  border-radius: 6px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.custom-tabs ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
}

.custom-tabs li {
  padding: 10px 15px;
  cursor: pointer;
  margin-bottom: 5px;
  color: #555;
  background: white;
  font-weight: 500;
  transition: all 0.3s ease-in-out;
  border-bottom: 1px solid #ddd;
}
.custom-tabs li:first-child {
  margin-top: 10px;
}
.custom-tabs li:last-child {
  border-bottom: none;
}

.custom-tabs li.active,
.custom-tabs li:hover {
  background-color: #a12592;
  color: white;
  border-radius: 3px;
}
</style>
  
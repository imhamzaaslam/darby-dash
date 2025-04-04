<template>
  <!-- Dashboard Header -->
  <VRow class="pb-0">
    <VCol cols="12">
      <div class="d-flex align-center">
        <VAvatar
          icon="tabler-settings"
          size="36"
          class="me-2"
          color="primary"
          variant="tonal"
        />
        <h3 class="text-primary">
          {{ company.name }} Settings
        </h3>
      </div>
      <p class="text-body-1 text-muted mt-1">
        Manage {{ company.name }}'s details, branding, users, and settings with ease.
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
          :class="{ active: activeTab == tab.tab }"
          @click="selectTab(tab.tab)"
        >
          {{ tab.title }}
        </li>
      </ul>
    </VCol>
    <VCol
      cols="12"
      md="10"
    >
      <VRow
        v-if="activeTab == 'basic-setting'"
        class="ms-1"
      >
        <!-- Company Info Section -->
        <VCol
          v-if="authStore.isSuperAdmin"
          cols="12"
          md="12"
        >
          <VCard class="px-3 py-2">
            <VCardTitle class="d-flex justify-space-between align-center">
              <h6 class="text-h6 mt-2">
                Company Details
              </h6>
              <VSwitch
                v-if="authStore.isSuperAdmin"
                v-model="isActive"
                label="Active"
                :true-value="1"
                :false-value="0"
                @change="handleActiveChange"
              />
            </VCardTitle>

            <VCardText class="px-4 pb-3 mt-3">
              <!--
                <AppTextField
                v-model="companyDetails.name"
                label="Name"
                class="cursor-not-allowed"
                outlined
                dense
                :style="{ width: '300px' }"
                disabled
                /> 
              -->
              <label><span class="text-high-emphasis text-h6">Name:</span> {{ companyDetails.name }}</label>
              <div
                v-if="authStore.isSuperAdmin"
                class="mt-3"
              >
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
                  :href="formattedUrl(company.url)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="text-primary text-decoration-underline d-block mt-1"
                >
                  {{ company.url ? formattedUrl(company.url) : '' }}
                </a>
              </div>
            </VCardText>
            <!--
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
            -->
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
                      @click="downloadAsset(company?.logo)"
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
                      @click="uploadLogo"
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
                      @click="deleteLogo(company?.logo?.uuid)"
                    />
                  </template>
                  <span>Delete</span>
                </VTooltip>
              </div>
            </VCardTitle>
            <VCardText class="text-center">
              <VImg
                v-if="company?.logo"
                :src="getImageUrl(company?.logo?.path)"
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
            <input
              ref="logoInputRef"
              :key="inputLogoKey"
              type="file"
              accept="image/*"
              class="d-none"
              @change="handleLogoChange"
            >
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
                      @click="downloadAsset(company?.favicon)"
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
                      @click="uploadFavicon"
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
                      @click="deleteFavicon(company?.favicon?.uuid)"
                    />
                  </template>
                  <span>Delete</span>
                </VTooltip>
              </div>
            </VCardTitle>

            <VCardText class="text-center">
              <VImg
                v-if="company?.favicon"
                :src="getImageUrl(company?.favicon?.path)"
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
            <input
              ref="faviconInputRef"
              :key="inputFaviconKey"
              type="file"
              accept="image/*"
              class="d-none"
              @change="handleFaviconChange"
            >
          </VCard>
        </VCol>
      </VRow>
      <VRow
        v-if="activeTab == 'theme-setting'"
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
                  mode="hexa"
                  class="ma-2"
                  width="800"
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
        v-if="activeTab == 'incentive-hub'"
        class="ms-1"
      >
        <!-- Bucks Info Section -->
        <VCol
          cols="12"
          md="12"
        >
          <VCard
            class="px-3 py-2 d-flex flex-column"
            style="height: 270px;"
          >
            <VCardTitle class="d-flex justify-space-between align-center">
              <h6 class="text-h6 mt-2">
                Incentive Hub Management
              </h6>
            </VCardTitle>

            <VCardText class="px-4 pb-3 mt-3">
              <div class="d-flex align-items-center mb-2">
                <VRadioGroup
                  v-model="isBucksSetting"
                  label="Do you want to enable reward allocation for project management?"
                  inline
                  @change="saveBucksDetails"
                >
                  <VRadio
                    label="Yes"
                    value="1"
                    class="me-2"
                    density="compact"
                  />
                  <VRadio
                    label="No"
                    value="0"
                    class="me-2"
                    density="compact"
                  />
                </VRadioGroup>
              </div>
              <AppTextField
                v-if="isBucksSetting === '1'"
                v-model="bucksLabel"
                label="Reward Label"
                class="cursor-not-allowed"
                placeholder="e.g., Darby Bucks"
                outlined
                dense
                :style="{ width: '300px' }"
              /> 
            </VCardText>
            <VCardActions class="px-5 py-3 d-flex justify-end">
              <VBtn
                color="primary"
                :loading="isLoading"
                size="small"
                variant="flat"
                @click="updateBucksInfo"
              >
                Save
              </VBtn>
            </VCardActions> 
          </VCard>
        </VCol>
      </VRow>
      <!-- Users & Roles Section -->
      <VRow
        v-if="(activeTab == 'users-and-roles') && authStore.isSuperAdmin"
        class="ms-1"
      >
        <VCol cols="12">
          <VCard class="px-3 py-2">
            <VCardTitle class="d-flex mb-2 justify-space-between align-center">
              <h5 class="text-h6">
                Manage Users
              </h5>
              <VBtn
                variant="elevated"
                color="primary"
                size="small"
                prepend-icon="tabler-plus"
                @click="isAddMemberDialogVisible = true"
              >
                New User
              </VBtn>
            </VCardTitle>
            <VCol
              v-if="getUsers.length === 0"
              cols="12"
              class="mt-7"
            >
              <VCard class="px-3 py-3 text-center">
                <span>No users found</span>
              </VCard>
            </VCol>
            <VRow v-else>
              <VCol
                v-for="user in getUsers"
                :key="user.id"
                cols="12"
                class="mt-1"
              >
                <VCard
                  class="d-flex align-center ps-4 py-1 list-side-border"
                  @click.stop="editMember(user)"
                >
                  <VCol cols="3">
                    <div class="d-flex align-center gap-x-3">
                      <VBadge
                        dot
                        location="top end"
                        offset-x="1"
                        offset-y="1"
                        :color="user.is_online ? 'success' : 'warning'"
                      >
                        <VAvatar
                          size="34"
                          :class="user.avatar ? '' : 'text-white bg-primary'"
                          :variant="!user.avatar ? 'tonal' : ''"
                        >
                          <span>{{ avatarText(user.name_first + ' ' + user.name_last) }}</span>
                        </VAvatar>
                      </VBadge>
                      <div>
                        <h6 class="text-h6 text-no-wrap">
                          <span class="d-block">{{ user.name_first }} {{ user.name_last }}</span>
                        </h6>
                        <small>{{ roleStore.capitalizeFirstLetter(user.role) }}</small>
                      </div>
                    </div>
                  </VCol>
                  <VCol cols="4">
                    <small class="text-xs">
                      <strong><VIcon
                        color="primary"
                        icon="tabler-mail"
                      /></strong> <span class="text-sm ms-1 text-high-emphasis">{{ user.email }}</span>
                    </small>
                  </VCol>
                  <VCol cols="2">
                    <small class="text-xs">
                      <strong><VIcon
                        color="primary"
                        icon="tabler-phone"
                      /></strong> <span class="text-sm ms-1 text-high-emphasis">{{ user?.info?.phone }}</span>
                    </small>
                  </VCol>
                  <VCol cols="2">
                    <div class="">
                      <VChip
                        :color="getStatusColor(user.state)"
                        variant="outlined"
                        size="small"
                        class="user-state-chip"
                      >
                        {{ roleStore.capitalizeFirstLetter(user.state) }}
                      </VChip>
                    </div>
                  </VCol>
                  <VCol cols="1">
                    <IconBtn @click.prevent>
                      <VIcon icon="tabler-dots" />
                      <VMenu activator="parent">
                        <VList>
                          <VList>
                            <VListItem
                              value="edit"
                              @click="editMember(user)"
                            >
                              Edit
                            </VListItem>
                            <VListItem
                              value="delete"
                              @click="deleteMember(user)"
                            >
                              Delete
                            </VListItem>
                          </VList>
                        </VList>
                      </VMenu>
                    </IconBtn>
                  </VCol>
                </VCard>
              </VCol>
            </VRow>
            <TablePagination
              v-if="!isLoading && getUsers.length > 0"
              v-model:page="options.page"
              :items-per-page="options.itemsPerPage"
              :total-items="totalUsers"
              class="custom-pagination"
              @update:page="handlePageChange"
            />
          </VCard>
        </VCol>
      </VRow>
    </vcol>
  </VRow>
  <VDialog
    v-model="isAddMemberDialogVisible"
    persistent
    class="v-dialog-sm"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="isAddMemberDialogVisible = !isAddMemberDialogVisible" />

    <!-- Dialog Content -->
    <VCard title="Add Member Details">
      <VForm
        ref="addMemberForm"
        @submit.prevent="submitAddMemberForm"
      >
        <VCardText>
          <VRow>
            <VCol cols="6">
              <AppTextField
                v-model="newMemberDetails.name_first"
                label="First Name *"
                :rules="[requiredValidator]"
                placeholder="First Name"
                :error-messages="addingErrors.name_first"
              />
            </VCol>

            <!-- Last Name -->
            <VCol cols="6">
              <AppTextField
                v-model="newMemberDetails.name_last"
                label="Last Name *"
                :rules="[requiredValidator]"
                placeholder="Last Name"
                :error-messages="addingErrors.name_last"
              />
            </VCol>

            <!-- Email -->
            <VCol cols="12">
              <AppTextField
                v-model="newMemberDetails.email"
                label="Email *"
                :rules="[requiredValidator, emailValidator]"
                placeholder="Email"
                :error-messages="addingErrors.email"
              />
            </VCol>

            <!-- Role -->
            <VCol cols="6">
              <AppSelect
                v-model="newMemberDetails.role"
                label="Select Role *"
                placeholder="Select Role"
                :items="getRoles"
                item-title="name"
                item-value="name"
                :rules="[requiredValidator]"
                :error-messages="addingErrors.role"
              />
            </VCol>

            <!-- Phone Mask Field -->
            <VCol cols="6">
              <AppTextField
                v-model="newMemberDetails.phone"
                v-mask="'(###) ###-####'"
                label="Phone *"
                :rules="[requiredValidator]"
                placeholder="(123) 456-7890"
                :error-messages="addingErrors.phone"
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
                placeholder="Password"
                autocomplete="new-password"
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
                placeholder="Confirm Password"
                :error-messages="addingErrors.confirmPassword"
                @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
              />
            </VCol>
          </VRow>
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            @click="isAddMemberDialogVisible = false"
          >
            Cancel
          </VBtn>
          <VBtn
            type="submit"
            :disabled="getLoadStatus === 1"
            @click="addMemberForm?.validate()"
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
        </VCardText>
      </VForm>
    </VCard>
  </VDialog>
  <VDialog
    v-model="isEditMemberDialogVisible"
    persistent
    class="v-dialog-sm"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="isEditMemberDialogVisible = !isEditMemberDialogVisible" />

    <!-- Dialog Content -->
    <VCard title="Edit Member Details">
      <VForm
        ref="editMemberForm"
        @submit.prevent="submitEditMemberForm"
      >
        <VCardText>
          <VRow>
            <VCol cols="6">
              <AppTextField
                v-model="editMemberDetails.name_first"
                label="First Name *"
                :rules="[requiredValidator]"
                placeholder="First Name"
              />
            </VCol>

            <!-- Last Name -->
            <VCol cols="6">
              <AppTextField
                v-model="editMemberDetails.name_last"
                label="Last Name *"
                :rules="[requiredValidator]"
                placeholder="Last Name"
              />
            </VCol>

            <!-- Email -->
            <VCol cols="12">
              <AppTextField
                v-model="editMemberDetails.email"
                label="Email *"
                :rules="[requiredValidator, emailValidator]"
                placeholder="Email"
              />
            </VCol>

            <!-- Role -->
            <VCol cols="6">
              <AppSelect
                v-model="editMemberDetails.role"
                label="Select Role *"
                placeholder="Select Role"
                :items="getRoles"
                item-title="name"
                item-value="name"
                :rules="[requiredValidator]"
              />
            </VCol>

            <!-- Phone Mask Field -->
            <VCol cols="6">
              <AppTextField
                v-model="editMemberDetails.phone"
                v-mask="'(###) ###-####'"
                label="Phone *"
                :rules="[requiredValidator]"
                placeholder="(123) 456-7890"
              />
            </VCol>

            <!-- Password -->
            <VCol cols="6">
              <AppTextField
                v-model="editMemberDetails.password"
                label="Password"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                placeholder="Password"
                autocomplete="new-password"
                @click:append-inner="isPasswordVisible = !isPasswordVisible"
              />
            </VCol>

            <!-- Confirm Password -->
            <VCol cols="6">
              <AppTextField
                v-model="editMemberDetails.confirmPassword"
                :type="isConfirmPasswordVisible ? 'text' : 'password'"
                label="Confirm Password"
                :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                :rules="[confirmedValidator(editMemberDetails.confirmPassword, editMemberDetails.password)]"
                placeholder="Confirm Password"
                @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
              />
            </VCol>
          </VRow>
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            @click="isEditMemberDialogVisible = false"
          >
            Cancel
          </VBtn>
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
        </VCardText>
      </VForm>
    </VCard>
  </VDialog>
</template>
  
<script setup>
import { ref, computed } from 'vue'
import { layoutConfig } from '@layouts'
import Swal from 'sweetalert2'
import { useToast } from "vue-toastification"
import { useCompanyStore } from "@/store/companies"
import { useRoleStore } from "@/store/roles"
import { useAuthStore } from "@/store/auth"
import { useRoute, useRouter } from 'vue-router'
import { useHead } from '@unhead/vue'
import { useTheme } from 'vuetify'
import { useStorage } from '@vueuse/core'
import { cookieRef, namespaceConfig } from '@layouts/stores/config'
import { updateSystemLogo, updateSystemFavicon } from '@themeConfig'

const toast = useToast()
const $route = useRoute()
const router = useRouter()
const vuetifyTheme = useTheme()
const companyStore = useCompanyStore()
const roleStore = useRoleStore()
const authStore = useAuthStore()
  
const isLoading = ref(false)

const companyUuid = $route.params.id
  
const companyDetails = ref({
  name: '',
})
  
const logo = ref(null)
const favicon = ref(null)
const logoInputRef = ref(null)
const faviconInputRef = ref(null)
const inputLogoKey = ref(0)
const inputFaviconKey = ref(0)
const isActive = ref(1)
const isCopied = ref(false)
const activeTab = ref('basic-setting')
const isLogoCardHovered = ref(false)
const isFaviconCardHovered = ref(false)
const primaryColor = ref("#a12592")
const isEditMemberDialogVisible = ref(false)
const editMemberForm = ref('')
const isAddMemberDialogVisible = ref(false)
const addMemberForm = ref('')
const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const editMemberDetails = ref({})
const selectedRole = ref(null)
const searchName = ref('')
const searchEmail = ref('')
const bucksLabel = ref('')
const isBucksSetting = ref('')
const options = ref({ page: 1, itemsPerPage: 10, orderBy: '', order: '' })

const tabs = ref([
  { title: 'Basic Setting', tab: 'basic-setting' },
  { title: 'Theme Setting', tab: 'theme-setting' },
  { title: `Incentive Hub`, tab: 'incentive-hub' },
  ...(authStore.isSuperAdmin ? [{ title: 'Users & Roles', tab: 'users-and-roles' }] : []),
])

const allowedLogoTypes = ['image/svg+xml', 'image/png', 'image/jpeg', 'image/jpg', 'image/avif', 'image/webp']
const allowedFaviconType = ['image/svg+xml', 'image/png', 'image/jpeg', 'image/jpg', 'image/webp', 'image/x-icon']

const newMemberDetails = ref({
  name_first: '',
  name_last: '',
  role: null,
  email: '',
  phone: '',
  password: '',
  confirmPassword: '',
})

const addingErrors = ref({
  name_first: '',
  name_last: '',
  email: '',
  role: '',
  phone: '',
  password: '',
  confirmPassword: '',
})

onBeforeMount(async () => {
  await fetchCompany()

  const searchParams = new URLSearchParams(window.location.search)
  const requestedTab = searchParams.get('tab')
  
  if (requestedTab && tabs.value.some(tab => tab.tab === requestedTab)) {
    selectTab(requestedTab)
  }

  await fetchUsers()
  await fetchRoles()
})

const fetchCompany = async () => {
  try {
    await companyStore.show(companyUuid)
    companyDetails.value.name = company?.value?.name ?? ''
    primaryColor.value = company?.value?.general_setting?.primary_color ?? '#a12592'
    bucksLabel.value = company?.value?.general_setting?.bucks_label ?? 'Darby Bucks'
    isBucksSetting.value = company?.value?.general_setting?.is_bucks_setting ?? '0'
    isActive.value = company?.value?.is_active
  } catch (error) {
    toast.error('Error fetching company:', error)
  }
}

const fetchUsers = async () => {
  try {
    await companyStore.getAllUsers(companyUuid, options.value.page, options.value.itemsPerPage, searchName.value, searchEmail.value, selectedRole.value)
  } catch (error) {
    toast.error('Error fetching users:', error)
  }
}

const fetchRoles = async () => {
  try {
    isLoading.value = true

    await roleStore.getAll()
  } catch (error) {
    toast.error('Failed to get roles:', error.message || error)
  }
  finally {
    isLoading.value = false
  }
}

async function submitAddMemberForm() {
  addMemberForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        resetErrors()
        await companyStore.createUser(companyUuid, newMemberDetails.value)
        if(getErrors.value)
        {
          showError()
        }
        else{
          isLoading.value = true
          toast.success('Member added successfully', { timeout: 1000 })
          isAddMemberDialogVisible.value = false
          await fetchUsers()
          addMemberForm.value?.reset()
          isLoading.value = false
        }
      } catch (error) {
        toast.error('Failed to add member:', error)
      }
    }
  })
}

async function submitEditMemberForm() {
  editMemberForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        await companyStore.updateUser(companyUuid, editMemberDetails.value.uuid, editMemberDetails.value)
        if(getErrors.value)
        {
          showError()
        }
        else{
          isLoading.value = true
          isEditMemberDialogVisible.value = false
          toast.success('Member updated successfully', { timeout: 1000 })
          await fetchUsers()
          isLoading.value = false
        }
      } catch (error) {
        toast.error('Failed to update member:', error.message || error)
      }
    }
  })
}

const showError = () => {
  if (getStatusCode.value === 500) {
    toast.error('Something went wrong. Please try again later.')
  } else {
    addingErrors.value = getErrors.value
  }
}

const resetErrors = () => {
  addingErrors.value = Object.fromEntries(Object.keys(addingErrors.value).map(key => [key, '']))
}

const applyFilters = async (name = '', email = null, roleId = null) => {
  searchName.value = name
  searchEmail.value = email
  selectedRole.value = roleId
  options.value.page = 1
  await fetchUsers()
}

const selectTab = tab => {
  activeTab.value = tab
}

const editMember = member => {
  editMemberDetails.value = { ...member }
  isEditMemberDialogVisible.value = true
}

const onLogoHover = state => {
  isLogoCardHovered.value = state
}

const onFaviconHover = state => {
  isFaviconCardHovered.value = state
}
  
const updateCompanyInfo = async() => {
  isLoading.value = true
  if (companyDetails.value.name.trim() === '') {
    toast.error('Company name cannot be empty')
    isLoading.value = false

    return
  }
  try {

    const response = await companyStore.saveCompanyDetails(companyDetails.value, companyUuid)
    
    toast.success('Details saved successfully.')
    isLoading.value = false
  } catch (error) {
    console.error('company details failed:', error)
    isLoading.value = false
  }
  isLoading.value = false
}

const updateBucksInfo = async() => {
  isLoading.value = true
  if (bucksLabel.value.trim() === '') {
    toast.error('Label cannot be empty')
    isLoading.value = false

    return
  }
  try {
    const payload = {
      'label': bucksLabel.value,
      'isBucksSetting': isBucksSetting.value,
    }

    const response = await companyStore.saveBucksDetails(payload, companyUuid)

    if(authStore.isTenant){
      await authStore.tenantInfo()
    }

    toast.success('Details saved successfully.')
    isLoading.value = false
  } catch (error) {
    console.error('bucks details failed:', error)
    isLoading.value = false
  }
  isLoading.value = false
}
  
const saveColor = async () => {
  isLoading.value = true
  if (primaryColor.value && !isValidHex.value) {
    toast.error('Please enter a valid hex color code (e.g., #FFF or #FFFFFF).')
    isLoading.value = false

    return
  }
  try {
    const payload = {
      'primary_color': primaryColor.value,
    }

    const response = await companyStore.saveColors(payload, companyUuid)
    
    if(authStore.isTenant){
      await authStore.tenantInfo()
      setPrimaryColor(authStore.generalSetting?.primary_color)
    }

    toast.success('Colors saved successfully.')
    isLoading.value = false
  } catch (error) {
    console.error('color update failed:', error)
    isLoading.value = false
  }
}

const setPrimaryColor = useDebounceFn(color => {
  vuetifyTheme.themes.value[vuetifyTheme.name.value].colors.primary = color
  vuetifyTheme.themes.value[vuetifyTheme.name.value].colors['primary-darken-1'] = color
  cookieRef(`${ vuetifyTheme.name.value }ThemePrimaryColor`, null).value = color
  cookieRef(`${ vuetifyTheme.name.value }ThemePrimaryDarkenColor`, null).value = color
  useStorage(namespaceConfig('initial-loader-color'), null).value = color
}, 100)

const handleActiveChange = async () => {

  const payload = {
    isActive: isActive.value,
  }

  await companyStore.updateActiveState(companyUuid, payload)
  toast.success(isActive.value ? 'Company active successfully.' : 'Company inactive successfully.')
}

const handlePageChange = async page => {
  options.value.page = page
  await fetchUsers()
}

const downloadAsset = file => {
  const link = document.createElement('a')

  link.href = getImageUrl(file.path)
  link.download = file.name
  link.click()
}

const uploadLogo = () => {
  inputLogoKey.value++
  logoInputRef.value.click()
}

const uploadFavicon = () => {
  inputFaviconKey.value++
  faviconInputRef.value.click()
}

const deleteFavicon = async fileId => {
  try {
    const response = await companyStore.deleteAsset(fileId, companyUuid)
    
    toast.success('Favicon deleted successfully.')
    favicon.value = null
    company.value.favicon = null
    faviconInputRef.value = null
  } catch (error) {
    console.error('favicon delete failed:', error)
  }
}

const deleteLogo = async fileId => {
  try {
    const response = await companyStore.deleteAsset(fileId, companyUuid)
    
    toast.success('Logo deleted successfully.')
    logo.value = null
    company.value.logo = null
    logoInputRef.value = null
  } catch (error) {
    console.error('Logo delete failed:', error)
  }
}

const handleLogoChange = async event => {
  const file = event.target.files[0]
  if (file) {
    if (!allowedLogoTypes.includes(file.type)) {
      toast.error('Please upload an SVG, PNG, JPG, AVIF or WEBP file for the logo.')
      
      return
    }
    const reader = new FileReader()

    reader.onload = () => {
      logo.value = reader.result
    }
    reader.readAsDataURL(file)

    const formData = new FormData()

    formData.append('file', file)

    try {
      const response = await companyStore.uploadLogo(formData, companyUuid)

      if(authStore.isTenant){
        await authStore.tenantInfo()
        updateSystemLogo(authStore.getLogo)
        console.log('At here')
      }

      toast.success('Logo uploaded successfully.')
      fetchCompany()
      logo.value = getImageUrl(response.data.url)
      logoInputRef.value = null
    } catch (error) {
      console.error('Logo upload failed:', error)
    }
  }
}

const handleFaviconChange = async event => {
  const file = event.target.files[0]
  if (file) {
    if (!allowedFaviconType.includes(file.type)) {
      toast.error('Please upload an ICO, SVG, PNG, JPG, or WEBP file for the favicon.')

      return
    }
    const reader = new FileReader()

    reader.onload = () => {
      favicon.value = reader.result
    }
    reader.readAsDataURL(file)

    const formData = new FormData()

    formData.append('file', file)

    try {
      const response = await companyStore.uploadFavicon(formData, companyUuid)

      if(authStore.isTenant){
        await authStore.tenantInfo()
        updateSystemFavicon(authStore.getFavicon)
      }

      toast.success('Favicon uploaded successfully.')
      favicon.value = getImageUrl(response.data.url)
      faviconInputRef.value = null
      fetchCompany()
    } catch (error) {
      console.error('Favicon upload failed:', error)
    }
  }
}

const deleteMember = async member => {
  try {

    const confirmDelete = await Swal.fire({
      title: "Are you sure?",
      html: `
        <div>
        <p>
            Do you want to delete <strong>${member.name_first} ${member.name_last}</strong>?
            <br>
            <small>This action will also delete all associated tasks.</small>
        </p>
        </div>
      `,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "rgba(var(--v-theme-primary))",
      cancelButtonColor: "#808390",
      confirmButtonText: "Yes, delete it!",
      didOpen: () => {
        document.querySelector('.swal2-confirm').blur()
      },
    })

    if (confirmDelete.isConfirmed) {

      await companyStore.deleteUser(companyUuid, member.uuid)

      isLoading.value = true
      toast.success('Member deleted successfully', { timeout: 1000 })
      await fetchUsers()
      isLoading.value = false
    }
  } catch (error) {
    console.log('Failed to delete user:', error)
  }
}

const getStatusColor = role => {
  const colors = {
    'active': 'success',
    'inactive': 'error',
  }

  return colors[role] ?? 'warning'
}

const formattedUrl = url => {
  if (!/^https?:\/\//i.test(url)) {
    return 'http://' + url
  }
  
  return url
}

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

const copyToClipboard = () => {
  const domain = formattedUrl(company?.value?.url) || ''

  navigator.clipboard.writeText(domain).then(() => {
    isCopied.value = true
    setTimeout(() => {
      isCopied.value = false 
    }, 1500)
  })
}

const company = computed(() =>{
  return companyStore.getCompany
})

const getRoles = computed(() => {
  return roleStore.getRoles
})

const getUsers = computed(() => {
  return companyStore.getUsers
})

const totalUsers = computed(() => {
  return companyStore.usersCount
})

const isValidHex = computed(() => {
  const hexRegex = /^#([A-F\d]{3}){1,2}$/i
  
  return hexRegex.test(primaryColor.value)
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

watch(activeTab, newActiveTab => {
  router.push({ query: { tab: newActiveTab } })
  useHead({ title: `${layoutConfig.app.title} | ${tabs?.value?.find(tab => tab.tab === newActiveTab).title}` })
})
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
  background-color: rgb(var(--v-theme-surface)); 
  margin-top: 12px;
  border-radius: 6px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  height: 500px;
}
.custom-tabs ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
}
/* cutome tabs */
.custom-tabs li {
  padding: 10px 15px;
  cursor: pointer;
  margin-bottom: 5px;
  color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
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
  background-color: rgba(var(--v-theme-primary));
  color: white !important;
  border-radius: 3px;
}
</style>
  
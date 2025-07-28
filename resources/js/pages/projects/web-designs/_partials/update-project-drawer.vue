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
      title="Edit Project Details"
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
            ref="editProjectForm"
            @submit.prevent="submitEditProjectForm"
          >
            <VRow>
              <VCol
                md="6"
                cols="12"
              >
                <AppAutocomplete
                  ref="focusField"
                  v-model="projectDetails.client_id"
                  label="Business Name*"
                  placeholder="Select Business Name"
                  :rules="[requiredValidator]"
                  :items="props.getClients"
                  item-title="name"
                  item-value="id"
                  chips
                >
                  <template #chip="{ props, item }">
                    <VListItem
                      class="px-0"
                      v-bind="props"
                    >
                      <VAvatar
                        color="primary"
                        :image="item?.raw?.avatar ? getImageUrl(item?.raw?.avatar?.path) : undefined"
                        :variant="item?.raw?.avatar ? undefined : 'tonal'"
                        size="28"
                      >
                        <span v-if="!item?.raw?.avatar">{{ avatarText(item?.raw?.name) }}</span>
                      </VAvatar>
                      <span class="ms-2">{{ item.raw.name }}</span>
                    </VListItem>
                  </template>

                  <template #item="{ props, item }">
                    <VListItem v-bind="{ ...props, title: '' }">
                      <VAvatar
                        color="primary"
                        :image="item?.raw?.avatar ? getImageUrl(item?.raw?.avatar?.path) : undefined"
                        :variant="item?.raw?.avatar ? undefined : 'tonal'"
                        size="38"
                      >
                        <span v-if="!item?.raw?.avatar">{{ avatarText(item?.raw?.name) }}</span>
                      </VAvatar>
                      <span class="ms-2">{{ item.raw.name }}</span>
                    </VListItem>
                  </template>
                </AppAutocomplete>
              </VCol>
              <VCol
                md="6"
                cols="12"
              >
                <AppAutocomplete
                  v-model="projectDetails.project_type_id"
                  label="Project Type*"
                  placeholder="Select Project Type"
                  :rules="[requiredValidator]"
                  :items="props.getProjectTypes"
                  item-title="name"
                  item-value="id"
                />
              </VCol>
              <VCol
                md="6"
                cols="12"
              >
                <AppTextField
                  v-model="projectDetails.title"
                  label="Project Title*"
                  :rules="[requiredValidator]"
                  placeholder="Project Title"
                />
              </VCol>
              <VCol
                md="6"
                cols="12"
              >
                <AppAutocomplete
                  v-model="projectDetails.project_manager_id"
                  label="Project Manager*"
                  placeholder="Select Project Manager"
                  :rules="[requiredValidator]"
                  :items="props.getProjectManagersList"
                  item-title="name"
                  chips
                  item-value="id"
                >
                  <template #chip="{ props, item }">
                    <VListItem
                      class="px-0"
                      v-bind="props"
                    >
                      <VAvatar
                        color="primary"
                        :image="item?.raw?.avatar ? getImageUrl(item?.raw?.avatar?.path) : undefined"
                        :variant="item?.raw?.avatar ? undefined : 'tonal'"
                        size="28"
                      >
                        <span v-if="!item?.raw?.avatar">{{ avatarText(item?.raw?.name) }}</span>
                      </VAvatar>
                      <span class="ms-2">{{ item.raw.name }}</span>
                    </VListItem>
                  </template>

                  <template #item="{ props, item }">
                    <VListItem v-bind="{ ...props, title: '' }">
                      <VAvatar
                        color="primary"
                        :image="item?.raw?.avatar ? getImageUrl(item?.raw?.avatar?.path) : undefined"
                        :variant="item?.raw?.avatar ? undefined : 'tonal'"
                        size="38"
                      >
                        <span v-if="!item?.raw?.avatar">{{ avatarText(item?.raw?.name) }}</span>
                      </VAvatar>
                      <span class="ms-2">{{ item.raw.name }}</span>
                    </VListItem>
                  </template>
                </AppAutocomplete>
              </VCol>
              <VCol cols="12">
                <!--
                  <label>Select Staff Members</label>
                  <Multiselect
                  v-model="projectDetails.staff_ids"
                  mode="tags"
                  placeholder="Select Staff Members"
                  close-on-select
                  searchabler
                  :options="props.getStaffList"
                  class="bg-background multiselect-purple"
                  style="color: #000 !important;"
                  /> 
                -->
                <AppAutocomplete
                  v-model="projectDetails.staff_ids"
                  label="Select Staff Members"
                  placeholder="Select Staff Members"
                  :items="props.getStaffList"
                  :item-title="item => item.label"
                  :item-value="item => item.value"
                  chips
                  multiple
                  closable-chips
                  eager
                  clearable
                  clear-icon="tabler-x"
                >
                  <template #chip="{ props, item }">
                    <VChip
                      v-bind="props"
                      variant="elevated"
                      color="primary"
                    >
                      <VAvatar
                        color="white"
                        :image="item?.raw?.avatar ? getImageUrl(item?.raw?.avatar?.path) : undefined"
                        :variant="item?.raw?.avatar ? undefined : 'tonal'"
                        size="18"
                      >
                        <small
                          v-if="!item?.raw?.avatar"
                          class=""
                        >{{ avatarText(item?.raw?.label) }}</small>
                      </VAvatar>
                      <span class="ms-2">{{ item.raw.label }}</span>
                    </VChip>
                  </template>

                  <template #item="{ props, item }">
                    <VListItem v-bind="{ ...props, title: '' }">
                      <VAvatar
                        color="primary"
                        :image="item?.raw?.avatar ? getImageUrl(item?.raw?.avatar?.path) : undefined"
                        :variant="item?.raw?.avatar ? undefined : 'tonal'"
                        size="38"
                      >
                        <span v-if="!item?.raw?.avatar">{{ avatarText(item?.raw?.label) }}</span>
                      </VAvatar>
                      <span class="ms-2">{{ item.raw.label }}</span>
                    </VListItem>
                  </template>
                </AppAutocomplete>
                <div class="mt-2 d-flex justify-end">
                  <VBtn
                    variant="text"
                    class="text-primary text-sm custom-btn-style"
                    size="x-small"
                    @click="props.openAddNewMemberModal()"
                  >
                    + Add New Member
                  </VBtn>
                </div>
              </VCol>
              <VCol
                md="6"
                cols="12"
              >
                <AppTextField
                  v-model="projectDetails.budget_amount"
                  label="Project Budget*"
                  :rules="[requiredValidator]"
                  placeholder="0.00"
                  type="number"
                  class="no-arrows"
                  prepend-inner-icon="tabler-currency-dollar"
                />
              </VCol>
              <VCol
                v-if="showBucksShare"
                md="6"
                cols="12"
              >
                <!-- Input Field -->
                <AppTextField
                  v-model="projectDetails.bucks_share"
                  :label="`${generalSetting?.bucks_label || 'Darby Bucks'} Share*`"
                  :rules="[requiredValidator]"
                  placeholder="0.00"
                  type="number"
                  class="no-arrows me-1"
                  prepend-inner-icon="tabler-percentage"
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol
                v-if="generalSetting?.is_bucks_setting == 1"
                md="6"
                cols="12"
              >
                <VSwitch
                  v-model="showBucksShare"
                  :label="`Enable ${generalSetting?.bucks_label || 'Darby Bucks'} Share`"
                  class="mb-3 mt-5"
                />
              </VCol>
              <VCol
                md="6"
                cols="12"
              >
                <label class="text-sm font-medium mb-1 d-block">Project Logo</label>
                <VFileInput
                  v-model="projectDetails.project_logo"
                  accept="image/*"
                  variant="filled"
                  label="Project Logo"
                >
                  <template #selection="{ fileNames }">
                    <div v-if="fileNames?.length">
                      <span 
                        v-for="(file, index) in fileNames" 
                        :key="index"
                      >
                        {{ truncateFileName(file) }}
                      </span>
                    </div>
                  </template>
                </VFileInput>

                <VImg
                  v-if="projectLogo"
                  :src="projectLogo"
                  class="rounded-lg mt-1"
                  width="60"
                  height="60"
                />
              </VCol>
              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2 custom-btn-style"
                    :disabled="getLoadStatus === 1"
                    @click="editProjectForm?.validate()"
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
                    class="error-btn-customer-style"
                    @click="resetForm"
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
import { layoutConfig } from '@layouts'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'
import { ref, nextTick } from 'vue'
import Multiselect from '@vueform/multiselect'
import { useToast } from "vue-toastification"
import { useProjectStore } from "@/store/projects"
import { useAuthStore } from "@/store/auth"
import { useRouter } from 'vue-router'

const props = defineProps({
  isEditDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchProjects: Function,
  getProjectTypes: Object,
  getClients: Object,
  getStaffList: Object,
  getProjectManagers: Object,
  editProjectDetails: Object,
  getProjectManagersList: Object,
  getLoadStatus: Number,
  openAddNewMemberModal: Function,
})

const emit = defineEmits(['update:isEditDrawerOpen'])
const toast = useToast()
const projectStore = useProjectStore()
const authStore = useAuthStore()
const router = useRouter()

const focusField = ref(null)
const editProjectForm = ref()
const isLoading= ref(false)
const showBucksShare = ref(false)

const projectDetails = ref({
  client_id: null,
  project_manager_id: null,
  title: '',
  project_type_id: null,
  staff_ids: [],
  budget_amount: '',
  bucks_share: null,
  project_logo: null,
})

const projectLogo = ref(null)

const handleDrawerModelValueUpdate = val => {
  emit('update:isEditDrawerOpen', val)
}

const resetForm = () => {
  editProjectForm.value?.reset()
  emit('update:isEditDrawerOpen', false)
}

// const budgetTypes = [
//   { title: 'Fixed', value: 'fixed' },
//   { title: 'Percentage', value: 'percentage' },
// ]

async function submitEditProjectForm() {
  editProjectForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        const payload = projectDetails.value
        if (typeof payload.client_id === 'object') {
          payload.client_id = payload.client_id.id
        }
        if (typeof payload.project_manager_id === 'object') {
          payload.project_manager_id = payload.project_manager_id.id
        }
        if (typeof payload.project_type_id === 'object') {
          payload.project_type_id = payload.project_type_id.id
        }
        payload.id = props.editProjectDetails.id
        payload.uuid = props.editProjectDetails.uuid

        if (parseFloat(payload.bucks_share) > 100) {
          toast.error(`${generalSetting?.bucks_label} Share cannot be greater than 100%`)

          return
        }

        isLoading.value = true
        await projectStore.update(payload)

        if(projectStore.getErrors) {
          toast.error('Failed to add project')
          isLoading.value = false

          return
        } else {
          emit('update:isEditDrawerOpen', false)
          toast.success('Project updated successfully', { timeout: 1000 })
          await props.fetchProjects()
          isLoading.value = false
        }
      } catch (error) {
        toast.error('Failed to update project:', error.message || error)
      }
    }
  })
}

const generalSetting = computed(() => {
  return authStore.getGeneralSetting
})

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

const truncateFileName = name => {
  const maxLength = 20
  if (name.length <= maxLength) return name

  const ext = name.substring(name.lastIndexOf('.'))
  const base = name.substring(0, maxLength - ext.length - 3)

  return `${base}...${ext}`
}

watch(
  () => [props.editProjectDetails, props.isEditDrawerOpen],
  ([editProjectDetails, isEditDrawerOpen]) => {
    if (editProjectDetails && isEditDrawerOpen) {
      let members = editProjectDetails.project_members
      let client = members.find(member => member.role == USER_ROLES.CLIENT)
      let clientId = client?.id
      projectDetails.value.client_id = props.getClients.find(client => client.id == clientId)

      let projectManager = members.find(member => member.role == USER_ROLES.PROJECT_MANAGER)
      let projectManagerId = projectManager?.id
      projectDetails.value.project_manager_id = props.getProjectManagersList.find(manager => manager.id == projectManagerId)

      let staffs = members.filter(member => member.role == USER_ROLES.STAFF)
      projectDetails.value.staff_ids = staffs
        .map(staff => props.getStaffList.find(staffMember => staffMember.value === staff.id)?.value)
        .filter(id => id !== undefined)

      let projectTypeId = editProjectDetails?.project_type_id
      projectDetails.value.project_type_id = props.getProjectTypes.find(type => type.id == projectTypeId)

      projectDetails.value.title = editProjectDetails?.title
      projectDetails.value.budget_amount = editProjectDetails?.budget_amount
      projectDetails.value.bucks_share = editProjectDetails?.bucks_share
      projectLogo.value = editProjectDetails?.project_logo
    }

    if (isEditDrawerOpen) {
      nextTick(() => {
        const inputEl = focusField.value.$el.querySelector('input')
        if (inputEl) {
          inputEl.focus()
        }
      })
    }
  },
)

watch(
  () => projectDetails.value.bucks_share,
  newValue => {
    showBucksShare.value = newValue && newValue > 0
  },
)
</script>

    <style lang="scss">
    .v-navigation-drawer__content {
      overflow-y: hidden !important;
    }
    </style>

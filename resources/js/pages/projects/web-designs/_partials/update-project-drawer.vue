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
                  label="Client*"
                  placeholder="Select Client"
                  :rules="[requiredValidator]"
                  :items="props.getClients"
                  item-title="name"
                  item-value="id"
                />
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
                  label="Title*"
                  :rules="[requiredValidator]"
                  placeholder="Title"
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
                  item-value="id"
                />
              </VCol>
              <VCol cols="12">
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
                  label="Darby Bucks Share*"
                  :rules="[requiredValidator]"
                  placeholder="0.00"
                  type="number"
                  class="no-arrows me-1"
                  prepend-inner-icon="tabler-percentage"
                />
              </VCol>
              <VCol cols="12">
                <VSwitch
                  v-model="showBucksShare"
                  label="Enable Darby Bucks Share"
                  class="mb-3"
                />
              </VCol>
              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2"
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
      </PerfectScrollbar>
    </VCard>
  </VNavigationDrawer>
</template>

<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'
import { ref, nextTick } from 'vue'
import Multiselect from '@vueform/multiselect'
import { useToast } from "vue-toastification"
import { useProjectStore } from "../../../../store/projects"

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
})

const emit = defineEmits(['update:isEditDrawerOpen'])
const toast = useToast()
const projectStore = useProjectStore()

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
})

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
          toast.error('Darby Bucks Share cannot be greater than 100%')

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

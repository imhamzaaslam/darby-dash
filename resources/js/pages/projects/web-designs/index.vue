<template>
  <VRow>
    <VCol
      cols="12"
      md="6"
    />
    <VCol
      cols="12"
      md="6"
      class="mb-3"
    >
      <VDialog
        v-model="isDialogVisible"
        persistent
        class="v-dialog-sm"
      >
        <!-- Dialog Activator -->
        <template #activator="{ props }">
          <VBtn
            color="primary"
            size="small"
            rounded="pill"
            class="float-right"
            v-bind="props"
          >
            <VIcon
              start
              icon="tabler-plus"
            />
            New Project
          </VBtn>
        </template>

        <!-- Dialog close btn -->
        <DialogCloseBtn @click="isDialogVisible = !isDialogVisible" />

        <!-- Dialog Content -->
        <VCard title="Add Project Details">
          <VForm
            ref="addProjectForm"
            @submit.prevent="submitAddProjectForm"
          >
            <VCardText>
              <VRow>
                <VCol cols="12">
                  <AppTextField
                    v-model="newProjectDetails.title"
                    label="Title*"
                    :rules="[requiredValidator]"
                    placeholder="Title"
                  />
                </VCol>

                <VCol cols="6">
                  <AppSelect
                    v-model="newProjectDetails.project_type_id"
                    label="Project Type*"
                    placeholder="Select Project Type"
                    :rules="[requiredValidator]"
                    :items="getProjectTypes"
                    item-title="name"
                    item-value="id"
                  />
                </VCol>

                <VCol cols="6">
                  <AppSelect
                    v-model="newProjectDetails.project_manager_id"
                    label="Project Manager*"
                    placeholder="Select Project Manager"
                    :rules="[requiredValidator]"
                    :items="getProjectManagers"
                    item-title="name"
                    item-value="id"
                  />
                </VCol>
                <VCol cols="12">
                  <AppSelect
                    v-model="newProjectDetails.member_ids"
                    :items="getMembers"
                    item-title="name"
                    item-value="id"
                    label="Members*"
                    placeholder="Select Members"
                    multiple
                    clearable
                    clear-icon="tabler-x"
                  />
                </VCol>
                <VCol cols="6">
                  <AppTextField
                    v-model="newProjectDetails.est_hours"
                    label="Estimated Hours*"
                    :rules="[requiredValidator]"
                    placeholder="Estimated Hours"
                  />
                </VCol>

                <VCol cols="6">
                  <AppTextField
                    v-model="newProjectDetails.est_budget"
                    label="Estimated Budget*"
                    :rules="[requiredValidator]"
                    placeholder="Estimated Budget"
                  />
                </VCol>
              </VRow>
            </VCardText>

            <VCardText class="d-flex justify-end gap-3 flex-wrap">
              <!-- <VBtn
                color="secondary"
                @click="isDialogVisible = false"
              >
                Cancel
              </VBtn> -->
              <VBtn
                type="submit"
                @click="addProjectForm?.validate()"
              >
                Save & Add Task
              </VBtn>
            </VCardText>
          </VForm>
        </VCard>
      </VDialog>
    </VCol>
    <VDialog
      v-model="isEditDialogVisible"
      persistent
      class="v-dialog-sm"
    >
      <!-- Dialog close btn -->
      <DialogCloseBtn @click="isEditDialogVisible = !isEditDialogVisible" />

      <!-- Dialog Content -->
      <VCard title="Edit Project Details">
        <VForm
          ref="editProjectForm"
          @submit.prevent="submitEditProjectForm"
        >
          <VCardText>
            <VRow>
              <VCol cols="12">
                <AppTextField
                  v-model="editProjectDetails.title"
                  label="Title*"
                  :rules="[requiredValidator]"
                  placeholder="Title"
                />
              </VCol>

              <VCol cols="6">
                <AppSelect
                  v-model="editProjectDetails.project_type"
                  label="Project Type*"
                  placeholder="Select Project Type"
                  :rules="[requiredValidator]"
                  :items="getProjectTypes"
                  item-title="name"
                  item-value="id"
                />
              </VCol>

              <VCol cols="6">
                <AppSelect
                  v-model="editProjectDetails.project_manager"
                  label="Project Manager*"
                  placeholder="Select Project Manager"
                  :rules="[requiredValidator]"
                  :items="getProjectManagers"
                  item-title="name"
                  item-value="id"
                />
              </VCol>
              <VCol cols="12">
                <AppSelect
                  v-model="editProjectDetails.member_ids"
                  :items="getMembers"
                  item-title="name"
                  item-value="id"
                  label="Members*"
                  placeholder="Select Member"
                  multiple
                  clearable
                  clear-icon="tabler-x"
                />
              </VCol>
              <VCol cols="6">
                <AppTextField
                  v-model="editProjectDetails.est_hours"
                  label="Estimated Hours*"
                  :rules="[requiredValidator]"
                  placeholder="Estimated Hours"
                />
              </VCol>

              <VCol cols="6">
                <AppTextField
                  v-model="editProjectDetails.est_budget"
                  label="Estimated Budget*"
                  :rules="[requiredValidator]"
                  placeholder="Estimated Budget"
                />
              </VCol>
            </VRow>
          </VCardText>

          <VCardText class="d-flex justify-end gap-3 flex-wrap">
            <VBtn
              color="secondary"
              @click="isEditDialogVisible = false"
            >
              Cancel
            </VBtn>
            <VBtn
              type="submit"
              @click="editProjectForm?.validate()"
            >
              Save
            </VBtn>
          </VCardText>
        </VForm>
      </VCard>
    </VDialog>
  </VRow>
  <VCard>
    <VCardText class="d-flex justify-space-between align-center flex-wrap gap-4">
      <h5 class="text-h5">
        Web Design Projects
      </h5>
      <div style="inline-size: 272px;">
        <!--
          <AppTextField
          v-model="search"
          placeholder="Search Member"
          />
        -->
      </div>
    </VCardText>

    <VDivider />
    <VDataTable
      :headers="headers"
      :items-per-page="options.itemsPerPage"
      :items="getProjects"
      item-value="name"
      hide-default-footer
      :search="search"
      class="text-no-wrap"
      density="compact"
    >
      <template #item.progress="{ item }">
        <div class="d-flex align-center gap-3">
          <div class="flex-grow-1">
            <VProgressLinear
              :height="6"
              model-value="0"
              color="primary"
              rounded
            />
          </div>
          <div class="text-body-1 text-high-emphasis">
            {{ 0 }}%
          </div>
        </div>
      </template>
      <template #item.created_at="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ formatDate(item) }}</span>
        </div>
      </template>
      <template #item.Action="{ item }">
        <IconBtn>
          <VIcon icon="tabler-dots-vertical" />
          <VMenu activator="parent">
            <VList>
              <VListItem
                value="view"
                :to="{ name: 'web-design', params: { id: item.id } }"
              >
                View
              </VListItem>
              <VList>
                <VListItem
                  value="edit"
                  @click="editProject(item)"
                >
                  Edit
                </VListItem>
                <VListItem
                  value="delete"
                  @click="deleteProject(item)"
                >
                  Delete
                </VListItem>
              </VList>
            </VList>
          </VMenu>
        </IconBtn>
      </template>
      <template #bottom>
        <VCardText class="pt-2">
          <div
            v-if="isLoading"
            class="text-center"
          >
            <VProgressCircular
              :size="30"
              width="3"
              indeterminate
              color="primary"
            />
          </div>
        </VCardText>
        <TablePagination
          v-model:page="options.page"
          :items-per-page="options.itemsPerPage"
          :total-items="totalRecords"
          @update:page="handlePageChange"
        />
      </template>
    </VDataTable>
  </VCard>
</template>

<script setup>
import moment from 'moment'
import Swal from 'sweetalert2'
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useProjectStore } from "../../../store/projects"
import { useProjectTypeStore } from "../../../store/project_types"
import { useUserStore } from "../../../store/users"
import { useRouter } from 'vue-router'

const toast = useToast()
const projectStore = useProjectStore()
const projectTypeStore = useProjectTypeStore()
const userStore = useUserStore()
const router = useRouter()
const addProjectForm = ref()
const editProjectForm = ref()
const isDialogVisible = ref(false)
const isEditDialogVisible = ref(false)
const totalRecords = ref(0)

const newProjectDetails = ref({
  title: '',
  project_type_id: '',
  project_manager_id: '',
  member_ids: [],
  est_hours: '',
  est_budget: '',
})

const editProjectDetails = ref({})

const isLoading = ref(false)
const search = ref('')
const options = ref({ page: 1, itemsPerPage: 10, sortBy: [''], sortDesc: [false] })
const formatDate = date => moment(date).format('MMM DD, YYYY')

const headers = [
  {
    title: 'PROJECT',
    key: 'title',
  },
  {
    title: 'Project Manager',
    key: 'project_manager',
  },
  {
    title: 'Team',
    key: 'project_members',
  },
  {
    title: 'PROGRESS',
    key: 'progress',
  },
  {
    title: 'Created At',
    key: 'created_at',
    width: '15%',
    sortable: false,
  },
  {
    title: 'Action',
    key: 'Action',
    width: '5%',
    sortable: false,
  },
]

onBeforeMount(async () => {
  await fetchProjects()
  await fetchProjectTypes()
  await fetchProjectManagers()
  await fetchMembers()
  totalRecords.value = totalProjects.value
})

const fetchProjects = async () => {
  try {
    await projectStore.getAll(options.value.page, options.value.itemsPerPage)
    isLoading.value = true
  } catch (error) {
    toast.error('Error fetching projects:', error)
  }
  finally {
    isLoading.value = false
  }
}

async function submitAddProjectForm() {
  addProjectForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        const res = await projectStore.create(newProjectDetails.value)
        const errors = getErrors.value
        if(errors)
        {
          toast.error('Something went wrong.')
        }
        else{
          isDialogVisible.value = false
          isLoading.value = true
          toast.success('Project added successfully', { timeout: 1000 })
          await fetchProjects()

          /* const newProjectID = res.data.data.id

          console.log("PROJECT ID", newProjectID)
          router.push({ name: 'add-project-tasks', params: { project: 'web-designs', id: newProjectID } }) */
          isLoading.value = false
          newProjectDetails.value = {
            title: '',
            project_type_id: '',
            project_manager_id: '',
            member_ids: [],
            est_hours: '',
            est_budget: ''
          }
        }
      } catch (error) {
        toast.error('Failed to add project:', error)
      }
    }
  })
}

const editProject = project => {
  editProjectDetails.value = { ...project }
  isEditDialogVisible.value = true
}

async function submitEditProjectForm() {
  editProjectForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {

        const payload = {
          id: editProjectDetails.value.id,
          uuid: editProjectDetails.value.uuid,
          title: editProjectDetails.value.title,
          project_type_id: editProjectDetails.value.project_type_id,
          project_manager_id: editProjectDetails.value.project_manager_id,
          member_ids: editProjectDetails.value.member_ids,
          est_hours: editProjectDetails.value.est_hours,
          est_budget: editProjectDetails.value.est_budget,
        }

        const res = await projectStore.update(payload)
        const errors = getErrors.value
        if(errors)
        {
          toast.error('Something went wrong.')
        }
        else{
          isEditDialogVisible.value = false
          isLoading.value = true
          toast.success('Project updated successfully', { timeout: 1000 })
          await fetchProjects()
          isLoading.value = false
        }
      } catch (error) {
        toast.error('Failed to update project:', error.message || error)
      }
    }
  })
}

const deleteProject = async project => {
  try {
    const confirmDelete = await Swal.fire({
      title: "Are you sure?",
      text: `Do you want to delete ${project.title}?`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
    })

    if (confirmDelete.isConfirmed) {

      const res = await projectStore.delete(project.uuid)
      const errors = getErrors.value
      if(errors)
      {
        toast.error('Something went wrong.')
      }
      else{
        isLoading.value = true
        toast.success('Project deleted successfully', { timeout: 1000 })
        await fetchProjects()
        isLoading.value = false
      }
    }
  } catch (error) {
    toast.error('Failed to delete project:', error.message || error)
  }
}

const fetchProjectTypes = async () => {
  try {
    isLoading.value = true

    await projectTypeStore.getAll()
  } catch (error) {
    toast.error('Failed to get project types:', error.message || error)
  }
  finally {
    isLoading.value = false
  }
}

const fetchProjectManagers = async () => {
  try {
    isLoading.value = true

    await userStore.getProjectManagers()
  } catch (error) {
    toast.error('Failed to get project managers:', error.message || error)
  }
  finally {
    isLoading.value = false
  }
}

const fetchMembers = async () => {
  try {
    isLoading.value = true

    await userStore.getMembers()
  } catch (error) {
    toast.error('Failed to get members:', error.message || error)
  }
  finally {
    isLoading.value = false
  }
}

const getProjects = computed(() => {
  return projectStore.getProjects
})

const getProjectTypes = computed(() => {
  return projectTypeStore.getProjectTypes
})

const getProjectManagers = computed(() => {
  return userStore.getProjectManagersList
})

const getMembers = computed(() => {
  return userStore.getMembersList
})

const getErrors = computed(() => {
  return projectStore.getErrors
})

const totalProjects = computed(() => {
  return projectStore.projectsCount
})

const handlePageChange = async page => {
  options.value.page = page
  await fetchProjects()
}
</script>


  <style scoped>
  .table-wrapper {
      inline-size: auto;
      overflow-x: auto;
  }
  </style>

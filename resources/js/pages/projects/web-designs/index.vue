<template>
  <div>
    <!-- Toggle -->
    <VRow>
      <VCol
        cols="12"
        md="6"
        class="d-flex"
      >
        <VBtnToggle
          v-model="viewType"
          class="d-toggle"
          rounded="0"
        >
          <VIcon
            icon="tabler-list"
            class="me-1"
            :class="{ 'bg-primary': viewType === 'list' }"
            @click="viewType = 'list'"
          />
          <VIcon
            icon="tabler-layout-grid"
            :class="{ 'bg-primary': viewType === 'grid' }"
            @click="viewType = 'grid'"
          />
        </VBtnToggle>
      </VCol>
      <VCol
        cols="12"
        md="6"
      >
        <div class="d-flex justify-end mb-5">
          <VBtn
            prepend-icon="tabler-plus"
            size="small"
            rounded="pill"
            @click="isAddProjectDrawerOpen = !isAddProjectDrawerOpen"
          >
            New Project
          </VBtn>
        </div>
      </VCol>
    </VRow>
    <VRow
      v-if="viewType === 'list'"
      class="mb-4"
    >
      <VCol
        v-for="project in getProjects"
        :key="project.id"
        cols="12"
      >
        <RouterLink :to="{ name: 'web-design', params: { id: project.uuid } }">
          <VCard class="d-flex ps-4 py-1">
            <VCol cols="3">
              <span class="font-weight-medium text-high-emphasis">
                <VIcon
                  size="28"
                  class="me-2"
                  color="info"
                  icon="tabler-chart-histogram"
                />
                {{ project.title }}
              </span>
            </VCol>
            <VCol cols="3">
              <div class="d-flex align-center">
                <VAvatar
                  size="36"
                  color="primary"
                  variant="tonal"
                >
                  <span>{{ avatarText(project.project_manager) }}</span>
                </VAvatar>
                <div class="d-flex flex-column ms-3">
                  <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ project.project_manager }}</span>
                  <small class="mt-0">Project Manager</small>
                </div>
              </div>
            </VCol>
            <VCol cols="3">
              <span class="font-weight-medium text-high-emphasis">Team:</span>
              <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ project.project_members }}</span>
            </VCol>
            <VCol cols="2">
              <div class="d-flex align-center gap-3">
                <div class="flex-grow-1">
                  <VProgressLinear
                    height="6"
                    :value="project.progress"
                    rounded
                    color="primary"
                  />
                </div>
                <span>0%</span>
              </div>
            </VCol>
            <VCol
              cols="1"
              class="ms-8"
            >
              <IconBtn @click.prevent>
                <VIcon icon="tabler-dots-vertical" />
                <VMenu activator="parent">
                  <VList>
                    <VListItem
                      value="add tasks"
                      :to="{ name: 'add-project-tasks', params: { project: 'web-designs', id: project.uuid } }"
                    >
                      Add Tasks
                    </VListItem>
                    <VListItem
                      value="view"
                      :to="{ name: 'web-design', params: { id: project.uuid } }"
                    >
                      View
                    </VListItem>
                    <VList>
                      <VListItem
                        value="edit"
                        @click="editProject(project)"
                      >
                        Edit
                      </VListItem>
                      <VListItem
                        value="delete"
                        @click="deleteProject(project)"
                      >
                        Delete
                      </VListItem>
                    </VList>
                  </VList>
                </VMenu>
              </IconBtn>
            </VCol>
          </VCard>
        </RouterLink>
      </VCol>
    </VRow>

    <!-- Grid View -->
    <VRow v-else>
      <VCol
        v-for="project in getProjects"
        :key="project.id"
        cols="12"
        md="4"
      >
        <RouterLink :to="{ name: 'web-design', params: { id: project.uuid } }">
          <VCard class="pt-2">
            <VCardTitle>
              <VRow>
                <VCol cols="10">
                  <VIcon
                    size="28"
                    project-card
                    class="me-2"
                    color="info"
                    icon="tabler-chart-histogram"
                  />
                  {{ project.title }}
                </VCol>
                <VCol cols="2">
                  <IconBtn @click.prevent>
                    <VIcon icon="tabler-dots-vertical" />
                    <VMenu activator="parent">
                      <VList>
                        <VListItem
                          value="add tasks"
                          :to="{ name: 'add-project-tasks', params: { project: 'web-designs', id: project.uuid } }"
                        >
                          Add Tasks
                        </VListItem>
                        <VListItem
                          value="view"
                          :to="{ name: 'web-design', params: { id: project.uuid } }"
                        >
                          View
                        </VListItem>
                        <VList>
                          <VListItem
                            value="edit"
                            @click="editProject(project)"
                          >
                            Edit
                          </VListItem>
                          <VListItem
                            value="delete"
                            @click="deleteProject(project)"
                          >
                            Delete
                          </VListItem>
                        </VList>
                      </VList>
                    </VMenu>
                  </IconBtn>
                </VCol>
              </VRow>
            </VCardTitle>
            <VCardText class="px-3 pt-2">
              <VRow>
                <VCol cols="8">
                  <div class="d-flex align-center mt-1">
                    <VAvatar
                      size="36"
                      color="primary"
                      variant="tonal"
                    >
                      <span>{{ avatarText(project.project_manager) }}</span>
                    </VAvatar>
                    <div class="d-flex flex-column ms-3">
                      <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ project.project_manager }}</span>
                      <small class="mt-0">Project Manager</small>
                    </div>
                  </div>
                  <span class="px-1 mt-3 d-block font-weight-medium text-high-emphasis">Team:</span>
                  <span class="px-1 d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ project.project_members }}</span>
                </VCol>
                <VCol cols="4">
                  <div class="d-flex align-end justify-end">
                    <VProgressCircular
                      :rotate="360"
                      :size="70"
                      :width="6"
                      :model-value="0"
                      color="primary"
                    >
                      0%
                    </VProgressCircular>
                  </div>
                </VCol>
              </VRow>
            </VCardText>
          </VCard>
        </RouterLink>
      </VCol>
    </VRow>
  </div>
  <AddProjectDrawer
    v-model:is-drawer-open="isAddProjectDrawerOpen"
    :fetch-projects="fetchProjects"
    :get-project-types="getProjectTypes"
    :get-members="getMembers"
    :get-project-managers="getProjectManagers"
  />
  <EditProjectDrawer
    v-model:is-edit-drawer-open="isEditProjectDrawerOpen"
    :fetch-projects="fetchProjects"
    :get-project-types="getProjectTypes"
    :get-members="getMembers"
    :get-project-managers="getProjectManagers"
    :edit-project-details="editProjectDetails"
  />
</template>

<script setup>
import Swal from 'sweetalert2'
import AddProjectDrawer from '@/pages/projects/web-designs/_partials/add-project-drawer.vue'
import EditProjectDrawer from '@/pages/projects/web-designs/_partials/update-project-drawer.vue'
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useProjectStore } from "../../../store/projects"
import { useProjectTypeStore } from "../../../store/project_types"
import { useUserStore } from "../../../store/users"

const toast = useToast()
const projectStore = useProjectStore()
const projectTypeStore = useProjectTypeStore()
const userStore = useUserStore()

const totalRecords = ref(0)
const viewType = ref('list')
const isAddProjectDrawerOpen = ref(false)
const isEditProjectDrawerOpen = ref(false)
const isLoading = ref(false)

const editProjectDetails = ref({})


onBeforeMount(async () => {
  await fetchProjects()
  await fetchProjectTypes()
  await fetchProjectManagers()
  await fetchMembers()
  totalRecords.value = totalProjects.value
})

const fetchProjects = async () => {
  try {
    await projectStore.getAll()
    isLoading.value = true
  } catch (error) {
    toast.error('Error fetching projects:', error)
  }
  finally {
    isLoading.value = false
  }
}

const editProject = project => {
  editProjectDetails.value = { ...project }
  isEditProjectDrawerOpen.value = true
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
</script>

<style scoped>
.d-toggle{
    border: unset !important;
    padding: 0 !important;
    align-items: unset !important;
    block-size: unset !important;
}
</style>

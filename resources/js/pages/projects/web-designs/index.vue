<template>
  <div>
    <!-- Toggle -->
    <VRow class="mb-0">
      <VCol
        cols="12"
        :md="!(authStore.hasPermission('project-create')) ? 9 : 7"
        class="d-flex pb-0"
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
        <VIcon
          icon="tabler-filter"
          class="bg-primary ms-2"
          @click="isFilterDrawerOpen = !isFilterDrawerOpen"
        />
      </VCol>
      <VCol
        cols="12"
        md="3"
        class="pb-0"
      >
        <div class="d-flex justify-end">
          <AppAutocomplete
            v-model="selectedProjectType"
            placeholder="Select Project Type"
            :items="projectTypesWithFirstOption('All Projects')"
            item-title="name"
            item-value="id"
            @update:model-value="onFilter"
          />
        </div>
      </VCol>
      <VCol
        v-if="authStore.hasPermission('project-create')"
        cols="12"
        md="2"
        class="pb-0"
      >
        <div class="d-flex justify-end">
          <VBtn
            prepend-icon="tabler-plus"
            @click="isAddProjectDrawerOpen = !isAddProjectDrawerOpen"
          >
            New Project
          </VBtn>
        </div>
      </VCol>
    </VRow>
    <VRow class="mt-0 pt-0">
      <VCol
        cols="12"
        class="pt-0 ps-4"
      >
        <h3>Manage Projects</h3>
      </VCol>
    </VRow>

    <!-- Skeleton Loader -->
    <div v-if="getLoadStatus == 1">
      <VRow
        v-if="viewType === 'list'"
        class="mb-4"
      >
        <ListViewSkeleton />
      </VRow>
      <VRow
        v-else
        class="mb-4"
      >
        <GridViewSkeleton />
      </VRow>
    </div>

    <div v-else>
      <VRow v-if="getProjects.length === 0">
        <VCol cols="12">
          <VCard class="px-3 py-3 text-center">
            <span>No projects found</span>
          </VCard>
        </VCol>
      </VRow>
      <VRow v-else-if="viewType === 'list'">
        <VCol
          v-for="project in getProjects"
          :key="project.id"
          cols="12"
        >
          <RouterLink :to="{ name: 'web-design', params: { id: project.uuid } }">
            <VIcon
              v-if="project.is_completed"
              icon="tabler-circle-check-filled"
              class="project_completed_active"
            />
            <VCard class="d-flex ps-4 py-1 list-side-border">
              <VCol cols="3">
                <div class="d-flex align-center gap-x-3">
                  <VAvatar
                    :size="34"
                    :image="sketch"
                  />
                  <div>
                    <h6 class="text-h6 text-no-wrap">
                      <span class="d-block">{{ project.title }}</span>
                    </h6>
                    <VChip
                      color="primary"
                      size="x-small"
                    >
                      <span class="text-high-emphasis text-xs">
                        {{ project?.project_manager?.name_first + ' ' + project?.project_manager?.name_last }} (PM)
                      </span>
                    </VChip>
                    <VTooltip>
                      <template #activator="{ props }">
                        <VIcon
                          v-if="project.is_completed && project.is_pm_bucks_awarded && (authStore.isAdmin || authStore.isManager)"
                          color="primary"
                          variant="text"
                          class="tabler-coin-filled ms-1"
                          rounded
                          v-bind="props"
                        />
                      </template>
                      <small>${{ project.pm_bucks }} Awarded to PM</small>
                    </VTooltip>
                  </div>
                </div>
              </VCol>
              <VCol cols="3">
                <div class="d-flex flex-column ms-3">
                  <span class="d-block font-weight-bold text-high-emphasis text-sm text-truncate">Project Type</span>
                  <small>{{ project.project_type }}</small>
                </div>
              </VCol>
              <VCol
                cols="3"
                class="d-flex justify-center align-items-center"
              >
                <div
                  class="text-center text-h6 mb-2"
                  style="position: absolute; bottom: 32px;"
                >
                  <small>{{ project.total_tasks }}</small>
                </div>

                <div class="d-flex justify-between align-center w-100">
                  <div class="text-body-1 text-high-emphasis">
                    <small>{{ project.progress }}%</small>
                  </div>
                  <div class="flex-grow-1 mx-2">
                    <VProgressLinear
                      :height="6"
                      :model-value="project.progress"
                      color="primary"
                      rounded
                    />
                  </div>
                  <div class="text-body-1 text-high-emphasis">
                    <small>{{ project.completed_tasks }}</small>
                  </div>
                </div>
              </VCol>

              <VCol
                v-if="project.is_completed"
                cols="2"
              >
                <div class="d-flex flex-column ms-3">
                  <span class="d-block font-weight-bold text-high-emphasis text-sm text-truncate text-center">Completed At</span>
                  <small class="text-center">{{ formatDate(project.completed_at) }}</small>
                </div>
              </VCol>

              <VCol
                v-else
                cols="2"
              >
                <div class="d-flex flex-column ms-3">
                  <span class="d-block font-weight-bold text-high-emphasis text-sm text-truncate text-center">Due Date</span>
                  <small class="text-center">{{ project.launching_date == 'Today' ? '---' : formatDate(project.launching_date) }}</small>
                </div>
              </VCol>

              <VCol
                cols="1"
                class="d-flex justify-end"
              >
                <IconBtn @click.prevent>
                  <VIcon icon="tabler-dots" />
                  <VMenu activator="parent">
                    <VList>
                      <VListItem
                        value="phases"
                        :to="{ name: 'add-project-tasks', params: { id: project.uuid } }"
                      >
                        Tasks
                      </VListItem>
                      <VListItem
                        value="view"
                        :to="{ name: 'web-design', params: { id: project.uuid } }"
                      >
                        View
                      </VListItem>
                      <VListItem
                        v-if="authStore.hasPermission('project-edit')"
                        value="edit"
                        @click="editProject(project)"
                      >
                        Edit
                      </VListItem>
                      <VListItem
                        v-if="authStore.hasPermission('project-delete')"
                        value="delete"
                        @click="deleteProject(project)"
                      >
                        Delete
                      </VListItem>
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
            <VCard
              class="pt-2"
              style="position: relative; overflow: visible;"
            >
              <VIcon
                v-if="project.is_completed"
                icon="tabler-circle-check-filled"
                class="project_completed_active_grid"
              />
              <VCardTitle>
                <VRow>
                  <VCol cols="10">
                    <div class="d-flex align-center gap-x-3">
                      <VAvatar
                        :size="34"
                        :image="sketch"
                      />
                      <div>
                        <h6 class="text-h6 text-no-wrap">
                          {{ project.title }}
                        </h6>
                        <VChip
                          color="primary"
                          size="x-small"
                        >
                          <span class="text-high-emphasis text-xs">
                            {{ project?.project_manager?.name_first + ' ' + project?.project_manager?.name_last }} (PM)
                          </span>
                        </VChip>
                        <VTooltip>
                          <template #activator="{ props }">
                            <VIcon
                              v-if="project.is_completed && project.is_pm_bucks_awarded && (authStore.isAdmin || authStore.isManager)"
                              color="primary"
                              variant="text"
                              class="tabler-coin-filled ms-1"
                              rounded
                              v-bind="props"
                            />
                          </template>
                          <small>${{ project.pm_bucks }} Awarded to PM</small>
                        </VTooltip>
                      </div>
                    </div>
                  </VCol>
                  <VCol cols="2">
                    <IconBtn @click.prevent>
                      <VIcon icon="tabler-dots-vertical" />
                      <VMenu activator="parent">
                        <VList>
                          <VListItem
                            value="phases"
                            :to="{ name: 'add-project-tasks', params: { id: project.uuid } }"
                          >
                            Tasks
                          </VListItem>
                          <VListItem
                            value="view"
                            :to="{ name: 'web-design', params: { id: project.uuid } }"
                          >
                            View
                          </VListItem>
                          <VListItem
                            v-if="authStore.hasPermission('project-edit')"
                            value="edit"
                            @click="editProject(project)"
                          >
                            Edit
                          </VListItem>
                          <VListItem
                            v-if="authStore.hasPermission('project-delete')"
                            value="delete"
                            @click="deleteProject(project)"
                          >
                            Delete
                          </VListItem>
                        </VList>
                      </VMenu>
                    </IconBtn>
                  </VCol>
                </VRow>
              </VCardTitle>
              <VCardText class="px-3 pt-2">
                <VRow>
                  <VCol cols="8">
                    <div class="d-flex flex-column ms-3">
                      <span class="d-block font-weight-bold text-high-emphasis text-sm text-truncate">Project Type</span>
                      <small>{{ project.project_type }}</small>
                    </div>
                    <div
                      v-if="project.is_completed"
                      class="d-flex flex-column ms-3"
                    >
                      <small><span class="font-weight-bold text-high-emphasis text-xs text-center text-truncate">Completed At: {{ formatDate(project.completed_at) }}</span></small>
                    </div>
                    <div
                      v-else
                      class="d-flex flex-column ms-3"
                    >
                      <small><span class="font-weight-bold text-high-emphasis text-xs text-center text-truncate">Due Date: {{ project.launching_date == 'Today' ? '' : formatDate(project.launching_date) }}</span></small>
                    </div>
                  </VCol>
                  <VCol cols="4">
                    <div class="d-flex align-end justify-end">
                      <VProgressCircular
                        :rotate="360"
                        :size="70"
                        :width="6"
                        :model-value="project.progress"
                        color="primary"
                      >
                        <span>{{ project.progress }}%</span>
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

    <TablePagination
      v-if="getLoadStatus !== 1 && getProjects.length > 0"
      v-model:page="options.page"
      :items-per-page="options.itemsPerPage"
      :total-items="totalProjects"
      class="custom-pagination"
      @update:page="handlePageChange"
    />
  </div>
  <AddProjectDrawer
    v-model:is-drawer-open="isAddProjectDrawerOpen"
    :fetch-projects="fetchProjects"
    :get-project-types="getProjectTypes"
    :get-staff-list="getStaffListsForDropDown"
    :get-clients="getClients"
    :get-templates="getTemplates"
    :get-project-managers-list="getProjectManagers"
    :get-load-status="getLoadStatus"
  />
  <EditProjectDrawer
    v-model:is-edit-drawer-open="isEditProjectDrawerOpen"
    :fetch-projects="fetchProjects"
    :get-project-types="getProjectTypes"
    :get-staff-list="getStaffListsForDropDown"
    :edit-project-details="editProjectDetails"
    :get-clients="getClients"
    :get-project-managers-list="getProjectManagers"
    :get-load-status="getLoadStatus"
  />
  <FilterDrawer
    v-model:is-filter-drawer-open="isFilterDrawerOpen"
    :apply-filters="applyFilters"
    :get-project-types="projectTypesWithFirstOption('All Projects')"
    :selected-project-type="selectedProjectType"
    :get-project-managers="projectManagersWithFirstOption"
    :get-load-status="getLoadStatus"
  />
</template>

<script setup>
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import Swal from 'sweetalert2'
import sketch from '@images/icons/project-icons/sketch.png'
import AddProjectDrawer from '@/pages/projects/web-designs/_partials/add-project-drawer.vue'
import EditProjectDrawer from '@/pages/projects/web-designs/_partials/update-project-drawer.vue'
import FilterDrawer from '@/pages/projects/web-designs/_partials/filter-projects-drawer.vue'
import ListViewSkeleton from '@/pages/projects/web-designs/_partials/list-view-skeleton.vue'
import GridViewSkeleton from '@/pages/projects/web-designs/_partials/grid-view-skeleton.vue'
import { computed, onBeforeMount, onMounted, onUnmounted, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useProjectStore } from "../../../store/projects"
import { useProjectTypeStore } from "../../../store/project_types"
import { useUserStore } from "../../../store/users"
import { useAuthStore } from '@/store/auth'
import { useTemplateStore } from '@/store/templates'
import { useRoute } from 'vue-router'
import moment from 'moment'

useHead({ title: `${layoutConfig.app.title} | Manage Projects` })
onBeforeMount(async () => {
  await fetchProjects()
  await fetchProjectTypes()
  await fetchTemplates()
  await fetchMembers()
  totalRecords.value = totalProjects.value
})

const toast = useToast()
const projectStore = useProjectStore()
const projectTypeStore = useProjectTypeStore()
const userStore = useUserStore()
const authStore = useAuthStore()
const templateStore = useTemplateStore()
const route = useRoute()

const totalRecords = ref(0)
const viewType = ref('list')
const isAddProjectDrawerOpen = ref(false)
const isEditProjectDrawerOpen = ref(false)
const isFilterDrawerOpen = ref(false)
const isLoading = ref(false)
const selectedProjectType = ref(null)
const editProjectDetails = ref({})
const search = ref('')
const selectedProjectManagerId = ref(null)
const options = ref({ page: 1, itemsPerPage: 10, orderBy: '', order: '' })

const formatDate = date => moment(date).format('MM/DD/YYYY')

const isMobile = () => {
  return window.innerWidth <= 768 || window.innerWidth <= 926
}

const handleResize = () => {
  viewType.value = isMobile() ? 'grid' : 'list'
}

onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})

const fetchProjects = async () => {
  try {
    isLoading.value = true
    await projectStore.getAll(options.value.page, options.value.itemsPerPage, search.value, selectedProjectType.value, selectedProjectManagerId.value)
  } catch (error) {
    toast.error('Error fetching projects:', error)
  } finally {
    isLoading.value = false
  }
}

const fetchTemplates = async () => {
  try {
    isLoading.value = true
    await templateStore.getAll()
  } catch (error) {
    toast.error('Error fetching templates:', error)
  } finally {
    isLoading.value = false
  }
}

const applyFilters = async (searchQuery = '', selectedProject = null, selectedProjectManager = null) => {
  search.value = searchQuery
  selectedProjectType.value = selectedProject
  selectedProjectManagerId.value = selectedProjectManager
  await fetchProjects()
}

const editProject = project => {
  editProjectDetails.value = { ...project }
  isEditProjectDrawerOpen.value = true
}

const deleteProject = async project => {
  try {
    const confirmDelete = await Swal.fire({
      title: "Are you certain about deleting this project?",
      text: `Once it’s gone, it’s gone.`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#a12592",
      cancelButtonColor: "#808390",
      confirmButtonText: "Yes, delete it!",
      didOpen: () => {
        document.querySelector('.swal2-confirm').blur()

        const title = document.querySelector('.swal2-title')
        if (title) {
          title.style.fontSize = '18px'
        }

        // Apply custom styles to text
        const text = document.querySelector('.swal2-html-container')
        if (text) {
          text.style.marginTop = '8px'
        }
      },
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

const fetchMembers = async () => {
  try {
    isLoading.value = true

    await userStore.getMembers()
  } catch (error) {
    toast.error('Failed to get members:', error.message || error)
  } finally {
    isLoading.value = false
  }
}

const handlePageChange = async page => {
  options.value.page = page
  await fetchProjects()
}

const onFilter = async value => {
  selectedProjectType.value = value
  options.value.page = 1
  await fetchProjects()
}

const getProjects = computed(() => {
  return projectStore.getProjects
})

const getProjectTypes = computed(() => {
  return projectTypeStore.getProjectTypes
})

const getClients = computed(() => {
  let members = userStore.getMembersList
  let clients = members.filter(member => member.role === USER_ROLES.CLIENT)

  return clients.map(client => ({ id: client.id, name: client.name_first + " " + client.name_last }))
})

const getProjectManagers = computed(() => {
  let members = userStore.getMembersList
  let projectManagers = members.filter(member => member.role === USER_ROLES.PROJECT_MANAGER)

  return projectManagers.map(manager => ({ id: manager.id, name: manager.name_first + " " + manager.name_last }))
})

const projectTypesWithFirstOption = (firstOption = null) => {
  const projectTypes = computed(() => {
    let types = [...projectTypeStore.getProjectTypes]
    if (firstOption) {
      types.unshift({ id: null, name: firstOption })
    }

    return types
  })

  return projectTypes.value
}

const projectManagersWithFirstOption = computed(() => {
  let members = userStore.getMembersList
  let projectManagers = members.filter(member => member.role === USER_ROLES.PROJECT_MANAGER)

  let managers = projectManagers.map(manager => ({ id: manager.id, name: manager.name_first + " " + manager.name_last }))

  managers.unshift({ id: null, name: '-- Select --' })

  return managers
})

const getStaffListsForDropDown = computed(() => {
  let members = userStore.getMembersList
  let staff = members.filter(member => member.role === USER_ROLES.STAFF)

  return staff.map(staff => ({ label: staff.name_first + " " + staff.name_last, value: staff.id }))
})

const getTemplates = computed(() => {
  return templateStore.getTemplatesDropdown
})

const getErrors = computed(() => {
  return projectStore.getErrors
})

const getLoadStatus = computed(() => {
  return projectStore.getLoadStatus
})

const totalProjects = computed(() => {
  return projectStore.projectsCount
})

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

watch(
  () => route.query.type,
  async newType => {
    if (newType) {
      selectedProjectType.value = parseInt(newType)
      await fetchProjects()
    }
  },
  { immediate: true },
)
</script>

<style scoped>
.d-toggle{
    border: unset !important;
    padding: 0 !important;
    align-items: unset !important;
    block-size: unset !important;
}

.custom-pagination :deep(hr) {
  display: none !important;
}
</style>

<template>
  <div>
    <!-- Toggle -->
    <VRow class="mb-0">
      <VCol
        cols="12"
        md="7"
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
            <VCard class="d-flex ps-4 py-1 list-side-border">
              <VCol cols="4">
                <div class="d-flex align-center gap-x-3">
                  <VAvatar
                    :size="34"
                    :image="sketch"
                  />
                  <div>
                    <h6 class="text-h6 text-no-wrap">
                      <span class="d-block">{{ project.title }}</span>
                    </h6>
                  </div>
                </div>
              </VCol>
              <VCol cols="4">
                <div class="d-flex flex-column ms-3">
                  <span class="d-block font-weight-bold text-high-emphasis text-sm text-truncate">Project Type</span>
                  <small>{{ project.project_type }}</small>
                </div>
              </VCol>
              <VCol cols="2">
                <div class="d-flex align-center gap-3">
                  <div class="flex-grow-1 mt-2">
                    <VProgressLinear
                      :height="6"
                      :model-value="project.progress"
                      color="primary"
                      rounded
                    />
                  </div>
                  <div class="text-body-1 text-high-emphasis mt-2">
                    {{ project.progress }}%
                  </div>
                </div>
              </VCol>
              <VCol
                cols="2"
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
                    <div class="d-flex align-center gap-x-3">
                      <VAvatar
                        :size="34"
                        :image="sketch"
                      />
                      <div>
                        <h6 class="text-h6 text-no-wrap">
                          {{ project.title }}
                        </h6>
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
                    <div class="d-flex flex-column ms-3">
                      <span class="d-block font-weight-bold text-high-emphasis text-sm text-truncate">Project Type</span>
                      <small>{{ project.project_type }}</small>
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
    :get-members="getMembers"
    :get-load-status="getLoadStatus"
  />
  <EditProjectDrawer
    v-model:is-edit-drawer-open="isEditProjectDrawerOpen"
    :fetch-projects="fetchProjects"
    :get-project-types="getProjectTypes"
    :get-members="getMembers"
    :edit-project-details="editProjectDetails"
    :get-load-status="getLoadStatus"
  />
  <FilterDrawer
    v-model:is-filter-drawer-open="isFilterDrawerOpen"
    :apply-filters="applyFilters"
    :get-project-types="projectTypesWithFirstOption('All Projects')"
    :selected-project-type="selectedProjectType"
    :get-project-managers="projectManagersWithFirstOption('-- Select --')"
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
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useProjectStore } from "../../../store/projects"
import { useProjectTypeStore } from "../../../store/project_types"
import { useUserStore } from "../../../store/users"
import { useRoute } from 'vue-router'

useHead({ title: `${layoutConfig.app.title} | Manage Projects` })
onBeforeMount(async () => {
  await fetchProjects()
  await fetchProjectTypes()
  await fetchProjectManagers()
  await fetchMembers()
  totalRecords.value = totalProjects.value
})

const toast = useToast()
const projectStore = useProjectStore()
const projectTypeStore = useProjectTypeStore()
const userStore = useUserStore()
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

const fetchProjects = async () => {
  try {
    await projectStore.getAll(options.value.page, options.value.itemsPerPage, search.value, selectedProjectType.value, selectedProjectManagerId.value)
    isLoading.value = true
  } catch (error) {
    toast.error('Error fetching projects:', error)
  }
  finally {
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
      title: "Are you sure?",
      text: `Do you want to delete ${project.title}?`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#a12592",
      cancelButtonColor: "#808390",
      confirmButtonText: "Yes, delete it!",
      didOpen: () => {
        document.querySelector('.swal2-confirm').blur()
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

const getProjectManagers = computed(() => {
  return userStore.getProjectManagersList
})

const projectManagersWithFirstOption = (firstOption = null) => {
  const projectManagers = computed(() => {
    let managers = [...userStore.getProjectManagersList]
    if (firstOption) {
      managers.unshift({ id: null, name: firstOption })
    }
    
    return managers
  })

  return projectManagers.value
}

const getMembers = computed(() => {
  return userStore.getMembersList
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

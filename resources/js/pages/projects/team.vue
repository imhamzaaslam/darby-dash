<template>
  <div>
    <!-- Toggle -->
    <VRow>
      <VCol
        cols="12"
        md="7"
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
        <!-- <VIcon
          icon="tabler-filter"
          class="bg-primary ms-2"
          @click="isFilterDrawerOpen = !isFilterDrawerOpen"
        /> -->
      </VCol>
      <VCol
        cols="12"
        md="5"
      >
        <div class="d-flex justify-end mb-5">
          <VBtn
            prepend-icon="tabler-plus"
            @click="isAddProjectDrawerOpen = !isAddProjectDrawerOpen"
          >
            New Member
          </VBtn>
        </div>
      </VCol>
    </VRow>

    <VRow
      v-if="isMembersLoading"
      class="mb-4"
    >
      <VCol cols="12">
        <VCard
          class="d-flex justify-center align-center"
          height="200px"
        >
          <VProgressCircular
            indeterminate
            color="primary"
          />
        </VCard>
      </VCol>
    </VRow>

    <div v-else>
      <VRow
        v-if="viewType === 'list'"
        class="mb-4"
      >
        <VCol
          v-for="member in getUsersByProjects"
          :key="member.id"
          cols="12"
        > 
          <VCard class="d-flex ps-4 py-1">
            <VCol cols="3">
              <div class="d-flex align-center">
                <VAvatar
                  size="36"
                  color="primary"
                  variant="tonal"
                >
                  <span>{{ avatarText(member.name_first + ' ' + member.name_last) }}</span>
                </VAvatar>
                <div class="d-flex flex-column ms-3">
                  <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ member.name_first }} {{ member.name_last }}</span>
                </div>
              </div>
            </VCol>
            <VCol cols="3">
              <span>
                {{ member.email }}
              </span>
            </VCol>
            <VCol cols="3">
              <VChip
                variant="outlined"
                color="primary"
                size="small"
              >
                <span>{{ member.role }}</span>
              </VChip>
            </VCol>
            <VCol cols="2">
              <VChip
                variant="outlined"
                color="success"
                size="small"
              >
                <span>{{ roleStore.capitalizeFirstLetter(member.state) }}</span>
              </VChip>
            </VCol>
            <VCol
              cols="1"
              class="ms-8"
            >
              <IconBtn @click.prevent>
                <VIcon icon="tabler-dots" />
                <VMenu activator="parent">
                  <VList>
                    <VList>
                      <VListItem
                        value="edit"
                        @click="editProject(project)"
                      >
                        Edit
                      </VListItem>
                      <VListItem
                        value="delete"
                        @click="deleteMember(project)"
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

      <!-- Grid View -->
      <VRow v-else>
        <VCol
          v-for="member in getUsersByProjects"
          :key="member.id"
          cols="12"
          md="4"
        >
          <VCard>
            <div class="image-container">
              <VImg :src="Page2" />
              <IconBtn
                class="dots-icon"
                @click.prevent
              >
                <VIcon icon="tabler-dots-vertical" />
                <VMenu activator="parent">
                  <VList>
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
            </div>
            <VCardText class="position-relative">
              <!-- User Avatar -->
              <VAvatar
                size="75"
                class="avatar-center"
                color="primary"
              >
                <span>{{ avatarText(member.name_first + ' ' + member.name_last) }}</span>
              </VAvatar>
    
              <VRow class="mt-5">
                <VCol cols="8">
                  <div class="d-flex align-center mt-1 ms-1">
                    <div class="d-flex flex-column ms-3">
                      <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ member.name_first }} {{ member.name_last }}</span>
                      <span class="text-sm text-truncate mb-0">{{ member.email }}</span>
                      <VChip
                        variant="outlined"
                        color="primary"
                        size="small"
                        class="mt-3 position-absolute user_role"
                      >
                        <span>{{ member.role }}</span>
                      </VChip>
                    </div>
                  </div>
                </VCol>
              </VRow>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>
    </div>
  </div>
  
  
  <AddMemberDrawer
    v-model:is-drawer-open="isAddMemberDrawerOpen"
    :fetch-members="fetchMembers"
    :get-roles="getRoles"
    :get-errors="getErrors"
    :get-status-code="getStatusCode"
    :get-load-status="getLoadStatus"
  /> 
 
  <!--
    <EditMemberDrawer
    v-model:is-edit-drawer-open="isEditMemberDrawerOpen"
    :fetch-members="fetchMembers"
    :get-roles="getRoles"
    :get-errors="getErrors"
    :get-status-code="getStatusCode"
    :edit-member-details="editMemberDetails"
    :get-load-status="getLoadStatus"
    />
    <FilterDrawer
    v-model:is-filter-drawer-open="isFilterDrawerOpen"
    :fetch-projects="fetchProjects"
    :get-members="getMembers"
    :get-project-managers="getProjectManagers"
    :get-load-status="getLoadStatus"
    /> 
  -->
</template>

<script setup>
import Swal from 'sweetalert2'
import AddMemberDrawer from '@/pages/teams/_partials/add-member-drawer.vue'
import EditMemberDrawer from '@/pages/teams/_partials/update-member-drawer.vue'
import FilterDrawer from '@/pages/projects/web-designs/_partials/filter-projects-drawer.vue'
import Page2 from '../../../images/pages/2.png'
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useProjectStore } from "../../store/projects"
import { useProjectTypeStore } from "../../store/project_types"
import { useUserStore } from "../../store/users"
import { useRoleStore } from "../../store/roles"
import { useRoute } from 'vue-router'

const toast = useToast()
const projectStore = useProjectStore()
const projectTypeStore = useProjectTypeStore()
const userStore = useUserStore()
const roleStore = useRoleStore()
const route = useRoute()

const totalRecords = ref(0)
const viewType = ref('list')
const isAddMemberDrawerOpen = ref(false)
const isEditMemberDrawerOpen = ref(false)
const isFilterDrawerOpen = ref(false)
const isLoading = ref(false)
const isMembersLoading = ref(false)
const selectedProjectType = ref(1)
const editProjectDetails = ref({})

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

onBeforeMount(async () => {
  await fetchMembersByProject()
  await fetchProjects()
  await fetchProjectTypes()
  await fetchProjectManagers()
  await fetchMembers()
  totalRecords.value = totalProjects.value
})

const fetchProjects = async () => {
  try {
    console.log("SELECTED VALUE TYPE", selectedProjectType.value)
    await projectStore.getByType(selectedProjectType.value)
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

const deleteMember = async member => {
  try {
    const confirmDelete = await Swal.fire({
      title: "Are you sure?",
      text: `Do you want to delete ?`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
    })

    if (confirmDelete.isConfirmed) {
      await userStore.delete(member.uuid)
      if(getErrors.value)
      {
        toast.error('Something went wrong. Please try again later.')
      }
      else{
        isLoading.value = true
        toast.success('Member deleted successfully', { timeout: 1000 })
        await fetchMembers()
        isLoading.value = false
      }
    }
  } catch (error) {
    toast.error('Failed to delete member:', error.message || error)
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
    if(getLoadStatus.value === 1) return

    await userStore.getAll()
    isLoading.value = true
  } catch (error) {
    toast.error('Error fetching members:', error)
  }
  finally {
    isLoading.value = false
  }
}

const fetchMembersByProject = async () => {
  try {
    isMembersLoading.value = true

    const projectUuid = route.params.id

    await userStore.getByProjects(projectUuid)
    isMembersLoading.value = false
  } catch (error) {
    toast.error('Error fetching members:', error)
  }
  finally {
    isLoading.value = false
  }
}

const getProjects = computed(() => {
  return projectStore.getProjectsByType
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

const getRoles = computed(() => {
  return roleStore.getRoles
})

const getUsers = computed(() => {
  return userStore.getUsers
})

const getUsersByProjects = computed(() => {
  return userStore.getUsersByProjects
})

const getErrors = computed(() => {
  return userStore.getErrors
})

const getStatusCode = computed(() => {
  return userStore.getStatusCode
})

const getLoadStatus = computed(() => {
  return userStore.getLoadStatus
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

.avatar-center {
  position: absolute;
  border: 3px solid rgb(var(--v-theme-surface));
  inset-block-start: -2rem;
  inset-inline-start: 1rem;
}

.image-container {
  position: relative;
}

.dots-icon {
  position: absolute;
  top: 8px;
  right: 8px;
}

.user_role{
  right: 10px;
}
</style>

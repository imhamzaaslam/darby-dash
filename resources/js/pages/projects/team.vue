<template>
  <div>
    <!-- Toggle -->
    <VRow>
      <VCol
        cols="12"
        md="9"
        class="d-flex"
      >
        <div class="d-flex align-center">
          <VAvatar
            icon="tabler-world"
            size="36"
            class="me-2"
            color="primary"
            variant="tonal"
          /> 
          <!-- <VAvatar
            v-if="project?.project_logo"
            size="36"
            class="me-2"
          >
            <VImg :src="project?.project_logo" /> 
          </VAvatar>
          <VAvatar
            v-else
            icon="tabler-cube"
            size="36"
            class="me-2"
            color="primary"
            variant="tonal"
          /> -->
          <h3 class="text-primary">
            {{ project?.title }}
            <span class="d-block text-xs text-black">{{ project?.project_type }}</span>
          </h3>
        </div>
      </VCol>
      <VCol
        cols="12"
        md="3"
      >
        <div class="d-flex justify-end">
          <VBtnToggle
            v-model="viewType"
            class="d-toggle align-center-important"
            rounded="0"
          >
            <VIcon
              icon="tabler-list"
              class="me-1 ms-4"
              :class="{ 'bg-primary': viewType === 'list' }"
              @click="viewType = 'list'"
            />
            <VIcon
              icon="tabler-layout-grid"
              :class="{ 'bg-primary': viewType === 'grid' }"
              @click="viewType = 'grid'"
            />
            <VIcon
              :icon="useMagnifierIcon == 1 ? 'tabler-search' : 'tabler-filter'"
              class="bg-primary ms-1"
              @click="isFilterDrawerOpen = !isFilterDrawerOpen"
            />
          </VBtnToggle>
          <VBtn
            icon
            color="td-hover"
            class="ma-2 custom-btn-style"
            size="small"
            rounded="pills"
            @click.prevent
          >
            <VIcon icon="tabler-dots" />
            <VMenu activator="parent">
              <VList>
                <VListItem
                  value="add-team-member"
                  @click="(isAddMemberDialogueOpen = !isAddMemberDialogueOpen, isMultiselectValid = true)"
                >
                  Add Team Member
                </VListItem>
              </VList>
            </VMenu>
          </VBtn>
        </div>
      </VCol>
    </VRow>
    <VRow class="mt-5 mb-3 pt-0 pb-0">
      <VCol
        cols="12"
        class="pt-0 ps-4 pb-0"
      >
        <h3>
          Manage Team
        </h3>
      </VCol>
    </VRow>

    <div
      v-if="getProjectLoadStatus !== 1 && getUsersByProjects.length === 0"
      class="text-center"
    >
      <div
        class="mt-12"
        v-html="NoTaskInList"
      />
      <span>
        No team members found
      </span>
    </div>

    <div v-else>
      <!-- Skeleton Loader -->
      <div v-if="getProjectLoadStatus === 1 || getUserLoadStatus === 1">
        <VRow
          v-if="viewType === 'list'"
          class="mb-4"
        >
          <TeamListSkeleton />
        </VRow>

        <!-- Grid View Skeleton Loader -->
        <VRow v-else>
          <TeamGridSkeleton />
        </VRow>
      </div>

      <div v-else>
        <VRow v-if="viewType === 'list'">
          <VCol
            v-for="member in getUsersByProjects"
            :key="member.value"
            cols="12"
          >
            <VCard
              class="d-flex align-center ps-4 py-1 list-side-border"
            >
              <VCol cols="4">
                <div class="d-flex align-center">
                  <VBadge
                    dot
                    location="top end"
                    offset-x="1"
                    offset-y="1"
                    :color="member.is_online ? 'success' : 'warning'"
                  >
                    <VAvatar
                      size="36"
                      :class="member.avatar ? '' : 'text-white bg-primary'"
                      :image="member?.info?.avatar ? getImageUrl(member?.info?.avatar?.path) : undefined"
                      :variant="!member.avatar ? 'tonal' : ''"
                    >
                      <span>{{ avatarText(member.name_first + ' ' + member.name_last) }}</span>
                    </VAvatar>
                  </VBadge>
                  <div class="d-flex flex-column ms-3">
                    <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ member.name_first }} {{ member.name_last }}</span>
                    <small class="mt-0 text-xs">
                      {{ roleStore.capitalizeFirstLetter(member.role) }}
                    </small>
                  </div>
                </div>
              </VCol>
              <VCol cols="4">
                <VIcon
                  color="primary"
                  icon="tabler-mail"
                />
                <span class="ms-2">
                  {{ member.email }}
                </span>
              </VCol>
              <VCol
                cols="3"
                class="ms-2"
              >
                <VIcon
                  color="primary"
                  icon="tabler-phone"
                />
                <span class="ms-2">
                  {{ member?.info.phone }}
                </span>
              </VCol>
              <VCol
                cols="1"
                class="ms-2 cursor-pointer"
                @click.stop="deleteMember(member)"
              >
                <VIcon
                  color="primary"
                  class="tabler-user-off"
                >
                  <VTooltip
                    activator="parent"
                    location="top"
                  >
                    <span>Remove from Project</span>
                  </VTooltip>
                </VIcon>
              </VCol>
            </VCard>
          </VCol>
        </VRow>

        <!-- Grid View -->
        <VRow v-else>
          <VCol
            v-for="member in getUsersByProjects"
            :key="member.value"
            cols="12"
            md="4"
          >
            <VCard>
              <div class="image-container">
                <VImg :src="Page2" />
              </div>
              <VCardText class="position-relative d-flex align-center">
                <VAvatar
                  size="80"
                  :class="member.avatar ? '' : 'text-white bg-primary'"
                  :image="member?.info?.avatar ? getImageUrl(member?.info?.avatar?.path) : undefined"
                  :variant="!member.avatar ? 'tonal' : ''"
                  class="avatar-center"
                >
                  <span>{{ avatarText(member.name_first + ' ' + member.name_last) }}</span>
                </VAvatar>
                <div class="d-flex flex-column ms-3">
                  <span class="font-weight-medium text-high-emphasis text-sm text-truncate side-flick-name ms-4">{{ member.name_first }} {{ member.name_last }} <VBadge
                    dot
                    :color="member.is_online ? 'success' : 'warning'"
                    class="ms-2 mb-2"
                  /></span>
                  <small class="mt-0 text-xs side-flick-role ms-4">
                    {{ roleStore.capitalizeFirstLetter(member.role) }}
                  </small>
                  <div class="d-flex align-center mt-8">
                    <VIcon
                      color="primary"
                      icon="tabler-mail"
                    />
                    <span class="ms-1 text-sm">{{ member.email }}</span>
                  </div>
                  <div class="d-flex align-center mt-2">
                    <VIcon
                      color="primary"
                      icon="tabler-phone"
                    />
                    <span class="ms-1 text-sm">{{ member?.info.phone }}</span>
                  </div>
                </div>
              </VCardText>
              <div
                class="position-absolute delete-icon"
                @click.stop="deleteMember(member)"
              >
                <VIcon
                  color="primary"
                  class="tabler-user-off"
                >
                  <VTooltip
                    activator="parent"
                    location="top"
                  >
                    <span>Remove from Project</span>
                  </VTooltip>
                </VIcon>
              </div>
            </VCard>
          </VCol>
        </VRow>
      </div>
    </div>

    <TablePagination
      v-if="getProjectLoadStatus !== 1 && getUserLoadStatus !== 1 && getUsersByProjects.length > 0"
      v-model:page="options.page"
      :items-per-page="options.itemsPerPage"
      :total-items="totalUsers"
      class="custom-pagination"
      @update:page="handlePageChange"
    />
  </div>
  <FilterDrawer
    v-model:is-filter-drawer-open="isFilterDrawerOpen"
    :apply-filters="applyFilters"
    :get-roles="rolesWithFirstOption('All Members')"
    :selected-role="selectedRole"
    :get-load-status="getProjectLoadStatus"
  />
  <VDialog
    v-model="isAddMemberDialogueOpen"
    persistent
    class="v-dialog-sm"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="isAddMemberDialogueOpen = !isAddMemberDialogueOpen" />

    <!-- Dialog Content -->
    <VCard title="Add Team Member">
      <VForm
        ref="addMemberForm"
        @submit.prevent="submitAddMemberForm"
      >
        <VCardText>
          <!--
            <Multiselect
            ref="focusInput"
            v-model="newMembers"
            mode="tags"
            placeholder="Select Members"
            close-on-select
            searchable
            :options="getMembers"
            :class="{'is-invalid': !isMultiselectValid}"
            class="bg-background multiselect-purple"
            style="color: #000 !important;"
            @blur="validateMultiselect"
            @update:model-value="isMultiselectValid = true"
            /> 
          -->
          <AppAutocomplete
            ref="focusInput"
            v-model="newMembers"
            label="Select Members*"
            placeholder="Select Members"
            :items="getMembers"
            :item-title="item => item.name"
            :item-value="item => item.id"
            chips
            multiple
            closable-chips
            eager
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
                  >{{ avatarText(item?.raw?.shortName) }}</small>
                </VAvatar>
                <span class="ms-2">{{ item.raw.shortName }}</span>
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
                  <span v-if="!item?.raw?.avatar">{{ avatarText(item?.raw?.shortName) }}</span>
                </VAvatar>
                <span class="ms-2">{{ item.raw.shortName }}</span>
              </VListItem>
            </template>
          </AppAutocomplete>
          <div
            v-if="!isMultiselectValid"
            class="text-error"
          >
            Please select at least one member.
          </div>
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            type="submit"
            class="custom-btn-style"
            :disabled="getProjectLoadStatus === 1"
            @click="addMemberForm?.validate()"
          >
            <span v-if="getProjectLoadStatus === 1">
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
            color="secondary"
            class="custom-secondary-btn"
            @click="isAddMemberDialogueOpen = false"
          >
            Cancel
          </VBtn>
        </VCardText>
      </VForm>
    </VCard>
  </VDialog>
</template>

<script setup>
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import Swal from 'sweetalert2'
import Multiselect from '@vueform/multiselect'
import TeamListSkeleton from '@/pages/projects/_partials/team-list-skeleton.vue'
import TeamGridSkeleton from '@/pages/projects/_partials/team-grid-skeleton.vue'
import FilterDrawer from '@/pages/projects/_partials/filter-members-drawer.vue'
import Page2 from '../../../images/pages/2.png'
import NoTaskInList from '@images/darby/tasks_list.svg?raw'
import { computed, onBeforeMount, onMounted, onUnmounted, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useProjectStore } from "../../store/projects"
import { useUserStore } from "../../store/users"
import { useRoleStore } from "../../store/roles"
import { useAuthStore } from "@/store/auth"
import { useRoute } from 'vue-router'
import sketch from '@images/icons/project-icons/sketch.png'

const toast = useToast()
const projectStore = useProjectStore()
const userStore = useUserStore()
const roleStore = useRoleStore()
const authStore = useAuthStore()
const router = useRoute()

const focusInput = ref()
const viewType = ref('list')
const addMemberForm = ref()
const isAddMemberDialogueOpen = ref(false)
const isLoading = ref(false)
const selectedMembers = ref([])
const newMembers = ref([])
const isFilterDrawerOpen = ref(false)
const searchName = ref('')
const searchEmail = ref('')
const selectedRole = ref(null)
const options = ref({ page: 1, itemsPerPage: 10, orderBy: '', order: '' })
const isMultiselectValid = ref(true)

const projectId = computed(() => router.params.id)

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

onBeforeMount(async () => {
  await getByProjects()
  await fetchProject()
  await fetchMembers()
  await setSelectedMembers()
})

const validateMultiselect = () => {
  isMultiselectValid.value = newMembers.value.length > 0
}

async function submitAddMemberForm() {
  validateMultiselect()

  if(isMultiselectValid.value){
    try {
      const payload = {
        uuid: projectId.value,
        member_ids: newMembers.value,
      }

      await projectStore.updateMember(payload)

      isAddMemberDialogueOpen.value = false
      toast.success('Member added successfully', { timeout: 1000 })

      await getByProjects()
      await fetchProject()
      await setSelectedMembers()
    } catch (error) {
      console.error('Error adding member:', error)
      toast.error('Failed to add member:', error.message || error)
    }
  }
}

const fetchMembers = async () => {
  try {
    await userStore.getMembersForDropDown()
    isLoading.value = true
  } catch (error) {
    toast.error('Error fetching members:', error)
  }
  finally {
    isLoading.value = false
  }
}

const fetchProject = async () => {
  try {
    await projectStore.show(projectId.value)
    isLoading.value = true
  } catch (error) {
    toast.error('Error fetching project:', error)
  }
  finally {
    isLoading.value = false
  }
}

const setSelectedMembers = async () => {
  newMembers.value = null
}

const getByProjects = async () => {
  try {
    await userStore.getByProjects(options.value.page, options.value.itemsPerPage, searchName.value, searchEmail.value, selectedRole.value, projectId.value)
  } catch (error) {
    toast.error('Error fetching members:', error)
  }
}

const deleteMember = async member => {
  try {
    const confirmDelete = await Swal.fire({
      title: "Are you sure?",
      html: `<small>Do you want to remove <b>${member.name_first + ' ' + member.name_last}</b> from <b>${project.value?.title}</b>?</small>`,
      icon: "warning",
      showCancelButton: true,
      customClass: {
        confirmButton: 'v-btn custom-btn-style rounded-pill px-4',
        cancelButton: 'v-btn custom-secondary-btn rounded-pill px-4',
      },
      confirmButtonText: "Yes, remove it!",
      didOpen: () => {
        document.querySelector('.swal2-confirm').blur()
      },
    })

    if (confirmDelete.isConfirmed) {
      await projectStore.deleteMember(projectId.value, member.uuid)
      toast.success('Member deleted successfully', { timeout: 1000 })
      await getByProjects()
      await fetchProject()
      await setSelectedMembers()

    }
  } catch (error) {
    toast.error('Error deleting member:', error)
  }
}

const rolesWithFirstOption = (firstOption = null) => {
  let roles = [...roleStore.getRoles]
  if (firstOption) {
    roles.unshift({ id: null, name: firstOption })
  }

  return roles
}

const handlePageChange = async page => {
  options.value.page = page
  await getByProjects()
}

const onFilter = async value => {
  selectedRole.value = value
  options.value.page = 1
  await getByProjects()
}

const applyFilters = async (name = '', email = null, roleId = null) => {
  searchName.value = name
  searchEmail.value = email
  selectedRole.value = roleId
  options.value.page = 1
  await getByProjects()
}

const getMembers = computed(() => {
  const members = userStore.getMembersList
  const usersByProjects = userStore.getUsersByProjects

  const excludedIds = usersByProjects.map(user => user.id)
  const isClientAdded = usersByProjects.some(user => user.role == 'Client User')
  const isManagerAdded = usersByProjects.some(user => user.role == 'Project Manager')
  
  const remainingMembers = members.filter(member => {
    if ((member.role == 'Client User' && isClientAdded) || (member.role == 'Project Manager' && isManagerAdded)) {
      return false
    }

    return !selectedMembers.value.includes(member.value) && !excludedIds.includes(member.id)
  })

  return remainingMembers.map(member => ({
    id: member.id,
    name: `${member.name_first} ${member.name_last} (${member.role})`,
    shortName: `${member.name_first} ${member.name_last}`,
    avatar: member?.info?.avatar,
  }))
})

const project = computed(() =>{
  return projectStore.getProject
})

const getUsersByProjects = computed(() => {
  return userStore.getUsersByProjects
})

const totalUsers = computed(() => {
  return userStore.usersByProjectCount
})

const getErrors = computed(() => {
  return userStore.getErrors
})

const getUserLoadStatus = computed(() => {
  return userStore.getLoadStatus
})

const getProjectLoadStatus = computed(() => {
  return projectStore.getLoadStatus
})

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

const useMagnifierIcon  = computed(() => {
  return authStore.generalSetting?.is_magnifier_icon
})

watch([project, isAddMemberDialogueOpen], ([newProject, newDialogueState]) => {
  if (newProject) {
    useHead({ title: `${layoutConfig.app.title} | ${newProject?.title} - Team` })
  }

  if (newDialogueState) {
    nextTick(() => {
      const inputEl = focusInput.value?.$el?.querySelector('input')
      if (inputEl) {
        inputEl.focus()
      }
    })
  }
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
.side-flick-name{
    position: absolute;
    left: 27%;
    top: 5%;
}
.side-flick-role{
    position: absolute;
    left: 27%;
}

.align-center-important {
  align-items: center !important;
}

.delete-icon {
  position: absolute;
  top: 0;
  right: 0;
  padding: 10px;
  background-color: rgba(255, 255, 255, 0.5);
  border-radius: 0 0 0 10px;
  cursor: pointer;
}

.delete-icon:hover {
  background-color: rgba(255, 255, 255, 0.8);
}

.custom-pagination :deep(hr) {
  display: none !important;
}
</style>

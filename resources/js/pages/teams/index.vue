<template>
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
          v-model="selectedRole"
          placeholder="Select Type"
          :items="rolesWithFirstOption('All Members')"
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
          @click="isAddMemberDrawerOpen = !isAddMemberDrawerOpen"
        >
          New Member
        </VBtn>
      </div>
    </VCol>
  </VRow>
  <VRow class="mt-0 pt-0">
    <VCol 
      cols="12"
      class="pt-0 ps-4"
    >
      <h3>Manage Members</h3>
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
    <VRow v-if="getUsers.length === 0">
      <VCol cols="12">
        <VCard class="px-3 py-3 text-center">
          <span>No members found</span>
        </VCard>
      </VCol>
    </VRow>
    <VRow
      v-else-if="viewType === 'list'"
      class="mb-4"
    >
      <VCol
        v-for="user in getUsers"
        :key="user.id"
        cols="12"
      >
        <VCard class="d-flex align-center ps-4 py-1">
          <VCol cols="4">
            <div class="d-flex align-center gap-x-3">
              <VAvatar
                size="34"
                :class="user.avatar ? '' : 'text-white bg-primary'"
                :variant="!user.avatar ? 'tonal' : ''"
              >
                <span>{{ avatarText(user.name_first + ' ' + user.name_last) }}</span>
              </VAvatar>
              <div>
                <h6 class="text-h6 text-no-wrap">
                  <span class="d-block">{{ user.name_first }} {{ user.name_last }}</span>
                </h6>
                <small>{{ roleStore.capitalizeFirstLetter(user.role) }}</small>
              </div>
            </div>
          </VCol>
          <VCol cols="4">
            <div class="d-flex align-center">
              <div class="d-flex flex-column ms-3">
                <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ user.email }}</span>
                <small class="mt-0">{{ user?.info?.phone }}</small>
              </div>
            </div>
          </VCol>
          <VCol cols="3">
            <div class="d-flex flex-column ms-3">
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
          <VCol
            cols="1"
            class="ms-8"
          >
            <IconBtn
              v-if="user.role !== 'Admin'"
              @click.prevent
            >
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

    <!-- Grid View -->
    <VRow v-else>
      <VCol
        v-for="user in getUsers"
        :key="user.id"
        cols="12"
        md="4"
      >
        <VCard class="pt-2">
          <VCardTitle>
            <VRow>
              <VCol cols="10">
                <div class="d-flex align-center gap-x-3">
                  <VAvatar
                    size="36"
                    :class="user.avatar ? '' : 'text-white bg-primary'"
                    :variant="!user.avatar ? 'tonal' : ''"
                  >
                    <span>{{ avatarText(user.name_first + ' ' + user.name_last) }}</span>
                  </VAvatar>
                  <div>
                    <h6 class="text-h6 text-no-wrap">
                      {{ user.name_first }} {{ user.name_last }}
                    </h6>
                  </div>
                </div>
              </VCol>
              <VCol cols="2">
                <IconBtn 
                  v-if="user.role !== 'Admin'"
                  @click.prevent
                >
                  <VIcon icon="tabler-dots-vertical" />
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
            </VRow>
          </VCardTitle>
          <VCardText class="px-3 pt-2">
            <VRow class="d-flex align-center">
              <VCol cols="8">
                <div class="d-flex align-center mt-1 ms-1">
                  <div class="d-flex flex-column ms-3">
                    <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ user.email }}</span>
                    <small class="mt-0">{{ roleStore.capitalizeFirstLetter(user.role) }}</small>
                  </div>
                </div>
              </VCol>
              <VCol cols="4">
                <div class="d-flex align-end justify-end">
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
            </VRow>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>

  <TablePagination
    v-if="!isLoading && getUsers.length > 0"
    v-model:page="options.page"
    :items-per-page="options.itemsPerPage"
    :total-items="totalUsers"
    class="custom-pagination"
    @update:page="handlePageChange"
  />
  <AddMemberDrawer
    v-if="isAddMemberDrawerOpen"

    v-model:is-drawer-open="isAddMemberDrawerOpen"
    :fetch-members="fetchMembers"
    :get-roles="getRoles"
    :get-errors="getErrors"
    :get-status-code="getStatusCode"
    :get-load-status="getLoadStatus"
  />
  <EditMemberDrawer
    v-if="isEditMemberDrawerOpen"
    v-model:is-edit-drawer-open="isEditMemberDrawerOpen"
    :fetch-members="fetchMembers"
    :get-roles="getRoles"
    :get-errors="getErrors"
    :get-status-code="getStatusCode"
    :edit-member-details="editMemberDetails"
    :get-load-status="getLoadStatus"
  />
  <FilterDrawer
    v-if="isFilterDrawerOpen"
    v-model:is-filter-drawer-open="isFilterDrawerOpen"
    :apply-filters="applyFilters"
    :get-roles="rolesWithFirstOption('All Members')"
    :selected-role="selectedRole"
    :get-load-status="getLoadStatus"
  />
</template>

<script setup>
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'
import AddMemberDrawer from '@/pages/teams/_partials/add-member-drawer.vue'
import EditMemberDrawer from '@/pages/teams/_partials/update-member-drawer.vue'
import ListViewSkeleton from '@/pages/teams/_partials/list-view-skeleton.vue'
import GridViewSkeleton from '@/pages/teams/_partials/grid-view-skeleton.vue'
import FilterDrawer from '@/pages/teams/_partials/filter-projects-drawer.vue'
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useRoleStore } from "../../store/roles"
import { useUserStore } from "../../store/users"

useHead({ title: `${layoutConfig.app.title} | Manage Members` })

const router = useRouter()
const toast = useToast()
const roleStore = useRoleStore()
const userStore = useUserStore()

const editMemberDetails = ref({})
const isAddMemberDrawerOpen = ref(false)
const isEditMemberDrawerOpen = ref(false)
const isFilterDrawerOpen = ref(false)
const selectedRole = ref(null)
const viewType = ref('list')
const isLoading = ref(false)
const searchName = ref('')
const searchEmail = ref('')
const options = ref({ page: 1, itemsPerPage: 10, orderBy: '', order: '' })

onBeforeMount(async () => {
  const searchParams = new URLSearchParams(window.location.search)
  const savedViewType = searchParams.get('view')
  if (savedViewType && ['list', 'grid'].includes(savedViewType)) {
    viewType.value = savedViewType
  }
  await fetchRoles()
  await fetchMembers()
})

const fetchMembers = async () => {
  try {
    await userStore.getAll(options.value.page, options.value.itemsPerPage, searchName.value, searchEmail.value, selectedRole.value)
    isLoading.value = true
  } catch (error) {
    toast.error('Error fetching members:', error)
  }
  finally {
    isLoading.value = false
  }
}

const applyFilters = async (name = '', email = null, roleId = null) => {
  searchName.value = name
  searchEmail.value = email
  selectedRole.value = roleId
  options.value.page = 1
  await fetchMembers()
}

const editMember = member => {
  const { role, ...rest } = member

  editMemberDetails.value = { ...rest, role: role }
  isEditMemberDrawerOpen.value = true
}

const deleteMember = async member => {
  try {
    const confirmDelete = await Swal.fire({
      title: "Are you sure?",
      text: `Do you want to delete ${member.name_first} ${member.name_last}?`,
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

const getStatusColor = role => {
  const colors = {
    'active': 'success',
    'inactive': 'error',
  }

  return colors[role] ?? 'warning'
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
  await fetchMembers()
}

const onFilter = async value => {
  selectedRole.value = value
  options.value.page = 1
  await fetchMembers()
}

const getRoles = computed(() => {
  return roleStore.getRoles
})

const getUsers = computed(() => {
  return userStore.getUsers
})

const getErrors = computed(() => {
  return userStore.getErrors
})

const totalUsers = computed(() => {
  return userStore.usersCount
})

const getStatusCode = computed(() => {
  return userStore.getStatusCode
})

const getLoadStatus = computed(() => {
  return userStore.getLoadStatus
})

watch([viewType], ([newViewType]) => {
  router.push({
    query: {
      view: newViewType,
    },
  })
})
</script>

<style scoped>
.d-toggle{
    border: unset !important;
    padding: 0 !important;
    align-items: unset !important;
    block-size: unset !important;
}

.table-wrapper {
    inline-size: auto;
    overflow-x: auto;
}

.user-state-chip {
  width: fit-content;
}

.custom-pagination :deep(hr) {
  display: none !important;
}
</style>

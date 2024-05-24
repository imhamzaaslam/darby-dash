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
      <div class="d-flex justify-end mb-5">
        <VBtn
          prepend-icon="tabler-plus"
          @click="isAddMemberDrawerOpen = !isAddMemberDrawerOpen"
        >
          New Member
        </VBtn>
      </div>
    </VCol>
  </VRow>
  <VCard>
    <VCardText class="d-flex justify-space-between align-center flex-wrap gap-4">
      <h3>
        Manage Members
      </h3>
      <div style="inline-size: 272px;">
        <AppTextField
          v-model="search"
          placeholder="Search Member"
          @input="onFilter($event.target.value)"
        />
      </div>
    </VCardText>

    <VDivider />
    <VDataTable
      :headers="headers"
      :items-per-page="options.itemsPerPage"
      :items="getUsers"
      item-value="name"
      hide-default-footer
      class="text-no-wrap"
      @update:options="updateOptions"
    >
      <template #item.name="{ item }">
        <div class="d-flex align-center">
          <VAvatar
            size="36"
            :color="item.avatar ? '' : generateRandomColor()"
            :class="item.avatar ? '' : 'v-avatar-light-bg info--text'"
            :variant="!item.avatar ? 'tonal' : generateRandomColor()"
          >
            <span>{{ avatarText(item.name_first + ' ' + item.name_last) }}</span>
          </VAvatar>
          <div class="d-flex flex-column ms-3">
            <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ item.name_first }} {{ item.name_last }}</span>
            <small class="mt-0 text-xs">
              {{ roleStore.capitalizeFirstLetter(item.role) }}
            </small>
          </div>
        </div>
      </template>
      <template #item.email="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.email }}</span>
        </div>
      </template>
      <template #item.state="{ item }">
        <div class="d-flex gap-1">
          <VChip
            :color="getStatusColor(item.state)"
            variant="outlined"
            size="small"
          >
            {{ roleStore.capitalizeFirstLetter(item.state) }}
          </VChip>
        </div>
      </template>
      <template #item.created_at="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.created_at }}</span>
        </div>
      </template>

      <template #item.actions="{ item }">
        <div class="d-flex">
          <template v-if="item.email !== 'eric@darby.com'">  
            <IconBtn @click="editMember(item)">
              <VIcon
                icon="tabler-edit"
                color="info"
              />
            </IconBtn>
            <IconBtn @click="deleteMember(item)">
              <VIcon
                icon="tabler-trash"
                color="error"
              />
            </IconBtn>
          </template>
        </div>
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
          :total-items="totalUsers"
          @update:page="handlePageChange"
        />
      </template>
    </VDataTable>
  </VCard>
  <AddMemberDrawer
    v-model:is-drawer-open="isAddMemberDrawerOpen"
    :fetch-members="fetchMembers"
    :get-roles="getRoles"
    :get-errors="getErrors"
    :get-status-code="getStatusCode"
    :get-load-status="getLoadStatus"
  />
  <EditMemberDrawer
    v-model:is-edit-drawer-open="isEditMemberDrawerOpen"
    :fetch-members="fetchMembers"
    :get-roles="getRoles"
    :get-errors="getErrors"
    :get-status-code="getStatusCode"
    :edit-member-details="editMemberDetails"
    :get-load-status="getLoadStatus"
  />
</template>

<script setup>
import Swal from 'sweetalert2'
import AddMemberDrawer from '@/pages/teams/_partials/add-member-drawer.vue'
import EditMemberDrawer from '@/pages/teams/_partials/update-member-drawer.vue'
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useRoleStore } from "../../store/roles"
import { useUserStore } from "../../store/users"

const toast = useToast()
const roleStore = useRoleStore()
const userStore = useUserStore()

const editMemberDetails = ref({})

const isAddMemberDrawerOpen = ref(false)
const isEditMemberDrawerOpen = ref(false)
const isLoading = ref(false)
const search = ref('')
const options = ref({ page: 1, itemsPerPage: 10, orderBy: '', order: '' })

const headers = [
  {
    title: 'Name',
    key: 'name',
    sortable: true,
  },
  {
    title: 'Email',
    key: 'email',
    sortable: false,
  },
  {
    title: 'Status',
    key: 'state',
    sortable: true,
    width: '15%',
  },
  {
    title: 'Created At',
    key: 'created_at',
    sortable: true,
    width: '20%',
  },
  {
    title: 'Actions',
    key: 'actions',
    sortable: false,
    width: '5%',
  },
]

onBeforeMount(async () => {
  await fetchRoles()
  await fetchMembers()
})

const fetchMembers = async () => {
  try {
    if(getLoadStatus.value === 1) return

    await userStore.getAll(options.value.page, options.value.itemsPerPage, search.value, options.value.orderBy, options.value.order)
    isLoading.value = true
  } catch (error) {
    toast.error('Error fetching members:', error)
  }
  finally {
    isLoading.value = false
  }
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

const generateRandomColor = () => '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(-50, '0')

const handlePageChange = async page => {
  options.value.page = page
  await fetchMembers()
}

const onFilter = async value => {
  options.value.page = 1
  search.value = value
  await fetchMembers()
}

const updateOptions = async updateOptions => {
  console.log('updateOptions calling')
  options.value.orderBy = updateOptions.sortBy[0]?.key,
  options.value.order = updateOptions.sortBy[0]?.order

  await fetchMembers()
}
</script>

<style scoped>
.table-wrapper {
    inline-size: auto;
    overflow-x: auto;
}
</style>

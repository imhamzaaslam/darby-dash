<template>
  <Loader v-if="isRoleFetching" />
  <VRow class="pb-0">
    <VCol cols="12">
      <div class="d-flex align-center">
        <VAvatar
          icon="tabler-settings"
          size="36"
          class="me-2"
          color="primary"
          variant="tonal"
        />
        <h3 class="text-primary">
          Manage Roles & Permissions
        </h3>
      </div>
      <p class="text-body-1 text-muted mt-1">
        Effortlessly manage {{ userDetails?.company }}'s roles, permissions, user details, and settings to optimize team workflows.
      </p>
    </VCol>
  </VRow>
  <VRow>
    <!-- 👉 Roles -->
    <VCol
      v-for="item in roles"
      :key="item.role"
      cols="12"
      sm="6"
      lg="4"
    >
      <VCard>
        <VCardText>
          <div class="d-flex justify-space-between align-center">
            <h5 class="text-h6">
              <VIcon
                class=""
                color="primary"
                size="large"
                icon="tabler-shield-check"
              />
              {{ item.name }}
            </h5>
            <VChip
              color="primary"
              class="text-capitalize"
              variant="outlined"
              size="small"
              @click="openUserList(item.name)"
            >
              {{ item.total_users }} users
            </VChip>
          </div>
          <div class="d-flex align-center">
            <VBtn
              color="primary"
              size="small"
              class="mt-8"
              rounded="pill"
              @click="editPermission(item)"
            >
              <VIcon icon="tabler-pencil" />
              Manage Permissions
            </VBtn>
          </div>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>

  <AddEditRoleDialog
    v-if="isRoleDialogVisible"
    v-model:is-dialog-visible="isRoleDialogVisible"
    :role-id="selectedRoleId"
    :role-name="selectedRoleName"
  />
  <UserListDialog
    v-if="isUserListDialogVisible"
    v-model:is-dialog-visible="isUserListDialogVisible"
    :users-list="usersList"
    :role-name="selectedRoleName"
    :role-users-length="selectedRoleLength"
  />
</template>

<script setup>
import { useRoleStore } from "@/store/roles"
import { useUserStore } from "@/store/users"
import Loader from "@/components/Loader.vue"
import UserListDialog from "@/pages/roles/_partials/UserListDialogue.vue"

const roleStore = useRoleStore()
const userStore = useUserStore()

const isRoleFetching = ref(false)

onMounted(async () => {
  await fetchRoles()
  await fetchMembers()
})

const fetchRoles = async () => {
  try {
    isRoleFetching.value = true
    await roleStore.getAll()
  } catch (error) {
    console.error(error)
  } finally {
    isRoleFetching.value = false
  }
}

const fetchMembers = async () => {
  try {
    await userStore.getMembers()
  } catch (error) {
    toast.error('Error fetching members:', error)
  }
}

const roles = computed(() => {
  return roleStore.getRolesFullData
})

const members = computed(() => {
  return userStore.getMembersList
})

const isRoleDialogVisible = ref(null)
const isUserListDialogVisible = ref(false)
const selectedRoleId = ref()
const selectedRoleName = ref()
const selectedRoleLength = ref(0)
const usersList = ref({})

const editPermission = value => {
  isRoleDialogVisible.value = true
  selectedRoleId.value = value.id
  selectedRoleName.value = value.name
}

const openUserList = roleName => {
  const users = members.value.filter(member =>
    member.role == roleName,
  )

  selectedRoleName.value = roleName
  selectedRoleLength.value = users.length
  usersList.value = { ...users }

  isUserListDialogVisible.value = true
}

const userDetails = computed(() => {
  return userStore.getUser
})
</script>

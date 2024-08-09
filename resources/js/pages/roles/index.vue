<template>
  <VRow class="mt-0 pt-0">
    <VCol 
      cols="12"
      class="pt-0 ps-4"
    >
      <h3>Manage Roles & Permissions</h3>
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
</template>

<script setup>
import { useRoleStore } from "@/store/roles"

const roleStore = useRoleStore()

onMounted(async () => {
  await fetchRoles()
})

const fetchRoles = async () => {
  await roleStore.getAll()
}

// use computed to get all role data
const roles = computed(() => {
  return roleStore.getRolesFullData
})

const isRoleDialogVisible = ref(null)
const selectedRoleId = ref()
const selectedRoleName = ref()

const editPermission = value => {
  isRoleDialogVisible.value = true
  selectedRoleId.value = value.id
  selectedRoleName.value = value.name
}
</script>
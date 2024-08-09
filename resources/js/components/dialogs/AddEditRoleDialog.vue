<script setup>
import { VForm } from 'vuetify/components/VForm'
import { useRoleStore } from "@/store/roles"
import { ref, onMounted, watch, computed } from 'vue'
import { useToast } from "vue-toastification"

const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
  roleId: {
    type: Number,
    required: true,
  },
  roleName: {
    type: String,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDialogVisible',
])

const toast = useToast()
const roleStore = useRoleStore()

const permissions = ref([])
const isSelectAll = ref(false)
const isIndeterminate = ref(false)
const role = ref(props.roleName)
const isFetchingPermissions = ref(false)
const refPermissionForm = ref()

onMounted(async () => {
  await fetchRolePermissions()
})

const fetchRolePermissions = async () => {
  try {
    isFetchingPermissions.value = true
    await roleStore.getPermissions(props.roleId)
  } catch (error) {
    toast.error('Failed to fetch role permissions')
  } finally {
    isFetchingPermissions.value = false
  }
}

const updateAllPermissions = () => {
  permissions.value.forEach(permission => {
    permission.create = isSelectAll.value
    permission.view = isSelectAll.value
    permission.edit = isSelectAll.value
    permission.delete = isSelectAll.value
  })
}

const onSubmit = async () => {
  const payload = permissions.value.map(permission => {
    return {
      name: permission.name,
      create: permission.create,
      view: permission.view,
      edit: permission.edit,
      delete: permission.delete,
    }
  })

  try {
    await roleStore.updateRolePermissions(props.roleId, payload)
    toast.success('Role permissions updated successfully')
    emit('update:isDialogVisible', false)
  } catch (error) {
    toast.error('Failed to update role permissions')
  }
}

const onReset = () => {
  emit('update:isDialogVisible', false)
  refPermissionForm.value?.reset()
}

const checkedCount = computed(() => {
  let counter = 0
  permissions.value.forEach(permission => {
    Object.entries(permission).forEach(([key, value]) => {
      if (key !== 'name' && value)
        counter++
    })
  })

  return counter
})

const loadStatus = computed(() => {
  return roleStore.getLoadStatus
})

watch(() => roleStore.getRolePermissions, newPermissions => {
  permissions.value = newPermissions
}, { immediate: true })

watch(() => checkedCount.value, count => {
  const totalPermissions = permissions.value.length * 4

  isIndeterminate.value = count > 0 && count < totalPermissions
  isSelectAll.value = count === totalPermissions
})
</script>


<template>
  <VDialog
    :width="$vuetify.display.smAndDown ? 'auto' : 900"
    :model-value="props.isDialogVisible"
    @update:model-value="onReset"
  >
    <!-- 👉 Dialog close btn -->
    <DialogCloseBtn @click="onReset" />

    <VCard class="pa-sm-10 pa-2">
      <VCardText>
        <h4 class="text-h4 text-center mb-2">
          Set Role Permissions
        </h4>

        <!-- 👉 Form -->
        <VForm ref="refPermissionForm">
          <!-- 👉 Role name -->
          <AppTextField
            v-model="role"
            label="Role Name"
            placeholder="Enter Role Name"
            disabled
          />

          <!-- 👉 Role Permissions -->
          <div 
            v-if="isFetchingPermissions"
            class="mt-6 mb-6 d-flex justify-center"
          >
            <VProgressCircular
              color="primary"
              indeterminate
            />
          </div>
          <VTable
            v-else
            class="permission-table text-no-wrap mb-6 mt-6"
          >
            <!-- 👉 Admin  -->
            <tr>
              <td>
                <h6 class="text-h6">
                  Administrator Access
                </h6>
              </td>
              <td colspan="4">
                <div class="d-flex justify-end">
                  <VCheckbox
                    v-model="isSelectAll"
                    :indeterminate="isIndeterminate"
                    label="Select All"
                    @change="updateAllPermissions"
                  />
                </div>
              </td>
            </tr>

            <!-- 👉 Other permission loop -->
            <template
              v-for="permission in permissions"
              :key="permission.name"
            >
              <tr>
                <td>
                  <h6 class="text-h6">
                    {{ permission.name }}
                  </h6>
                </td>
                <td>
                  <div class="d-flex justify-end">
                    <VCheckbox
                      v-model="permission.create"
                      label="Create"
                    />
                  </div>
                </td>
                <td>
                  <div class="d-flex justify-end">
                    <VCheckbox
                      v-model="permission.view"
                      label="View"
                    />
                  </div>
                </td>
                <td>
                  <div class="d-flex justify-end">
                    <VCheckbox
                      v-model="permission.edit"
                      label="Edit"
                    />
                  </div>
                </td>
                <td>
                  <div class="d-flex justify-end">
                    <VCheckbox
                      v-model="permission.delete"
                      label="Delete"
                    />
                  </div>
                </td>
              </tr>
            </template>
          </VTable>

          <!-- 👉 Actions button -->
          <div class="d-flex align-center justify-center gap-4">
            <VBtn
              :disabled="loadStatus === 1"
              @click="onSubmit"
            >
              <span v-if="loadStatus === 1 && !isFetchingPermissions">
                <VProgressCircular
                  :size="16"
                  width="3"
                  indeterminate
                />
                Loading...
              </span>
              <span v-else>
                Submit
              </span>
            </VBtn>
            <VBtn
              color="secondary"
              variant="tonal"
              @click="onReset"
            >
              Cancel
            </VBtn>
          </div>
        </VForm>
      </VCardText>
    </VCard>
  </VDialog>
</template>


<style lang="scss">
.permission-table {
  td {
    border-block-end: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    padding-block: 0.5rem;

    .v-checkbox {
      min-inline-size: 4.75rem;
    }

    &:not(:first-child) {
      padding-inline: 0.5rem;
    }

    .v-label {
      white-space: nowrap;
    }
  }
}
</style>

<!-- eslint-disable vue/no-template-shadow -->
<template>
  <VDialog
    :model-value="props.isSortServiceModalOpen"
    :width="$vuetify.display.smAndDown ? 'auto' : 600"
    persistent
    @update:model-value="dialogVisibleUpdate"
  >
    <!-- Dialog close button -->
    <DialogCloseBtn @click="$emit('update:isSortServiceModalOpen', false)" />

    <VCard class="sort-dialog-card">
      <VCardTitle class="d-flex justify-between align-center mb-0 text-h5">
        <span>
          <VIcon
            icon="tabler-checklist"
            class="me-1"
            color="primary"
          />
          Manage Services ({{ props.getServicesWithoutPagination ? props.getServicesWithoutPagination.length : 0 }})
        </span>
      </VCardTitle>

      <VCardText class="scrollable-content">
        <VueDraggableNext
          :list="props.getServicesWithoutPagination"
          item-key="id"
          class="drag-container"
        >
          <VRow
            v-for="(service, index) in props.getServicesWithoutPagination"
            :key="service.id"
            class="sort-item"
            :class="[{ 'no-hover': editingServiceId === service.id }]"
          >
            <!-- Left side: drag icon and list name -->
            <VCol
              cols="12"
              class="d-flex align-items-center py-0"
              @mouseenter="hoveredServiceId = service.id"
              @mouseleave="hoveredServiceId = null"
            >
              <VIcon
                icon="tabler-grip-vertical"
                class="drag-handle"
                color="grey darken-1"
              />
              <template v-if="editingServiceId === service.id">
                <VTextField
                  v-model="service.editingName"
                  hide-details
                  autofocus
                  density="compact"
                  class="ms-2"
                  dense
                  @keydown.enter="saveServiceName(service, index)"
                />
                <div>
                  <VBtn
                    color="success"
                    class="ms-2"
                    icon
                    size="x-small"
                    @click.stop="saveServiceName(service, index)"
                  >
                    <VIcon
                      size="small"
                      icon="tabler-check"
                    />
                  </VBtn>
                  <VTooltip
                    activator="parent"
                    location="top"
                  >
                    <span class="text-xs">Save</span>
                  </VTooltip>
                </div>
                <div>
                  <VBtn
                    color="error"
                    class="ms-1"
                    icon
                    size="x-small"
                    @click.stop="cancelEdit(service)"
                  >
                    <VIcon
                      size="small"
                      icon="tabler-x"
                    />
                  </VBtn>
                  <VTooltip
                    activator="parent"
                    location="top"
                  >
                    <span class="text-xs">Cancel</span>
                  </VTooltip>
                </div>
              </template>
              <template v-else>
                <small class="list-text text-primary text-sm ms-2">{{ service.title }} <small class="text-xs text-secondary font-weight-bold">({{ service.service_type }})</small></small>
                <VIcon
                  v-if="hoveredServiceId === service.id"
                  icon="tabler-edit"
                  class="ms-2 edit-icon"
                  size="small"
                  color="primary"
                  @click.stop="editService(service)"
                />
                <VIcon
                  v-if="hoveredServiceId === service.id && (getServicesWithoutPagination ? getServicesWithoutPagination.length > 1 : 0)"
                  icon="tabler-trash"
                  class="ms-1 edit-icon"
                  size="small"
                  color="primary"
                  @click.stop="deleteService(service)"
                />
              </template>
            </VCol>
          </VRow>
        </VueDraggableNext>
      </VCardText>
      <VCardText class="d-flex justify-end gap-3 flex-wrap px-3 mb-0">
        <VBtn
          color="secondary"
          @click="$emit('update:isSortServiceModalOpen', false)"
        >
          Cancel
        </VBtn>
        <VBtn
          :disabled="loadStatus === 1"
          @click="saveSortedOrder"
        >
          <VProgressCircular
            v-if="loadStatus === 1"
            indeterminate
            size="16"
            color="white"
          />
          <span v-if="loadStatus === 1">Processing...</span>
          <span v-else>Save</span>
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>
</template>

<script setup>
import { ref, computed } from 'vue'
import { VueDraggableNext } from 'vue-draggable-next'
import { useUserSettingStore } from '@/store/user_settings'
import { useToast } from 'vue-toastification'
import Swal from 'sweetalert2'

const props = defineProps({
  isSortServiceModalOpen: { type: Boolean, required: true },
  getServicesWithoutPagination: { type: Array, required: true },
  fetchServicesWithoutPagination: { type: Function },
  fetchServices: { type: Function },
  getLoadStatus: { type: Boolean, required: true },
})

const emit = defineEmits(['update:isSortServiceModalOpen'])

const hoveredServiceId = ref(null)
const editingServiceId = ref(null)

const dialogVisibleUpdate = async val => {
  emit('update:isSortServiceModalOpen', val)
}

const userSettingStore = useUserSettingStore()
const toast = useToast()

const editService = service => {
  editingServiceId.value = service.id
  service.editingName = service.title
}

const saveServiceName = async(service, index) => {
  if (!service.editingName.trim()) {
    toast.error('Service title cannot be empty.')

    return
  }

  service.title = service.editingName

  const editServiceDetails = {
    title: service.title,
  }

  await userSettingStore.updateService(service.uuid, editServiceDetails)
  await props.fetchServicesWithoutPagination()
  await props.fetchServices()
  editingServiceId.value = null
  toast.success('Service title updated!')
}

const cancelEdit = service => {
  editingServiceId.value = null
  service.editingName = service.name
}

const saveSortedOrder = async () => {
  try {

    const sortedServices = props.getServicesWithoutPagination.map((service, index) => ({
      id: service.id,
      order: index + 1,
    }))

    await userSettingStore.saveSortedOrder({ services: sortedServices })
    toast.success('Services sorted successfully!')
    await props.fetchServices()
    emit('update:isSortServiceModalOpen', false)
  } catch (error) {
    toast.error('Failed to save sorted order.')
  }
}

const deleteService = async service => {
  try {

    const confirmDelete = await Swal.fire({
      title: "Are you sure?",
      html: `
          <div>
          <p>
              Do you want to delete <strong>${service.title}</strong>?
              <br>
              <small>This action will also delete all associated tasks.</small>
          </p>
          </div>
        `,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "rgba(var(--v-theme-primary))",
      cancelButtonColor: "#808390",
      confirmButtonText: "Yes, delete it!",
      didOpen: () => {
        document.querySelector('.swal2-confirm').blur()
      },
    })

    if (confirmDelete.isConfirmed) {

      const res = await userSettingStore.deleteService(service.uuid)

      toast.success('Service deleted successfully', { timeout: 1000 })
      await props.fetchServicesWithoutPagination()
    }
  } catch (error) {
    toast.error('Failed to delete project service:', error)
  }
}

const loadStatus = computed(() => userSettingStore.getLoadStatus)
</script>

<style scoped>
.sort-dialog-card {
  border-radius: 12px;
  padding: 6px;
}

.scrollable-content {
  max-height: 400px;
  overflow-y: auto;
}

.sort-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 2px;
  border-bottom: 1px solid #f0f0f0;
  cursor: grab;
}

.sort-item:hover {
  background-color: rgba(var(--v-theme-td-hover));
}

.drag-handle {
  margin-right: 12px;
}

.sort-item:hover .drag-handle,
.sort-item:hover .list-text,
.sort-item:hover .progress-text,
.sort-item:hover .progress-complete-task-text {
  color: rgba(var(--v-theme-primary)) !important;
  font-weight: normal;
}

.no-hover:hover {
  background-color: transparent !important;
}

.no-hover:hover .drag-handle,
.no-hover:hover .list-text,
.no-hover:hover .progress-text,
.no-hover:hover .progress-complete-task-text {
  color: inherit !important;
  font-weight: normal;
}

.edit-icon {
  cursor: pointer;
  transition: color 0.3s;
}

.list-text {
  font-weight: 500;
}

.v-btn {
  text-transform: none;
  font-size: 16px;
}

.v-card-title {
  font-weight: bold;
  margin-bottom: 12px;
}
</style>


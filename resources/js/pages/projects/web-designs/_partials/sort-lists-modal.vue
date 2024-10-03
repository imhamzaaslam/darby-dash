<template>
  <VDialog
    :model-value="props.isSortListModalOpen"
    :width="$vuetify.display.smAndDown ? 'auto' : 600"
    persistent
    @update:model-value="dialogVisibleUpdate"
  >
    <!-- Dialog close button -->
    <DialogCloseBtn @click="$emit('update:isSortListModalOpen', false)" />

    <VCard class="sort-dialog-card">
      <VCardTitle class="text-h5">
        <span>
          <VIcon
            icon="tabler-align-box-left-top"
            class="me-2"
            color="primary"
          />
          Sort Lists ({{ props.getProjectLists ? props.getProjectLists.length : 0 }})
        </span>
      </VCardTitle>

      <VCardText class="px-4">
        <VueDraggableNext
          :list="props.getProjectLists"
          item-key="id"
          class="drag-container"
        >
          <VRow
            v-for="list in props.getProjectLists"
            :key="list.id"
            class="sort-item"
          >
            <!-- Left side: drag icon and list name -->
            <VCol
              cols="8"
              class="d-flex align-items-center py-0"
            >
              <VIcon
                icon="tabler-grip-vertical"
                class="drag-handle"
                color="grey darken-1"
              />
              <small class="list-text text-sm ms-2">{{ list.name }} ({{ list.total_tasks }})</small>
            </VCol>

            <!-- Right side: progress and tasks -->
            <VCol
              cols="4"
              class="d-flex justify-center align-items-center py-0"
            >
              <div class="d-flex justify-between align-center w-100">
                <div class="text-body-1 text-high-emphasis">
                  <small>{{ list.progress }}%</small>
                </div>
                <div class="flex-grow-1 mx-2">
                  <VProgressLinear
                    :height="6"
                    :model-value="list.progress"
                    color="primary"
                    rounded
                  />
                </div>
                <div class="text-body-1 text-high-emphasis">
                  <small>{{ list.completed_tasks }}</small>
                </div>
              </div>
            </VCol>
          </VRow>
        </VueDraggableNext>
      </VCardText>

      <VCardText class="d-flex justify-end gap-3 flex-wrap">
        <VBtn
          color="secondary"
          @click="$emit('update:isSortListModalOpen', false)"
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
import { computed } from 'vue'
import { VueDraggableNext } from 'vue-draggable-next'
import { useProjectListStore } from '@/store/project_lists'
import { useToast } from 'vue-toastification'

const props = defineProps({
  isSortListModalOpen: { type: Boolean, required: true },
  selectedProject: { type: String, required: true },
  getProjectLists: { type: Array, required: true },
})

const emit = defineEmits(['update:isSortListModalOpen'])

const dialogVisibleUpdate = val => {
  emit('update:isSortListModalOpen', val)
}

const projectListStore = useProjectListStore()
const toast = useToast()

const saveSortedOrder = async () => {
  try {
    const projectId = props.selectedProject

    const sortedLists = props.getProjectLists.map((list, index) => ({
      id: list.id,
      order: index + 1,
    }))

    await projectListStore.saveSortedOrder(projectId, sortedLists)
    toast.success('Lists sorted successfully!')
    emit('update:isSortListModalOpen', false)
  } catch (error) {
    toast.error('Failed to save sorted order.')
  }
}

const loadStatus = computed(() => projectListStore.getLoadStatus)
</script>

  <style scoped>
  .sort-dialog-card {
    border-radius: 12px;
    padding: 6px;
  }

  .sort-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 2px;
    border-bottom: 1px solid #f0f0f0;
    cursor: grab;
  }

  .sort-item:hover {
    background-color: #F0D9EB;
    border-radius: 4px;
  }

  .drag-handle {
    margin-right: 12px;
  }
  .sort-item:hover .drag-handle {
    color: #a12592 !important;
    font-weight: bold;
  }

  .sort-item:hover .list-text {
    color: #a12592 !important;
    font-weight: bold;
  }

  .list-text {
    font-weight: 500;
    color: #333;
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

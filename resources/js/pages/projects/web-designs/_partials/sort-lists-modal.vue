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
        <span><VIcon
          icon="tabler-align-box-left-top"
          class="me-2"
          color="primary"
        />Sort Lists ({{ props.getProjectLists ? props.getProjectLists.length : 0 }})</span>
      </VCardTitle>

      <VCardText class="px-4">
        <VueDraggableNext
          :list="props.getProjectLists"
          item-key="id"
          class="drag-container"
        >
          <VListItem
            v-for="list in props.getProjectLists"
            :key="list.id"
            class="sort-item"
          >
            <VIcon
              icon="tabler-grip-vertical"
              class="drag-handle"
              color="grey darken-1"
            />
            <span class="list-text">{{ list.name }}</span>
          </VListItem>
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
  isSortListModalOpen: {
    type: Boolean,
    required: true,
  },
  selectedProject: {
    type: String,
    required: true,
  },
  getProjectLists: {
    type: Array,
    required: true,
  },
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
    padding: 12px;
  }

  .drag-container {
    max-height: 800px;
    overflow-y: auto;
  }

  .sort-item {
    display: flex;
    align-items: center;
    padding: 6px 8px;
    border-bottom: 1px solid #f0f0f0;
  }

  .sort-item:hover {
    background-color: #f9f9f9;
  }

  .drag-handle {
    margin-right: 12px;
    cursor: grab;
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

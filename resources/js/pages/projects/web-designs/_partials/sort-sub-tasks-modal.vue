<!-- eslint-disable vue/no-template-shadow -->
<template>
  <VDialog
    :model-value="props.isSortSubTaskModalOpen"
    :width="$vuetify.display.smAndDown ? 'auto' : 600"
    persistent
    @update:model-value="dialogVisibleUpdate"
  >
    <!-- Dialog close button -->
    <DialogCloseBtn @click="$emit('update:isSortSubTaskModalOpen', false)" />
  
    <VCard class="sort-dialog-card">
      <VCardTitle class="d-flex justify-between align-center mb-0 text-h5">
        <span>
          <VIcon
            icon="tabler-align-box-left-top"
            class="me-1"
            color="primary"
          />
          Manage Subtasks ({{ props.getSubtasks ? props.getSubtasks.length : 0 }})
        </span>
        <!--
          <VTooltip location="top">
          <template #activator="{ props }">
          <VIcon
          v-bind="props"
          icon="tabler-circle-plus"
          class="cursor-pointer ms-2"
          color="primary"
          size="small"
          @click="toggleAddList"
          />
          </template>
          <span>Add List</span>
          </VTooltip> 
        -->
      </VCardTitle>
  
      <VCardText class="px-4">
        <VueDraggableNext
          :list="props.getSubtasks"
          item-key="id"
          class="drag-container"
        >
          <VRow
            v-for="task in props.getSubtasks"
            :key="task.id"
            class="sort-item"
          >
            <VCol
              cols="12"
              class="d-flex justify-left align-items-center py-0"
            >
              <VIcon
                icon="tabler-grip-vertical"
                class="drag-handle"
                color="grey darken-1"
              />
              <div class="text-body-1 text-high-emphasis">
                <small class="progress-text">{{ task.name }}</small>
              </div>
            </VCol>
          </VRow>
        </VueDraggableNext>
      </VCardText>
      <VCardText class="d-flex justify-end gap-3 flex-wrap px-3 mb-0">
        <VBtn
          color="secondary"
          @click="$emit('update:isSortSubTaskModalOpen', false)"
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
          <span v-else>Rearrange</span>
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>
</template>
  
<script setup>
import { ref, computed } from 'vue'
import { VueDraggableNext } from 'vue-draggable-next'
import { useProjectListStore } from '@/store/project_lists'
import { useProjectTaskStore } from '@/store/project_tasks'
import { useToast } from 'vue-toastification'
  
const props = defineProps({
  isSortSubTaskModalOpen: { type: Boolean, required: true },
  selectedProject: { type: String, required: true },
  selectedParentTask: { type: String, required: true },
  getSubtasks: { type: Array, required: true },
})
  
const emit = defineEmits(['update:isSortSubTaskModalOpen'])
  
const dialogVisibleUpdate = val => {
  emit('update:isSortSubTaskModalOpen', val)
}
  
const projectTaskStore = useProjectTaskStore()
const projectListStore = useProjectListStore()
const toast = useToast()
  
const fetchProjectLists = async () => {
  try {
    await projectListStore.getAll(props.selectedProject, {})
  } catch (error) {
    toast.error('Error fetching project lists:', error)
  }
}
  
const saveSortedOrder = async () => {
  try {
    const projectId = props.selectedProject
    const tasktId = props.selectedParentTask

    const sortedLists = props.getSubtasks.map((subtask, index) => ({
      id: subtask.id,
      order: index + 1,
    }))
  
    await projectTaskStore.updateSubTaskOrdering(projectId, tasktId, sortedLists)
    toast.success('Subtasks sorted successfully!')
    fetchProjectLists()
    emit('update:isSortSubTaskModalOpen', false)
  } catch (error) {
    toast.error('Failed to save sorted order.')
  }
}
  
const loadStatus = computed(() => projectTaskStore.getLoadStatus)
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
      padding: 12px 2px;
      border-bottom: 1px solid #f0f0f0;
      cursor: grab;
    }
  
    .sort-item:hover {
      background-color:  rgba(var(--v-theme-td-hover));
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
  
<!-- eslint-disable vue/no-template-shadow -->
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
      <VCardTitle class="d-flex justify-between align-center mb-0 text-h5">
        <span>
          <VIcon
            icon="tabler-align-box-left-top"
            class="me-1"
            color="primary"
          />
          Manage Lists ({{ props.getProjectAllLists ? props.getProjectAllLists.length : 0 }})
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

      <!-- Add new list section -->
      <VRow
        v-if="isAddingList"
        class="px-5 mt-4"
      >
        <VCol
          cols="12"
          class="d-flex align-center justify-between"
        >
          <!-- Text Field -->
          <AppTextField
            v-model="newListName"
            placeholder="List Name"
            density="compact"
            autofocus
            hide-details
            dense
            class="flex-grow-1"
            @keydown.enter="saveNewList"
          />

          <!-- Action Buttons (Icons) -->
          <div class="d-flex align-center ms-2">
            <div>
              <VBtn
                color="success"
                class="me-1 custom-btn-style"
                icon
                size="x-small"
                @click.stop="saveNewList"
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
                class="ms-1 error-btn-customer-style"
                icon
                size="x-small"
                @click.stop="cancelAddList"
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
          </div>
        </VCol>
      </VRow>
      <!--
        <VRow
        v-else
        class="px-1"
        >
        <VCol
        cols="12"
        class="py-0"
        >
        <VBtn
        color="primary"
        class="mt-2 float-right mb-2"
        size="small"
        @click.stop="toggleAddList"
        >
        <VIcon icon="tabler-plus" />
        <small>Add List</small>
        </VBtn>
        </VCol>
        </VRow> 
      -->

      <VCardText class="px-4">
        <VueDraggableNext
          :list="props.getProjectAllLists"
          item-key="id"
          class="drag-container"
        >
          <VRow
            v-for="(list, index) in props.getProjectAllLists"
            :key="list.id"
            class="sort-item"
            :class="[{ 'no-hover': editingListId === list.id }]"
          >
            <!-- Left side: drag icon and list name -->
            <VCol
              cols="8"
              class="d-flex align-items-center py-0"
              @mouseenter="hoveredListId = list.id"
              @mouseleave="hoveredListId = null"
            >
              <VIcon
                icon="tabler-grip-vertical"
                class="drag-handle"
                color="grey darken-1"
              />
              <template v-if="editingListId === list.id">
                <VTextField
                  v-model="list.editingName"
                  hide-details
                  autofocus
                  density="compact"
                  class="ms-2"
                  dense
                  @keydown.enter="saveListName(list, index)"
                />
                <div>
                  <VBtn
                    color="success"
                    class="ms-2"
                    icon
                    size="x-small"
                    @click.stop="saveListName(list, index)"
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
                    @click.stop="cancelEdit(list)"
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
                <small class="list-text text-sm ms-2">{{ list.name }} ({{ list.total_tasks }})</small>
                <VIcon
                  v-if="hoveredListId === list.id"
                  icon="tabler-edit"
                  class="ms-2 edit-icon"
                  size="small"
                  color="primary"
                  @click.stop="editList(list)"
                />
                <VIcon
                  v-if="hoveredListId === list.id && (getProjectAllLists? getProjectAllLists.length > 1 : 0)"
                  icon="tabler-trash"
                  class="ms-1 edit-icon"
                  size="small"
                  color="primary"
                  @click.stop="deleteProjectList(list)"
                />
              </template>
            </VCol>

            <!-- Right side: progress and tasks -->
            <VCol
              cols="4"
              class="d-flex justify-center align-items-center py-0"
            >
              <div class="d-flex justify-between align-center w-100">
                <div class="text-body-1 text-high-emphasis">
                  <small class="progress-text">{{ list.progress }}%</small>
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
                  <small class="progress-complete-task-text">{{ list.completed_tasks }}</small>
                </div>
              </div>
            </VCol>
          </VRow>
        </VueDraggableNext>
      </VCardText>
      <VCardText class="d-flex justify-end gap-3 flex-wrap px-3 mb-0">
        <VBtn
          color="secondary"
          class="custom-secondary-btn"
          @click="$emit('update:isSortListModalOpen', false)"
        >
          Cancel
        </VBtn>
        <VBtn
          :disabled="loadStatus === 1"
          class="custom-btn-style"
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
import { useToast } from 'vue-toastification'
import Swal from 'sweetalert2'

const props = defineProps({
  isSortListModalOpen: { type: Boolean, required: true },
  selectedProject: { type: String, required: true },
  getProjectAllLists: { type: Array, required: true },
})

const emit = defineEmits(['update:isSortListModalOpen'])

const hoveredListId = ref(null)
const editingListId = ref(null)
const isAddingList = ref(false)
const newListName = ref('')

const dialogVisibleUpdate = val => {
  emit('update:isSortListModalOpen', val)
}

const projectListStore = useProjectListStore()
const toast = useToast()

const fetchProjectLists = async () => {
  try {
    await projectListStore.getAll(props.selectedProject, {})
  } catch (error) {
    toast.error('Error fetching project lists:', error)
  }
}

const editList = list => {
  editingListId.value = list.id
  list.editingName = list.name
}

const saveListName = async(list, index) => {
  if (!list.editingName.trim()) {
    toast.error('List name cannot be empty.')

    return
  }

  list.name = list.editingName

  const editListDetails = {
    name: list.name,
    uuid: list.uuid,
    project_uuid: props.selectedProject,
  }

  await projectListStore.update(editListDetails)
  editingListId.value = null
  toast.success('List name updated!')
}

const cancelEdit = list => {
  editingListId.value = null
  list.editingName = list.name
}

const toggleAddList = () => {
  isAddingList.value = true
  newListName.value = ''
}

const saveNewList = async() => {
  if (!newListName.value.trim()) {
    toast.error('List name cannot be empty.')

    return
  }

  const newListDetails = {
    name: newListName.value.trim(),
    project_uuid: props.selectedProject,
  }

  await projectListStore.create(newListDetails)
  await fetchProjectLists()
  isAddingList.value = false
  toast.success('New list added!')
}

const cancelAddList = () => {
  isAddingList.value = false
}

const saveSortedOrder = async () => {
  try {
    const projectId = props.selectedProject

    const sortedLists = props.getProjectAllLists.map((list, index) => ({
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

const deleteProjectList = async list => {
  try {
    const listWithProjectId = { ...list, project_uuid: props.selectedProject }

    const confirmDelete = await Swal.fire({
      title: "Are you sure?",
      html: `
        <div>
        <p>
            Do you want to delete <strong>${list.name}</strong>?
            <br>
            <small>This action will also delete all associated tasks.</small>
        </p>
        </div>
      `,
      icon: "warning",
      showCancelButton: true,
      customClass: {
        confirmButton: 'v-btn custom-btn-style rounded-pill px-4',
        cancelButton: 'v-btn custom-secondary-btn rounded-pill px-4',
      },
      confirmButtonText: "Yes, delete it!",
      didOpen: () => {
        document.querySelector('.swal2-confirm').blur()
      },
    })

    if (confirmDelete.isConfirmed) {

      const res = await projectListStore.delete(listWithProjectId)

      toast.success('List deleted successfully', { timeout: 1000 })
      await fetchProjectLists()
    }
  } catch (error) {
    toast.error('Failed to delete project list:', error)
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

<template>
  <VContainer>
    <!-- Toggle -->
    <VRow>
      <VCol
        cols="12"
        md="6"
        class="d-flex"
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
      </VCol>
    </VRow>
    <VRow v-if="viewType === 'list'">
      <VCol cols="12">
        <AppTextField
          v-model="taskName"
          label="Add Task"
          clearable
          placeholder="Add Task"
          class="textfield-demo-icon-slot"
          @keydown.enter="addTask"
        >
          <!-- Append -->
          <template #append>
            <VBtn
              :icon="$vuetify.display.smAndDown"
              @click="addTask"
            >
              <VIcon
                icon="tabler-circle-plus"
                color="#fff"
                size="22"
              />
              <span
                v-if="$vuetify.display.mdAndUp"
                class="ms-1"
              >Add</span>
            </VBtn>
          </template>
        </AppTextField>
      </VCol>
      <VCol cols="12">
        <template v-if="isLoading">
          <VProgressLinear
            indeterminate
            color="primary"
          />
        </template>
        <template v-else>
          <VList v-if="getProjectTasks.length > 0">
            <VListItem
              v-for="(task, index) in getProjectTasks"
              :key="index"
              @click="startEditing(task)"
            >
              <VListItemContent>
                <VRow
                  align="center"
                  justify="space-between"
                >
                  <VCol cols="auto">
                    <!-- Drag Icon -->
                    <VIcon
                      class="tabler-brand-asana"
                      color="info"
                    />
                    <!-- Task Name -->
                    <span class="ms-2">{{ task.name.length > 60 ? task.name.substring(0, 60) + '...' : task.name }}</span>
                  </VCol>
                  <VCol cols="auto">
                    <VChip
                      color="secondary"
                      size="small"
                      class="ms-2"
                    >
                      {{ task.status }}
                    </VChip>
                    <VChip
                      v-if="task.due_date"
                      color="error"
                      size="small"
                      class="ms-2"
                    >
                      {{ formatDate(task.due_date) }}
                      <VTooltip
                        activator="parent"
                        location="top"
                      >
                        <span>Task is due on {{ formatDate(task.due_date) }}</span>
                      </VTooltip>
                    </VChip>
                    <VChip
                      color="info"
                      size="small"
                      class="ms-2"
                    >
                      {{ formatDate(task.created_at) }}
                      <VTooltip
                        activator="parent"
                        location="top"
                      >
                        <span>Created on {{ formatDate(task.created_at) }}</span>
                      </VTooltip>
                    </VChip>
                    <!--
                      <VChip
                      color="secondary"
                      size="small"
                      class="ms-2"
                      >
                      <VIcon class="tabler-message-circle" /> 8
                      </VChip>
                    -->
                    <!-- Actions Menu -->
                    <IconBtn class="ms-2">
                      <VIcon icon="tabler-dots-vertical" />
                      <VMenu activator="parent">
                        <VList>
                          <VListItem @click="startEditing(task)">
                            Edit
                          </VListItem>
                          <VListItem @click="deleteTask(task)">
                            Delete
                          </VListItem>
                        </VList>
                      </VMenu>
                    </IconBtn>
                  </VCol>
                </VRow>
              </VListItemContent>
            </VListItem>
          </VList>
          <div
            v-else
            class="text-center"
          >
            No tasks added yet.
          </div>
        </template>
      </VCol>
    </VRow>
    <VRow
      v-else
      class="kanban-scroll"
    >
      <VCol cols="12">
        <VRow
          class="kanban-columns"
          no-gutters
        >
          <VCol
            v-for="(column, index) in columns"
            :key="index"
            cols="4"
            class="kanban-column"
          >
            <VCard class="mb-3">
              <div
                class="kanban-column-header"
                @click="editTitle(index)"
              >
                <template v-if="!column.isEditing">
                  <span class="kanban-column-title">{{ column.title }}</span>
                </template>
                <template v-else>
                  <VTextField
                    ref="titleInput"
                    v-model="column.title"
                    dense
                    hide-details
                    single-line
                    autofocus
                    @blur="saveTitle(index)"
                  />
                </template>
              </div>
              <VDivider />
              <VueDraggableNext
                class="kanban-dropzone"
                :list="column.tasks"
                group="tasks"
                @change="onDrop(index, $event)"
              >
                <template
                  v-for="(task, taskIndex) in column.tasks"
                  :key="task.id"
                >
                  <VListItem
                    class="task-card"
                    :draggable="isDraggable"
                    @dragstart="onDragStart"
                    @click="editTask(index, taskIndex)"
                  >
                    <VListItemContent>
                      <VListItemTitle>{{ task.name }}</VListItemTitle>
                      <small v-html="task.description" />
                    </VListItemContent>
                  </VListItem>
                </template>
              </VueDraggableNext>
              <VDivider />
              <VCardActions class="justify-center">
                <VBtn
                  color="primary"
                  variant="plain"
                  @click="addNewTask(index)"
                >
                  Add Task
                </VBtn>
              </VCardActions>
            </VCard>
          </VCol>
          <!-- Add column button -->
          <VCol cols="4">
            <VCard class="mb-3">
              <VCardActions class="justify-center">
                <VBtn
                  color="primary"
                  variant="plain"
                  @click="addColumn"
                >
                  Add Column
                </VBtn>
              </VCardActions>
            </VCard>
          </VCol>
        </VRow>
      </VCol>
      <VDialog
        v-model="dialog"
        max-width="500"
      >
        <VCard>
          <VCardTitle>Edit Task</VCardTitle>
          <VCardText>
            <VTextField
              v-model="editedTask.title"
              label="Title"
              class="mb-4"
              autofocus
            />
            <VTextField
              v-model="editedTask.description"
              label="Description"
            />
          </VCardText>
          <VCardActions>
            <VBtn
              color="primary"
              @click="saveTask"
            >
              Save
            </VBtn>
            <VBtn
              color="error"
              @click="cancelEdit"
            >
              Cancel
            </VBtn>
          </VCardActions>
        </VCard>
      </VDialog>
    </VRow>
  </VContainer>
  <EditTaskDrawer
    v-model:is-edit-task-drawer-open="isEditTaskDrawerOpen"
    :fetch-project-tasks="fetchProjectTasks"
    :editing-task="editingTask"
    :get-load-status="getLoadStatus"
  />
</template>

<script setup="js">
import moment from 'moment'
import EditTaskDrawer from '@/pages/projects/web-designs/_partials/update-project-task-drawer.vue'
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useTaskStore } from "../../../../store/tasks"
import { useRouter } from 'vue-router'
import { VueDraggableNext } from 'vue-draggable-next'

const toast = useToast()
const taskStore = useTaskStore()
const router = useRouter()

const taskName = ref('')
const viewType = ref('list')
const editingTask = ref({})
const isEditTaskDrawerOpen = ref(false)

const isLoading = ref(false)

const projectId = computed(() => router.currentRoute.value.params.id)

const formatDate = date => moment(date).format('MMM DD, YYYY')

onBeforeMount(async () => {
  await fetchProjectTasks()
})

const fetchProjectTasks = async () => {
  try {
    isLoading.value = true
    await taskStore.getAll(projectId.value)
  } catch (error) {
    toast.error('Error fetching project tasks:', error)
  }
  finally {
    isLoading.value = false
  }
}

async function addTask() {
  if (taskName.value.trim() === '') {
    toast.error('Task name cannot be empty.')

    return
  }

  try {
    const newTaskDetails = {
      name: taskName.value.trim(),
      project_id: projectId.value,
    }

    await taskStore.create(newTaskDetails)

    taskName.value = ''
    toast.success('Task added successfully', { timeout: 1000 })
    fetchProjectTasks()
  } catch (error) {
    toast.error('Failed to add task:', error)
  }
}

function startEditing(task) {
  editingTask.value = { ...task }
  isEditTaskDrawerOpen.value = true
}

async function deleteTask(task) {
  try {
    task.isDeleting = true
    await taskStore.delete(task)
    toast.success('Task deleted successfully', { timeout: 1000 })
    task.isDeleting = false
    fetchProjectTasks()
  } catch (error) {
    toast.error('Failed to delete task:', error)
  }
}

const getProjectTasks = computed(() => taskStore.getProjectTasks)

const getLoadStatus = computed(() => {
  return taskStore.getLoadStatus
})

///TAKSKSKSKS
const isDraggable = true

const onDragStart = event => {
  event.dataTransfer.setData("text/plain", "This text may be dragged")
}

// Define the columns array with initial tasks
const columns = ref([
  { title: 'To Do', tasks: getProjectTasks, isEditing: false },
  { title: 'In Progress', tasks: [], isEditing: false },
  { title: 'Done', tasks: [], isEditing: false },
])

// Define the dialog state for editing tasks
const dialog = ref(false)

// Define the editedTask object for editing
const editedTask = ref({})

// Method to edit the column title
const editTitle = index => {
  columns.value[index].isEditing = true

  // Focus on the title input field when editing starts
  const titleInput = $refs.titleInput[index]

  titleInput && titleInput.focus()
}

// Method to save the edited column title
const saveTitle = index => {
  columns.value[index].isEditing = false
}

// Method to edit a task
const editTask = (columnIndex, taskIndex) => {
  // Copy the task details to editedTask
  editedTask.value = { columnIndex, taskIndex, ...columns.value[columnIndex].tasks[taskIndex] }

  // Open the dialog
  dialog.value = true
}

// Method to save the edited task
const saveTask = () => {
  // Extract column index and task index from editedTask
  const { columnIndex, taskIndex } = editedTask.value

  // Update the task in the columns array
  columns.value[columnIndex].tasks.splice(taskIndex, 1, editedTask.value)

  // Close the dialog
  dialog.value = false
}

// Method to cancel editing a task
const cancelEdit = () => {
  // Close the dialog
  dialog.value = false
}

// Method to add a new task
const addNewTask = columnIndex => {
  // Generate a unique ID for the new task
  const id = Date.now()

  // Create a new task object
  const newTask = { id, title: 'New Task', description: 'Description of New Task' }

  // Add the new task to the specified column
  columns.value[columnIndex].tasks.push(newTask)
}

// Method to add a new column
const addColumn = () => {
  // Generate a unique ID for the new column
  const id = Date.now()

  // Create a new column object
  const newColumn = { id, title: 'New Column', tasks: [] }

  // Add the new column to the columns array
  columns.value.push(newColumn)
}

// Method to handle drop event
const onDrop = (targetColumnIndex, event) => {
  // Retrieve the task being dragged
  const sourceColumnIndex = event.dragged.sourceIndex
  const sourceTaskIndex = event.dragged.sourceModel.index
  const taskToMove = columns.value[sourceColumnIndex].tasks.splice(sourceTaskIndex, 1)[0]

  // Move the task to the new column
  columns.value[targetColumnIndex].tasks.push(taskToMove)
}
</script>

<style scoped>
.task-row {
  border-bottom: 1px solid #ccc;
}
.d-toggle{
    border: unset !important;
    padding: 0 !important;
    align-items: unset !important;
    block-size: unset !important;
}
.kanban-scroll {
    overflow-x: auto;
    white-space: nowrap;
    margin-bottom: 20px;
  }

  .kanban-columns {
    flex-wrap: nowrap !important;
  }

  .kanban-column-header {
    padding: 10px;
    cursor: pointer;
    background-color: #f5f5f5;
  }

  .kanban-column-title {
    font-size: 18px;
    font-weight: bold;
  }

  .kanban-column {
    margin-right: 20px;
  }

  .v-card {
    margin-bottom: 20px;
  }

  .task-card {
    margin-bottom: 10px;
  }

  .v-list-item {
    cursor: pointer;
  }

  .v-list-item:hover {
    background-color: #f0f0f0;
  }
</style>

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
            icon="tabler-layout-cards"
            :class="{ 'bg-primary': viewType === 'grid' }"
            @click="viewType = 'grid'"
          />
        </VBtnToggle>
      </VCol>
      <VCol
        cols="12"
        md="6"
      >
        <div class="float-right">
          <VBtn
            v-if="!showAddTaskField && viewType === 'list'"
            color="primary"
            size="small"
            @click="activateQuickAdd"
          >
            <VIcon icon="tabler-plus" />
            Add Task
          </VBtn>
        </div>
      </VCol>
    </VRow>
    <VRow v-if="viewType === 'list'">
      <VCol cols="12">
        <div v-if="getProjectTasks.length > 0">
          <VCard
            v-for="(task, index) in getProjectTasks"
            :key="index"
            class="px-2 py-1 mt-2"
            @click="startEditing(task)"
          >
            <VRow>
              <VCol
                cols="6"
                class="mt-2"
              >
                <VIcon
                  class="tabler-playstation-circle"
                  color="primary"
                />
                <span class="ms-2">{{ task.name.length > 50 ? task.name.substring(0, 50) + '...' : task.name }}</span>
              </VCol>
              <VCol
                cols="5"
                class="mt-2"
              >
                <div class="float-right">
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
                    color="primary"
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
                </div>
              </VCol>
              <VCol cols="1">
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
          </VCard>
        </div>
        <div
          v-else
          class="text-center"
        >
          <div
            v-if="!showAddTaskField"
            class="mt-12"
            v-html="NoTaskInList"
          />
          <span v-if="!showAddTaskField">No tasks added yet.</span>
        </div>
        <VBtn
          v-if="!showAddTaskField && getProjectTasks.length > 0"
          color="primary"
          variant="plain"
          size="small"
          @click="activateQuickAdd"
        >
          <VIcon icon="tabler-plus" />
          Add Task
        </VBtn>
        <VCard
          v-if="showAddTaskField"
          class="px-2 py-2 mt-2"
        >
          <VRow>
            <VCol cols="8">
              <!-- Drag Icon -->
              <div class="d-flex align-center">
                <VIcon
                  class="tabler-playstation-circle me-2"
                  color="primary"
                />
                <VTextField
                  ref="quickTaskInput"
                  v-model="quickTaskName"
                  style="margin-top: -7px;"
                  variant="plain"
                  hide-details
                  dense
                  @keydown.enter="addQuickTask"
                />
              </div>
            </VCol>
            <VCol cols="4">
              <div class="float-right">
                <VIcon
                  class="tabler-calendar me-1"
                  color="primary"
                >
                  <VTooltip
                    activator="parent"
                    location="top"
                  >
                    <span>Select Due Date</span>
                  </VTooltip>
                  <AppDateTimePicker v-model="dueDate" />
                </VIcon>
                <VIcon
                  class="tabler-circle-check me-1"
                  color="primary"
                  @click="addQuickTask"
                >
                  <VTooltip
                    activator="parent"
                    location="top"
                  >
                    <span>Save</span>
                  </VTooltip>
                </VIcon>
                <VIcon
                  class="tabler-x"
                  color="primary"
                  @click="cancelQuickTask"
                >
                  <VTooltip
                    activator="parent"
                    location="top"
                  >
                    <span>Cancel</span>
                  </VTooltip>
                </VIcon>
              </div>
            </VCol>
          </VRow>
        </VCard>
      </VCol>
    </VRow>
    <!-- Kanban View -->
    <VRow
      v-else
      class="kanban-columns mt-8"
      no-gutters
    >
      <VCol
        v-for="(list, index) in lists"
        :key="index"
        cols="4"
        class="kanban-column"
      >
        <VCard class="mb-5">
          <VCardTitle class="d-flex justify-space-between bg-primary align-center">
            <div
              v-if="!list.isEditing"
              class="cursor-pointer"
              @click="editTitle(index)"
            >
              <span class="text-h6 text-white">{{ list.title }}</span>
            </div>
            <VTextField
              v-else
              v-model="list.title"
              dense
              hide-details
              single-line
              autofocus
              @blur="saveTitle(index)"
            />
          </VCardTitle>
        </VCard>
        <VueDraggableNext
          class="kanban-dropzone"
          :list="list.tasks"
          group="tasks"
          @change="onDrop(index, $event)"
        >
          <VCard
            v-for="(task) in list.tasks"
            :key="task.id"
            class="mt-2 task-card"
          >
            <div
              class="cursor-pointer"
              @click="startEditing(task)"
            >
              <VIcon
                icon="tabler-playstation-circle"
                color="primary"
                size="small"
              />
              <span class="ms-1">{{ task.name }}</span>
            </div>
            <div class="float-left">
              <VChip
                v-if="task.due_date"
                color="error"
                size="x-small"
              >
                {{ formatDate(task.due_date) }}
                <VTooltip
                  activator="parent"
                  location="top"
                >
                  <span>Task is due on {{ formatDate(task.due_date) }}</span>
                </VTooltip>
              </VChip>
            </div>
            <div class="float-right">
              <VBtn
                icon
                size="x-small"
                color="error"
                class="me-1"
                @click="deleteTask(task)"
              >
                <VIcon>
                  tabler-trash
                </VIcon>
              </VBtn>
              <VBtn
                icon
                size="x-small"
                color="success"
                @click="markAsComplete(task)"
              >
                <VIcon>
                  tabler-check
                </VIcon>
              </VBtn>
            </div>
          </VCard>
        </VueDraggableNext>
        <div class="justify-center mt-2">
          <VBtn
            v-if="!showKanbanAddTaskField"
            color="primary"
            variant="plain"
            size="small"
            @click="activateQuickKanbanAdd"
          >
            <VIcon icon="tabler-plus" />
            Add Task
          </VBtn>
        </div>
      </VCol>
      <!-- Add column button -->
      <VCol cols="4">
        <div class="justify-center">
          <VBtn
            color="primary"
            variant="plain"
            @click="addList"
          >
            <VIcon icon="tabler-plus" />
            Another List
          </VBtn>
        </div>
      </VCol>
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
import NoTaskInList from '@images/darby/tasks_list.svg?raw'
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
const showAddTaskField = ref(false)
const showKanbanAddTaskField = ref(false)
const quickTaskName = ref('')
const quickTaskInput = ref(null)
const dueDate = ref(null)

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

function activateQuickAdd() {
  showAddTaskField.value = true
  nextTick(() => {
    quickTaskInput.value.focus()
  })
}

function activateQuickKanbanAdd() {
  showKanbanAddTaskField.value = true
  nextTick(() => {
    quickTaskInput.value.focus()
  })
}

async function addQuickTask() {
  if (quickTaskName.value.trim() === '') {
    toast.error('Task name cannot be empty.')

    return
  }

  try {
    activateQuickAdd()
    activateQuickKanbanAdd()

    const newTaskDetails = {
      name: quickTaskName.value.trim(),
      due_date: dueDate.value,
      project_id: projectId.value,
    }

    await taskStore.create(newTaskDetails)
    quickTaskName.value = ''
    dueDate.value = null
    toast.success('Task added successfully', { timeout: 1000 })
    fetchProjectTasks()
  } catch (error) {
    toast.error('Failed to add task:', error)
  }
}

function cancelQuickTask() {
  showAddTaskField.value = false
  quickTaskName.value = ''
}

function cancelKanbanQuickTask() {
  showKanbanAddTaskField.value = false
  quickTaskName.value = ''
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

const lists = ref([
  { title: 'To Do', tasks: getProjectTasks, isEditing: false },
  { title: 'In Progress', tasks: [], isEditing: false },
  { title: 'Done', tasks: [], isEditing: false },
])

const editTitle = index => {
  lists.value[index].isEditing = true

  const titleInput = $refs.titleInput[index]

  titleInput && titleInput.focus()
}

const saveTitle = index => {
  lists.value[index].isEditing = false
}

const addList = () => {
  const id = Date.now()
  const newList = { id, title: 'New List', tasks: [] }

  lists.value.push(newList)
}

const onDrop = (targetListIndex, event) => {
  const sourceListIndex = event.dragged.sourceIndex
  const sourceTaskIndex = event.dragged.sourceModel.index
  const taskToMove = lists.value[sourceListIndex].tasks.splice(sourceTaskIndex, 1)[0]

  lists.value[targetListIndex].tasks.push(taskToMove)
}
</script>

<style scoped>
.kanban-columns {
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    padding-bottom: 1rem;
}

.kanban-column {
    flex: 1;
    min-width: 300px;
    margin-right: 1rem;
}

.d-toggle{
    border: unset !important;
    padding: 0 !important;
    align-items: unset !important;
    block-size: unset !important;
}
.task-row {
    border-bottom: 1px solid #ccc;
}
.task-card {
    border-radius: 5px !important;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid #e0e0e0;
    font-size: 14px;
    height: 100px;
    padding: 12px !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.task-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15) !important;
}
</style>

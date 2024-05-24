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
          <h5
            class="text-h5 ms-4 text-grey-700"
            style="margin-top: -2px;"
          >
            {{ project.title }}
          </h5>
        </VBtnToggle>
      </VCol>
      <VCol
        cols="12"
        md="6"
      >
        <div class="float-right">
          <VDialog
            v-if="viewType === 'list'"
            v-model="isAddListDialogVisible"
            persistent
            class="v-dialog-sm"
          >
            <template #activator="{ props }">
              <VBtn
                class="me-3"
                v-bind="props"
              >
                <VIcon icon="tabler-plus" />
                Add List
              </VBtn>
            </template>

            <!-- Dialog close btn -->
            <DialogCloseBtn @click="isAddListDialogVisible = !isAddListDialogVisible" />

            <!-- Dialog Content -->
            <VCard title="Add List">
              <VForm
                ref="addListForm"
                @submit.prevent="submitListForm"
              >
                <VCardText>
                  <AppTextField
                    v-model="listTitle"
                    label="Title *"
                    :rules="[requiredValidator]"
                  />
                </VCardText>
                <VCardText class="d-flex justify-end gap-3 flex-wrap">
                  <VBtn
                    color="secondary"
                    @click="isAddListDialogVisible = false"
                  >
                    Cancel
                  </VBtn>
                  <VBtn
                    type="submit"
                    @click="addListForm?.validate()"
                  >
                    Add
                  </VBtn>
                </VCardText>
              </VForm>
            </VCard>
          </VDialog>
          <VBtn
            v-if="!showAddTaskField && viewType === 'list'"
            color="primary"
            @click="activateQuickAdd"
          >
            <VIcon icon="tabler-plus" />
            Add Task
          </VBtn>
        </div>
      </VCol>
    </VRow>
    <!-- List View -->
    <VRow v-if="viewType === 'list'">
      <VCol cols="12">
        <div v-if="getProjectLists? getProjectLists.length > 0 : 0">
          <VCard
            v-for="(list, index) in getProjectLists"
            :key="index"
            class="px-4 py-4 mt-2"
          >
            <VRow>
              <VCol cols="6">
                <h6 class="text-h6 text-high-emphasize">
                  <VIcon
                    color="primary"
                    :icon="expandedRows[index] ? 'tabler-chevron-down' : 'tabler-chevron-right'"
                    @click="toggleRow(index)"
                  />
                  <span
                    class="mt-2 cursor-pointer"
                    @click="toggleRow(index)"
                  >{{ list.name }} ({{ dummyTasks.length }})</span>
                  <VBtn
                    v-if="!showAddListTaskField"
                    color="primary"
                    variant="plain"
                    size="small"
                    @click="activateQuickListTask"
                  >
                    <VIcon icon="tabler-plus" />
                    Add Task
                  </VBtn>
                </h6>
              </VCol>
            </VRow>
            <VRow v-if="expandedRows[index]">
              <VDataTable
                :headers="headers"
                :items="dummyTasks"
                density="compact"
                expand-on-click
                class="ms-3 py-3"
              >
                <template #expanded-row="{ item }">
                  <tr v-if="item.subtasks && item.subtasks.length">
                    <td :colspan="headers.length">
                      <VTable
                        density="compact"
                        class="text-no-wrap ms-5"
                      >
                        <tbody>
                          <tr
                            v-for="subtask in item.subtasks"
                            :key="subtask.id"
                          >
                            <td
                              width="59.5%"
                              style="padding: 0!important;"
                            >
                              <VIcon
                                class="tabler-playstation-circle"
                                size="small"
                                color="primary ms-2 me-2"
                              />
                              <span class="text-grey-600">{{ subtask.name }}</span>
                            </td>
                            <td style="padding: 0!important;">
                              <VChip
                                color="secondary"
                                size="small"
                              >
                                {{ subtask.status }}
                              </VChip>
                            </td>
                            <td style="padding: 0!important;">
                              <VChip
                                color="error"
                                size="small"
                              >
                                {{ formatDate(subtask.due_date) }}
                              </VChip>
                            </td>
                            <td style="padding: 0!important;">
                              <VChip
                                color="primary"
                                size="small"
                              >
                                {{ formatDate(subtask.created_at) }}
                              </VChip>
                            </td>
                            <td style="padding: 0!important;">
                              <IconBtn @click.prevent>
                                <VIcon icon="tabler-dots" />
                                <VMenu activator="parent">
                                  <VList>
                                    <VList>
                                      <VListItem
                                        value="edit"
                                        @click="startEditing(subtask)"
                                      >
                                        Edit
                                      </VListItem>
                                      <VListItem
                                        value="delete"
                                        @click="deleteTask(subtask)"
                                      >
                                        Delete
                                      </VListItem>
                                    </VList>
                                  </VList>
                                </VMenu>
                              </IconBtn>
                            </td>
                          </tr>
                        </tbody>
                      </VTable>
                    </td>
                  </tr>
                </template>
                <template #item.name="{ item }">
                  <VIcon
                    class="tabler-playstation-circle"
                    color="primary mr-2"
                  />
                  <span>{{ item.name }}</span>
                  <span
                    v-if="item.subtasks && item.subtasks.length"
                    class="text-caption"
                  >
                    <VIcon
                      class="tabler-subtask ms-1"
                      size="small"
                    />
                    <span>2</span>
                  </span>
                </template>
                <template #item.status="{ item }">
                  <VChip
                    color="secondary"
                    size="small"
                  >
                    {{ item.status }}
                  </VChip>
                </template>
                <template #item.due_date="{ item }">
                  <VChip
                    color="error"
                    size="small"
                  >
                    {{ formatDate(item.due_date) }}
                  </VChip>
                </template>
                <template #item.created_at="{ item }">
                  <VChip
                    color="primary"
                    size="small"
                  >
                    {{ formatDate(item.created_at) }}
                  </VChip>
                </template>
                <template #item.action="{ item }">
                  <IconBtn @click.prevent>
                    <VIcon icon="tabler-dots" />
                    <VMenu activator="parent">
                      <VList>
                        <VList>
                          <VListItem
                            value="edit"
                            @click="startEditing(item)"
                          >
                            Edit
                          </VListItem>
                          <VListItem
                            value="delete"
                            @click="deleteTask(item)"
                          >
                            Delete
                          </VListItem>
                        </VList>
                      </VList>
                    </VMenu>
                  </IconBtn>
                </template>
                <template #bottom>
                  <VRow
                    v-if="showAddListTaskField"
                    class="px-6 py-2"
                  >
                    <VCol cols="8">
                      <div class="d-flex align-center">
                        <VIcon
                          class="tabler-playstation-circle me-2"
                          color="primary"
                        />
                        <VTextField
                          ref="quickListTaskInput"
                          v-model="quickListTaskName"
                          placeholder="Task Name"
                          style="margin-top: -7px;"
                          variant="plain"
                          hide-details
                          dense
                          @keydown.enter="addQuickListTask"
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
                          @click="cancelQuickListTask"
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
                  <div class="float-left">
                    <VBtn
                      v-if="!showAddListTaskField"
                      color="primary"
                      class="ms-1 mt-2"
                      variant="plain"
                      size="small"
                      @click="activateQuickListTask"
                    >
                      <VIcon icon="tabler-plus" />
                      Add Task
                    </VBtn>
                  </div>
                </template>
              </VDataTable>
            </VRow>
          </VCard>
        </div>
      </VCol>
      <VCol cols="12">
        <div v-if="getProjectTasks? getProjectTasks.length > 0 : 0">
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
                  <VIcon icon="tabler-dots" />
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
            v-if="!showAddTaskField && !(getProjectLists ? getProjectLists.length > 0 : 0)"
            class="mt-12"
            v-html="NoTaskInList"
          />
          <span v-if="!showAddTaskField && !(getProjectLists ? getProjectLists.length > 0 : 0)">No tasks added yet.</span>
        </div>
        <VBtn
          v-if="!showAddTaskField && getProjectTasks? getProjectTasks.length > 0 : 0"
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
                  placeholder="Task Name"
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
      v-if="viewType === 'grid'"
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
            color="primary"
            variant="plain"
            size="small"
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
    :project-id="projectId"
    :get-load-status="getLoadStatus"
  />
</template>

<script setup="js">
import moment from 'moment'
import NoTaskInList from '@images/darby/tasks_list.svg?raw'
import EditTaskDrawer from '@/pages/projects/web-designs/_partials/update-project-task-drawer.vue'
import { computed, onBeforeMount, nextTick, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useProjectStore } from "../../../../store/projects"
import { useProjectTaskStore } from "../../../../store/project_tasks"
import { useListTaskStore } from "@/store/list_tasks"
import { useProjectListStore } from "../../../../store/project_lists"
import { useRouter } from 'vue-router'
import { VueDraggableNext } from 'vue-draggable-next'

const toast = useToast()
const projectStore = useProjectStore()
const projectTaskStore = useProjectTaskStore()
const listTaskStore = useListTaskStore()
const projectListStore = useProjectListStore()
const router = useRouter()

const viewType = ref('list')

const editingTask = ref({})
const isEditTaskDrawerOpen = ref(false)

const isAddListDialogVisible = ref(false)
const addListForm = ref()
const listTitle = ref(null)

const showAddTaskField = ref(false)
const showAddListTaskField = ref(false)
const quickTaskName = ref('')
const quickListTaskName = ref('')
const quickTaskInput = ref(null)
const quickListTaskInput = ref(null)
const dueDate = ref(null)

const isLoading = ref(false)

const projectId = computed(() => router.currentRoute.value.params.id)

const formatDate = date => moment(date).format('MMM DD, YYYY')

onBeforeMount(async () => {
  await fetchProjectTasks()
  await fetchProjectLists()
  await fetchProjectDetails()
})

const fetchProjectDetails = async () => {
  try {
    isLoading.value = true
    await projectStore.show(projectId.value)
  } catch (error) {
    toast.error('Error fetching project details:', error)
  }
  finally {
    isLoading.value = false
  }
}

const fetchProjectTasks = async () => {
  try {
    isLoading.value = true
    await projectTaskStore.getAll(projectId.value)
  } catch (error) {
    toast.error('Error fetching project tasks:', error)
  }
  finally {
    isLoading.value = false
  }
}

const fetchProjectLists = async () => {
  try {
    isLoading.value = true
    await projectListStore.getAll(projectId.value)
  } catch (error) {
    toast.error('Error fetching project lists:', error)
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

function activateQuickListTask() {
  showAddListTaskField.value = true
  nextTick(() => {
    const inputElement = quickListTaskInput.value[0].$el.querySelector('input')
    if (inputElement) {
      inputElement.focus()
    }
  })
}

async function submitListForm() {
  addListForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        const newListDetails = {
          name: listTitle.value.trim(),
          project_uuid: projectId.value,
        }

        await projectListStore.create(newListDetails)
        listTitle.value = ''
        isAddListDialogVisible.value = false
        toast.success('List added successfully', { timeout: 1000 })
        await fetchProjectLists()
      } catch (error) {
        console.error('Error adding/updating list:', error)
        toast.error('Failed to add/update list:', error)
      }
    }
  })
}

async function addQuickTask() {
  if (quickTaskName.value.trim() === '') {
    toast.error('Task name cannot be empty.')

    return
  }

  try {
    activateQuickAdd()

    const newTaskDetails = {
      name: quickTaskName.value.trim(),
      due_date: dueDate.value,
      project_uuid: projectId.value,
    }

    await projectTaskStore.create(newTaskDetails)
    quickTaskName.value = ''
    dueDate.value = null
    toast.success('Task added successfully', { timeout: 1000 })
    fetchProjectTasks()
  } catch (error) {
    toast.error('Failed to add task:', error)
  }
}

async function addQuickListTask() {
  if (quickListTaskName.value.trim() === '') {
    toast.error('Task name cannot be empty.')

    return
  }

  try {
    activateQuickListTask()

    const newTaskListkDetails = {
      name: quickListTaskName.value.trim(),
      due_date: dueDate.value,
      project_uuid: projectId.value,
    }
    alert('here');

    console.log('listTaskStore', listTaskStore)

    await listTaskStore.create(newTaskListkDetails)
    quickListTaskName.value = ''
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

function cancelQuickListTask() {
  showAddListTaskField.value = false
  quickListTaskName.value = ''
}

function startEditing(task) {
  editingTask.value = { ...task, project_uuid: projectId.value }
  isEditTaskDrawerOpen.value = true
}

async function deleteTask(task) {
  try {
    task.isDeleting = true

    const taskWithProjectId = { ...task, project_uuid: projectId.value }

    await projectTaskStore.delete(taskWithProjectId)
    toast.success('Task deleted successfully', { timeout: 1000 })
    task.isDeleting = false
    fetchProjectTasks()
  } catch (error) {
    toast.error('Failed to delete task:', error)
  }
}

const getProjectTasks = computed(() => projectTaskStore.getProjectTasks)
const getProjectLists = computed(() => projectListStore.getProjectLists)
const project = computed(() => projectStore.getProject)

const getLoadStatus = computed(() => {
  return projectTaskStore.getLoadStatus
})

const lists = ref([
  { title: 'To Do', tasks: getProjectTasks, isEditing: false },
  { title: 'In Progress', tasks: [], isEditing: false },
  { title: 'Done', tasks: [], isEditing: false },
])

const headers = [
  { title: 'Task Name', key: 'name', sortable: false, width: '60%' },
  { title: 'Status', key: 'status',  sortable: false },
  { title: 'Due Date', key: 'due_date', sortable: false },
  { title: 'Created Date', key: 'created_at', sortable: false },
  { title: 'Action', key: 'action', sortable: false },
]

const dummyTasks = [
  { id: 1, name: 'Task 1', status: 'In Progress', due_date: '2022-12-12', created_at: '2022-12-12',
    subtasks: [
      { id: 101, name: 'Subtask 1.1', status: 'In Progress', due_date: '2022-12-15', created_at: '2022-12-13' },
      { id: 102, name: 'Subtask 1.2', status: 'In Progress', due_date: '2022-12-16', created_at: '2022-12-14' },
    ],
  },
  { id: 2, name: 'Task 2', status: 'In Progress', due_date: '2022-12-12', created_at: '2022-12-12' },
  { id: 3, name: 'Task 3', status: 'In Progress', due_date: '2022-12-12', created_at: '2022-12-12' },
  { id: 4, name: 'Task 4', status: 'In Progress', due_date: '2022-12-12', created_at: '2022-12-12' },
]

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

const expandedRows = ref([])

expandedRows.value[0] = true

const toggleRow = index => {
  expandedRows.value[index] = !expandedRows.value[index]
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
.expanded-row, .expanded-td{
    padding: 0 !important;
}
</style>

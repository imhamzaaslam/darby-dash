<template>
  <!-- Toggle -->
  <Loader v-if="isLoading" />
  <VRow>
    <VCol
      cols="12"
      md="6"
      class="d-flex align-center"
    >
      <div class="d-flex justify-center align-center">
        <VIcon
          color="primary"
          :size="28"
          icon="tabler-template"
          class="me-1"
        />
        <h3 class="text-primary">
          {{ template?.template_name }}
        </h3>
      </div>
    </VCol>
    <VCol
      cols="12"
      md="6"
    >
      <div class="d-flex flex-column flex-md-row align-center justify-end">
        <!-- Button and Dialog -->
        <VBtn
          icon
          color="td-hover"
          class="ma-2 custom-btn-style"
          size="small"
          rounded="pills"
          @click.prevent
        >
          <VIcon icon="tabler-dots" />
          <VMenu activator="parent">
            <VList>
              <VListItem
                value="sort-list"
                @click="isSortListModalOpen = true"
              >
                Manage Lists
              </VListItem>
            </VList>
          </VMenu>
        </VBtn>
        <VDialog
          v-model="isAddListDialogVisible"
          persistent
          class="v-dialog-sm"
        >
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
                  autofocus
                  :rules="[requiredValidator]"
                />
              </VCardText>
              <VCardText class="d-flex justify-end gap-3 flex-wrap">
                <VBtn
                  color="secondary"
                  class="custom-secondary-btn"
                  @click="isAddListDialogVisible = false"
                >
                  Cancel
                </VBtn>
                <VBtn
                  type="submit"
                  class="custom-btn-style"
                  @click="addListForm?.validate()"
                >
                  Add
                </VBtn>
              </VCardText>
            </VForm>
          </VCard>
        </VDialog>
      </div>
    </VCol>
  </VRow>
  <!-- List View -->
  <VRow class="mt-1">
    <VCol cols="12">
      <div v-if="getTemplateLists? getTemplateLists.length > 0 : 0">
        <VCard
          v-for="(list, index) in getTemplateLists"
          :key="index"
          :class="`px-4 py-4 mt-2 ${expandedRows[index] ? '' : 'list-side-border'}`"
        >
          <VRow
            class="cursor-pointer"
            @click="toggleRow(index)"
          >
            <VCol cols="8">
              <h6 class="text-h6 text-high-emphasis d-flex align-center">
                <VIcon
                  color="primary"
                  :icon="expandedRows[index] ? 'tabler-chevron-down' : 'tabler-chevron-right'"
                  class="me-2"
                  @click.stop="toggleRow(index)"
                />
                <div
                  class="d-flex align-center"
                  style="flex-grow: 1;"
                >
                  <span
                    v-if="isListEditing[list.id]"
                    class="d-flex align-center"
                    style="flex-grow: 1;"
                  >
                    <VTextField
                      :ref="el => editListTitleInput[list.id] = el"
                      v-model="editListTitle"
                      class="text-white text-h6 pt-0"
                      density="compact"
                      :style="{ maxWidth: '300px' }"
                      @blur="saveListTitle(list)"
                      @keyup.enter="cancelStateChangeListField(list)"
                    />
                  </span>
                  <span
                    v-else
                    class="cursor-pointer d-flex align-center"
                  >
                    <VIcon
                      class="tabler-edit cursor-pointer"
                      color="primary"
                      @click.stop="startListEditing(list)"
                    />
                    <VTooltip
                      activator="parent"
                      location="top"
                    >
                      <span class="text-xs">Edit List Title</span>
                    </VTooltip>
                    {{ list.name }} ({{ list.tasks.length }})
                  </span>
                  <VBtn
                    v-if="!isListEditing[list.id] && !showAddListTaskField[index]"
                    color="primary"
                    class="custom-btn-style"
                    variant="plain"
                    size="small"
                    @click.stop="activateQuickListTask(index)"
                  >
                    <VIcon icon="tabler-plus" />
                    Add Task
                  </VBtn>
                </div>
              </h6>
            </VCol>
            <VCol
              cols="4"
              class="d-flex justify-end align-center"
            >
              <AppTextField
                v-model="searchQuery[index]"
                placeholder="Search tasks"
                clearable
                style="width:100%"
                class="me-2"
                @click.stop
              />
              <VBtn
                v-if="getTemplateLists? getTemplateLists.length > 1 : 0"
                icon
                size="small"
                :color="focusDeleteListId === list.id ? 'error' : 'grey-500'"
                @mouseenter="focusDeleteListId = list.id"
                @mouseleave="focusDeleteListId = null"
                @click.stop="deleteTemplateList(list)"
              >
                <VIcon icon="tabler-trash" />
                <VTooltip
                  activator="parent"
                  location="top"
                >
                  <span class="text-xs">Delete List</span>
                </VTooltip>
              </VBtn>
            </VCol>
          </VRow>
          <VRow v-if="expandedRows[index]">
            <VDataTable
              :headers="headers"
              :items="filteredTasks(index, list.tasks)"
              density="compact"
              class="ms-3 py-3"
              :items-per-page="-1"
            >
              <template #item="{ item }">
                <tr
                  :class="{ 'bg-td-hover': showAddSubtaskIcon === item.uuid }"
                  @mouseenter="showAddSubtaskIcon = item.uuid"
                  @mouseleave="showAddSubtaskIcon = null"
                >
                  <td
                    class="cursor-pointer"
                    @click="startEditing(list, item)"
                  >
                    <VIcon
                      v-if="item.subtasks && item.subtasks.length"
                      :icon="isExpandedSubTasks[item.id] ? 'tabler-chevron-down' : 'tabler-chevron-right'"
                      color="primary"
                      @click.stop="toggleSubtasks(item.id)"
                    />
                    <VIcon
                      class="tabler-playstation-circle"
                      color="primary me-1"
                    />
                    <span>{{ item.name.length > 50 ? item.name.substring(0, 50) + '...' : item.name }}</span>
                    <span
                      v-if="item.subtasks && item.subtasks.length"
                      class="text-primary  text-sm font-weight-bold cursor-pointer"
                      @click.stop="toggleSubtasks(item.id)"
                    >
                      <VIcon
                        size="small"
                        color="primary"
                        class="ms-1 tabler-subtask"
                      >
                        <VTooltip
                          activator="parent"
                          location="top"
                        >
                          <span class="text-xs">{{ item.subtasks.length }} Subtasks</span>
                        </VTooltip>
                      </VIcon>
                      <span>{{ item.subtasks.length }}</span>
                    </span>
                    <VIcon
                      :color="showAddSubtaskIcon === item.uuid ? 'primary' : 'white'"
                      variant="text"
                      rounded
                      class="ms-2 tabler-plus"
                      size="small"
                      @click.stop="activateQuickSubTask(item.id)"
                    >
                      <VTooltip
                        activator="parent"
                        location="top"
                      >
                        <span class="text-xs">Add Subtask</span>
                      </VTooltip>
                    </VIcon>
                    <VIcon
                      :color="showAddSubtaskIcon === item.uuid ? 'primary' : 'white'"
                      variant="text"
                      class="tabler-edit"
                      rounded
                      size="small"
                      @click.stop="startEditing(list, item)"
                    >
                      <VTooltip
                        activator="parent"
                        location="top"
                      >
                        <span class="text-xs">Edit Task</span>
                      </VTooltip>
                    </VIcon>
                  </td>
                  <td>
                    <IconBtn @click.prevent>
                      <VIcon icon="tabler-dots" />
                      <VMenu activator="parent">
                        <VList>
                          <VList>
                            <VListItem
                              value="edit"
                              @click="startEditing(list, item)"
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
                  </td>
                </tr>
                <template v-if="isExpandedSubTasks[item.id] && item.subtasks && item.subtasks.length">
                  <tr
                    v-for="subtask in item.subtasks"
                    :key="subtask.id"
                    width="100%"
                    :class="{ 'bg-td-hover': showAddSubtaskIcon === subtask.uuid }"
                    @mouseenter="showAddSubtaskIcon = subtask.uuid"
                    @mouseleave="showAddSubtaskIcon = null"
                  >
                    <td
                      class="cursor-pointer"
                      @click.stop="startEditing(list, subtask)"
                    >
                      <VIcon
                        class="tabler-playstation-circle"
                        size="x-small"
                        color="primary ms-6 me-2"
                      />
                      <span class="text-grey-600">{{ subtask.name.length > 50 ? subtask.name.substring(0, 50) + '...' : subtask.name }}</span>
                    </td>
                    <td>
                      <IconBtn @click.prevent>
                        <VIcon icon="tabler-dots" />
                        <VMenu activator="parent">
                          <VList>
                            <VList>
                              <VListItem
                                value="edit"
                                @click="startEditing(list, subtask)"
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
                </template>
                <tr>
                  <VRow
                    v-if="showAddSubTaskField[item.id]"
                    class="px-12 py-2"
                  >
                    <VCol cols="8">
                      <div class="d-flex align-center">
                        <VIcon
                          class="tabler-playstation-circle me-2"
                          color="primary"
                          size="x-small"
                        />
                        <VTextField
                          :ref="el => quickSubTaskInput[item.id] = el"
                          v-model="quickSubTaskName[item.id]"
                          placeholder="Subtask Name"
                          style="margin-top: -7px;"
                          variant="plain"
                          hide-details
                          dense
                          @keydown.enter="addQuickSubTask(list.uuid, item.id)"
                        />
                      </div>
                    </VCol>
                    <VCol cols="4">
                      <div class="float-right">
                        <VIcon
                          class="tabler-circle-check me-1"
                          size="small"
                          color="primary"
                          @click="addQuickSubTask(list.uuid, item.id)"
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
                          size="small"
                          color="primary"
                          @click="cancelQuickSubTask(item.id)"
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
                </tr>
              </template>
              <template #bottom>
                <VRow
                  v-if="showAddListTaskField[index]"
                  class="px-6 py-2"
                >
                  <VCol cols="8">
                    <div class="d-flex align-center">
                      <VIcon
                        class="tabler-playstation-circle me-2"
                        color="primary"
                      />
                      <VTextField
                        :ref="el => quickListTaskInput[index] = el"
                        v-model="quickListTaskName[index]"
                        placeholder="Task Name"
                        style="margin-top: -7px;"
                        variant="plain"
                        hide-details
                        dense
                        @keydown.enter="addQuickListTask(list.uuid, index)"
                      />
                    </div>
                  </VCol>
                  <VCol cols="4">
                    <div class="float-right">
                      <VIcon
                        class="tabler-circle-check me-1"
                        color="primary"
                        @click="addQuickListTask(list.uuid, index)"
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
                        @click="cancelQuickListTask(index)"
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
                    v-if="!showAddListTaskField[index]"
                    color="primary"
                    class="ms-1 mt-2 custom-btn-style"
                    variant="plain"
                    size="small"
                    @click="activateQuickListTask(index)"
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
  </VRow>
  <EditTaskDrawer
    v-model:is-edit-task-drawer-open="isEditTaskDrawerOpen"
    :fetch-template="fetchTemplate"
    :editing-task="editingTask"
    :get-load-status="getLoadStatus"
  />
  <SortListModal
    v-model:is-sort-list-modal-open="isSortListModalOpen"
    :selected-template="templateId"
    :fetch-template="fetchTemplate"
    :get-template-lists="getTemplateLists"
  />
</template>

<script setup="js">
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import moment from 'moment'
import Swal from 'sweetalert2'
import EditTaskDrawer from '@/pages/settings/templates/_partials/update-template-task-drawer.vue'
import SortListModal from '@/pages/settings/templates/_partials/sort-list-modal.vue'
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useTemplateStore } from "@/store/templates"
import { useRouter } from 'vue-router'
import { VIcon } from 'vuetify/lib/components/index.mjs'
import Loader from "@/components/Loader.vue"

const toast = useToast()
const templateStore = useTemplateStore()
const router = useRouter()

const isLoading = ref(false)
const isEditTaskDrawerOpen = ref(false)
const isSortListModalOpen = ref(false)

const expandedRows = ref([])
const showAddSubtaskIcon= ref(null)
const showAddListTaskField = ref([])
const showAddSubTaskField = ref([])
const quickListTaskInput = ref([])
const quickListTaskName = ref([])
const quickSubTaskInput = ref([])
const quickSubTaskName = ref([])

const isExpandedSubTasks = ref([])

const isListEditing = ref([])
const editListTitleInput = ref([])
const editListTitle = ref(null)

const searchQuery = ref([])

const focusDeleteListId = ref(null)

const templateId = router.currentRoute.value.params.id

const editingTask = ref({})

const headers = [
  { title: 'Task Name', key: 'name', sortable: false, width: '100%' },
  { title: 'Action', key: 'action', sortable: false },
]

const formatDate = date => moment(date).format('MM/DD/YYYY')

onBeforeMount(async () => {
  isLoading.value = true
  await fetchTemplate()
  isLoading.value = false
  toggleRow(0, true)
})

const fetchTemplate = async () => {
  try {
    await templateStore.show(templateId)
  } catch (error) {
    toast.error('Error showing template:', error)
  }
}

const toggleRow = (index, isIteration = false) => {
  expandedRows.value[index] = !expandedRows.value[index]
  if(isIteration)
  {
    expandedRows.value.forEach((row, i) => {
      if (i != index) {
        expandedRows.value[i] = false
      }
    })
    activateQuickListTask(index)
  }
  if(!isIteration)
  {
    cancelQuickListTask(index)
  }
}

function activateQuickListTask(index) {
  showAddListTaskField.value[index] = true
  expandedRows.value[index] = true
  nextTick(() => {
    const inputRef = quickListTaskInput.value[index]
    if (inputRef && inputRef.focus) {
      inputRef.focus()
    }
  })
}

function toggleSubtasks(index){
  isExpandedSubTasks.value[index] = !isExpandedSubTasks.value[index]
  cancelQuickSubTask(index)
}

function activateQuickSubTask(index) {
  showAddSubTaskField.value[index] = true
  isExpandedSubTasks.value[index] = true
  nextTick(() => {
    const inputRef = quickSubTaskInput.value[index]
    if (inputRef && inputRef.focus) {
      inputRef.focus()
    }
  })
}

function cancelQuickListTask(index) {
  showAddListTaskField.value[index] = false
  quickListTaskName.value[index] = ''
}

function cancelQuickSubTask(index) {
  showAddSubTaskField.value[index] = false
  quickSubTaskName.value[index] = ''
}

function startEditing(list, task) {
  editingTask.value = { ...task, templateUuid: templateId, listUuid: list.uuid }
  isEditTaskDrawerOpen.value = true
}

const startListEditing = list => {
  isListEditing.value[list.id] = true
  editListTitle.value = list.name
  nextTick(() => {
    const inputRef = editListTitleInput.value[list.id]
    if (inputRef && inputRef.focus) {
      inputRef.focus()
    }
  })
}

const saveListTitle = async list => {
  try {
    isListEditing.value[list.id] = false

    const editedName = editListTitle.value.trim()
    if (editedName !== list.name) {
      const editListDetails = {
        name: editedName,
      }

      await templateStore.updateList(list.uuid, editListDetails)
      toast.success('Title updated successfully', { timeout: 1000 })
      await fetchTemplate()
    }
  } catch (error) {
    console.error('Error adding/updating list:', error)
    toast.error('Failed to add/update list:', error)
  }
}

function cancelStateChangeListField(list)
{
  isListEditing.value[list.id] = false
}

function filteredTasks(index, tasks) {
  const query = searchQuery.value[index] || ''

  return tasks.filter(task => task.name.toLowerCase().includes(query.toLowerCase()))
}

const deleteTemplateList = async list => {
  try {

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
      confirmButtonColor: "rgba(var(--v-theme-primary))",
      cancelButtonColor: "#808390",
      confirmButtonText: "Yes, delete it!",
      didOpen: () => {
        document.querySelector('.swal2-confirm').blur()
      },
    })

    if (confirmDelete.isConfirmed) {

      const res = await templateStore.deleteList(list.uuid)

      toast.success('Template list deleted successfully', { timeout: 1000 })
      await fetchTemplate()
    }
  } catch (error) {
    toast.error('Failed to delete project list:', error)
  }
}

async function addQuickListTask(listUuid, index) {
  const taskName = quickListTaskName.value[index]

  if (taskName.trim() === '') {
    toast.error('Task name cannot be empty.')

    return
  }

  try {
    activateQuickListTask(index)

    const newTaskListDetails = {
      name: taskName.trim(),
    }

    await templateStore.createTask(listUuid, newTaskListDetails)
    quickListTaskName.value[index] = ''
    toast.success('Task added successfully', { timeout: 1000 })
    fetchTemplate()
  } catch (error) {
    toast.error('Failed to add task:', error)
  }
}

async function addQuickSubTask(listUuid, index) {
  const taskName = quickSubTaskName.value[index]

  if (taskName.trim() === '') {
    toast.error('Task name cannot be empty.')

    return
  }

  try {
    activateQuickSubTask(index)

    const newTaskListDetails = {
      name: taskName.trim(),
      parent_id: index,
    }

    await templateStore.createTask(listUuid, newTaskListDetails)
    quickSubTaskName.value[index] = ''
    toast.success('Task added successfully', { timeout: 1000 })
    fetchTemplate()
  } catch (error) {
    toast.error('Failed to add task:', error)
  }
}

async function deleteTask(task) {
  try {
    await templateStore.deleteTask(task.uuid)
    toast.success('Task deleted successfully', { timeout: 1000 })
    await fetchTemplate()
  } catch (error) {
    toast.error('Failed to delete task:', error)
  }
}

const getTemplateLists = computed(() => templateStore.getTemplate.lists)
const template = computed(() => templateStore.getTemplate)

const getLoadStatus = computed(() => {
  return templateStore.getLoadStatus
})

watch(template, () => {
  useHead({ title: `${layoutConfig.app.title} | Manage ${template?.value?.template_name}` })
})
</script>

<style scoped>
  .task-row {
      border-bottom: 1px solid #ccc;
  }
  .task-card {
      border-radius: 8px !important;
      font-size: 14px;
      height: auto;
      margin-right: 5px;
  }

  .task-card:hover {
      border: 1px solid rgba(var(--v-theme-primary));
  }
  .expanded-row, .expanded-td{
      padding: 0 !important;
  }
  .light::-webkit-scrollbar {
    width: 10px;
  }

  .light::-webkit-scrollbar-track {
    background: #e6e6e6;
    border-left: 1px solid #dadada;
  }

  .light::-webkit-scrollbar-thumb {
    background: #b0b0b0;
    border: solid 3px #e6e6e6;
    border-radius: 7px;
  }

  .light::-webkit-scrollbar-thumb:hover {
    background: #b0b0b0;
  }

  .align-center-important {
    align-items: center !important;
  }
</style>

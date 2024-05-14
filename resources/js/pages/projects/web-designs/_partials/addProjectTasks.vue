<template>
  <VContainer>
    <VRow>
      <VCol cols="12">
        <AppTextField
          v-if="!editingTask"
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
        <AppTextField
          v-else
          ref="editTaskFieldRef"
          v-model="editingTask.name"
          label="Edit Task"
          clearable
          placeholder="Edit Task"
          class="textfield-demo-icon-slot"
          @keydown.enter="saveTask(editingTask)"
        >
          <!-- Append -->
          <template #append>
            <VBtn
              :icon="$vuetify.display.smAndDown"
              @click="saveTask(editingTask)"
            >
              <VIcon
                icon="tabler-pencil"
                color="#fff"
                size="22"
              />
              <span
                v-if="$vuetify.display.mdAndUp"
                class="ms-1"
              >Update</span>
            </VBtn>

            <VBtn
              :icon="$vuetify.display.smAndDown"
              color="error"
              class="ms-1"
              @click="resetEditingTask"
            >
              <VIcon
                icon="tabler-refresh"
                color="#fff"
                size="22"
              />
              <span
                v-if="$vuetify.display.mdAndUp"
                class="me-1"
              >Reset</span>
            </VBtn>
          </template>
        </AppTextField>
      </VCol>
    </VRow>
    <VRow>
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
                    <span class="ms-2">{{ task.name }}</span>
                  </VCol>
                  <VCol cols="auto">
                    <VChip
                      color="primary"
                      size="small"
                      class="ms-2"
                    >
                      {{ task.status }}
                    </VChip>
                    <VChip
                      color="info"
                      size="small"
                      class="ms-2"
                    >
                      {{ formatDate(task.created_at) }}
                    </VChip>
                    <!-- <VChip
                      color="secondary"
                      size="small"
                      class="ms-2"
                    >
                      <VIcon class="tabler-message-circle" /> 8
                    </VChip> -->
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
  </VContainer>
</template>

<script setup="js">
import moment from 'moment'
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useTaskStore } from "../../../../store/tasks"
import { useRouter } from 'vue-router'

const toast = useToast()
const taskStore = useTaskStore()
const router = useRouter()

const taskName = ref('')
const editingTask = ref(null)
const editTaskFieldRef = ref(null)

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
  if (editTaskFieldRef.value) {
    editTaskFieldRef.value.focus()
  }
}

async function saveTask(task) {
  try {
    const editTaskDetails = {
      uuid: task.uuid,
      name: task.name,
      project_id: task.project_id,
    }

    await taskStore.update(editTaskDetails)
    toast.success('Task updated successfully', { timeout: 1000 })
    editingTask.value = null
    fetchProjectTasks()
  } catch (error) {
    toast.error('Failed to update task:', error)
  }
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

const resetEditingTask = () => {
  editingTask.value = null
  taskName.value = ''
}

const getProjectTasks = computed(() => taskStore.getProjectTasks)
</script>

<style scoped>
.task-row {
  border-bottom: 1px solid #ccc;
}
</style>

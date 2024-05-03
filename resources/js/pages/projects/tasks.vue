<template>
  <VContainer>
    <h2>Task List</h2>
    <div class="kanban-scroll">
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
                    <VListItemTitle>{{ task.title }}</VListItemTitle>
                    <VListItemSubtitle>{{ task.description }}</VListItemSubtitle>
                  </VListItemContent>
                </VListItem>
              </template>
            </VueDraggableNext>
            <VDivider />
            <VCardActions class="justify-center">
              <VBtn
                color="primary"
                variant="plain"
                @click="addTask(index)"
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
    </div>
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
  </VContainer>
</template>

<script setup>
import { ref } from 'vue'
import { VueDraggableNext } from 'vue-draggable-next'

const isDraggable = true

const onDragStart = event => {
  event.dataTransfer.setData("text/plain", "This text may be dragged")
}

// Define the columns array with initial tasks
const columns = ref([
  { title: 'To Do', tasks: [{ id: 1, title: 'Task 1', description: 'Description of Task 1' }, { id: 2, title: 'Task 2', description: 'Description of Task 2' }], isEditing: false },
  { title: 'In Progress', tasks: [{ id: 3, title: 'Task 3', description: 'Description of Task 3' }], isEditing: false },
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
const addTask = columnIndex => {
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

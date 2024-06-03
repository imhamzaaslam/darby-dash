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
            v-model="isAddListDialogVisible"
            persistent
            class="v-dialog-sm"
          >
            <template
              v-if="viewType === 'list'"
              #activator="{ props }"
            >
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
                <h6 class="text-h6 text-high-emphasis d-flex align-center">
                  <VIcon
                    color="primary"
                    :icon="expandedRows[index] ? 'tabler-chevron-down' : 'tabler-chevron-right'"
                    class="me-2"
                    @click="toggleRow(index)"
                  />
                  <div
                    class="d-flex align-center"
                    style="flex-grow: 1;"
                  >
                    <span
                      v-if="isListEditing[list.id] && list.is_deletable"
                      class="d-flex align-center"
                      style="flex-grow: 1;"
                    >
                      <VTextField
                        :ref="el => editListTitleInput[list.id] = el"
                        v-model="editListTitle"
                        class="text-white text-h6 pt-0"
                        density="compact"
                        variant="plain"
                        @blur="saveListTitle(list)"
                        @keyup.enter="cancelStateChangeListField(list)"
                      />
                    </span>
                    <span
                      v-else
                      class="cursor-pointer d-flex align-center"
                      @click="startListEditing(list)"
                    >
                      {{ list.name }} ({{ list.tasks.length }})
                    </span>
                    <VBtn
                      v-if="!showAddListTaskField[index]"
                      color="primary"
                      variant="plain"
                      size="small"
                      @click="activateQuickListTask(index)"
                    >
                      <VIcon icon="tabler-plus" />
                      Add Task
                    </VBtn>
                  </div>
                </h6>
              </VCol>
              <VCol
                v-if="list.is_deletable"
                cols="6"
              >
                <div class="d-flex justify-end">
                  <VBtn
                    icon
                    size="small"
                    color="error"
                    @click="deleteProjectList(list)"
                  >
                    <VIcon icon="tabler-trash" />
                  </VBtn>
                </div>
              </VCol>
            </VRow>
            <VRow v-if="expandedRows[index]">
              <VDataTable
                :headers="headers"
                :items="list.tasks"
                density="compact"
                class="ms-3 py-3"
                :items-per-page="-1"
              >
                <template #item="{ item }">
                  <tr
                    :class="{ 'bg-td-hover': showAddSubtaskIcon === item.uuid }"
                    @click="startEditing(item)"
                    @mouseenter="showAddSubtaskIcon = item.uuid"
                    @mouseleave="showAddSubtaskIcon = null"
                  >
                    <td>
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
                          class="tabler-subtask ms-1"
                          size="small"
                        />
                        <span>{{ item.subtasks.length }}</span>
                      </span>
                      <VBtn
                        :color="showAddSubtaskIcon === item.uuid ? 'primary' : 'white'"
                        variant="text"
                        rounded
                        class="ms-1"
                        icon="tabler-plus"
                        size="x-small"
                        @click.stop="activateQuickSubTask(item.id)"
                      />
                      <VBtn
                        :color="showAddSubtaskIcon === item.uuid ? 'primary' : 'white'"
                        variant="text"
                        rounded
                        icon="tabler-edit"
                        size="x-small"
                        @click.stop="startEditing(item)"
                      />
                    </td>
                    <td>
                      <VChip
                        color="secondary"
                        size="small"
                      >
                        {{ item.status }}
                      </VChip>
                    </td>
                    <td>
                      <VChip
                        v-if="item.due_date"
                        color="error"
                        size="small"
                      >
                        {{ formatDate(item.due_date) }}
                      </VChip>
                    </td>
                    <td>
                      <VChip
                        color="primary"
                        size="small"
                      >
                        {{ formatDate(item.created_at) }}
                      </VChip>
                    </td>
                    <td>
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
                    </td>
                  </tr>
                  <template v-if="isExpandedSubTasks[item.id] && item.subtasks && item.subtasks.length">
                    <tr
                      v-for="subtask in item.subtasks"
                      :key="subtask.id"
                      width="100%"
                    >
                      <td>
                        <VIcon
                          class="tabler-playstation-circle"
                          size="x-small"
                          color="primary ms-6 me-2"
                        />
                        <span class="text-grey-600">{{ subtask.name.length > 50 ? subtask.name.substring(0, 50) + '...' : subtask.name }}</span>
                      </td>
                      <td>
                        <VChip
                          color="secondary"
                          size="small"
                        >
                          {{ subtask.status.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ') }}
                        </VChip>
                      </td>
                      <td>
                        <VChip
                          v-if="subtask.due_date"
                          color="error"
                          size="small"
                        >
                          {{ formatDate(subtask.due_date) }}
                        </VChip>
                      </td>
                      <td>
                        <VChip
                          color="primary"
                          size="small"
                        >
                          {{ formatDate(subtask.created_at) }}
                        </VChip>
                      </td>
                      <td>
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
                            class="tabler-calendar me-1"
                            color="primary"
                            size="small"
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
                      class="ms-1 mt-2"
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
        v-for="(list, index) in getProjectLists"
        :key="index"
        cols="4"
        class="kanban-column"
      >
        <VCard class="mb-5">
          <VCardTitle class="bg-primary">
            <div v-if="isListEditing[list.id] && list.is_deletable">
              <VTextField
                :ref="el => editListTitleInput[list.id] = el"
                v-model="editListTitle"
                class="text-white pt-0"
                density="compact"
                variant="plain"
                @blur="saveListTitle(list)"
                @keyup.enter="cancelStateChangeListField(list)"
              />
            </div>
            <div
              v-else
              class="cursor-pointer d-flex align-center"
              @click="startListEditing(list)"
            >
              <span class="text-h6 text-white">{{ list.name.length > 25 ? list.name.substring(0, 25) + '...' : list.name }} ({{ list.tasks.length }})</span>
              <VSpacer />
              <VIcon
                v-if="list.is_deletable"
                class="tabler-trash"
                size="small"
                color="white"
                @click.stop="deleteProjectList(list)"
              >
                <VTooltip
                  activator="parent"
                  location="top"
                >
                  <span class="text-xs">Delete List</span>
                </VTooltip>
              </VIcon>
            </div>
          </VCardTitle>
        </VCard>
        <VueDraggableNext
          class="kanban-dropzone light"
          :list="list.tasks"
          group="tasks"
          @change="onDrop(list.id, $event)"
          @start="onDragStart"
          @end="onDragEnd"
        >
          <div
            v-for="(task) in list.tasks"
            :key="task.id"
          >
            <VCard
              class="mt-1 mb-2 task-card px-3 py-3"
              :class="{ 'bg-grey-600': isDragging }"
              @click="startEditing(task)"
              @mouseenter="showKanbanTaskIcon = task.uuid"
              @mouseleave="showKanbanTaskIcon = null"
            >
              <div class="cursor-pointer">
                <VIcon
                  icon="tabler-playstation-circle"
                  color="primary"
                  size="small"
                />
                <span class="ms-1">{{ task.name }}</span>
              </div>
              <div class="d-flex justify-space-between align-center mt-2">
                <!-- Chip and Subtask Section -->
                <div class="d-flex align-center">
                  <!-- Chip -->
                  <div>
                    <VChip
                      v-if="task.due_date"
                      color="error"
                      size="x-small"
                      class="me-2"
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

                  <!-- Subtask Count -->
                  <div
                    v-if="task.subtasks.length > 0"
                    class="d-flex align-center"
                  >
                    <span @click.stop="toggleKanbanSubtasks(task.id)">
                      <VIcon
                        size="small"
                        color="primary"
                        class=" tabler-subtask"
                      >
                        <VTooltip
                          activator="parent"
                          location="top"
                        >
                          <span class="text-xs">Subtasks</span>
                        </VTooltip>
                      </VIcon>
                      <span class="text-primary font-weight-bold">{{ task.subtasks.length }}</span>
                    </span>
                  </div>
                </div>

                <!-- Icon Section -->
                <div class="d-flex align-center">
                  <VIcon
                    :color="showKanbanTaskIcon === task.uuid ? 'primary' : 'white'"
                    variant="text"
                    rounded
                    class="me-1 tabler-plus"
                    size="small"
                    @click.stop="activateQuickKanbanSubTask(task.id)"
                  >
                    <VTooltip
                      activator="parent"
                      location="top"
                    >
                      <span class="text-xs">Add Subtask</span>
                    </VTooltip>
                  </VIcon>
                  <VIcon
                    :color="showKanbanTaskIcon === task.uuid ? 'primary' : 'white'"
                    variant="text"
                    rounded
                    class="tabler-edit me-1"
                    size="small"
                    @click.stop="startEditing(task)"
                  >
                    <VTooltip
                      activator="parent"
                      location="top"
                    >
                      <span class="text-xs">Edit Task</span>
                    </VTooltip>
                  </VIcon>
                  <VIcon
                    :color="showKanbanTaskIcon === task.uuid ? 'primary' : 'white'"
                    variant="text"
                    rounded
                    class="tabler-trash"
                    size="small"
                    @click.stop="deleteTask(task)"
                  >
                    <VTooltip
                      activator="parent"
                      location="top"
                    >
                      <span class="text-xs">Delete Task</span>
                    </VTooltip>
                  </VIcon>
                </div>
              </div>
            </VCard>
            <div v-if="isExpandedKanbanSubTasks[task.id]">
              <VCard
                v-for="(subtask) in task.subtasks"
                :key="subtask.id"
                class="mt-1 mb-2 ms-4 task-card px-3 py-2"
                @click="startEditing(subtask)"
                @mouseenter="showKanbanTaskIcon = subtask.uuid"
                @mouseleave="showKanbanTaskIcon = null"
              >
                <div class="cursor-pointer">
                  <VIcon
                    icon="tabler-playstation-circle"
                    color="primary"
                    size="x-small"
                  />
                  <span class="ms-1">{{ subtask.name }}</span>
                </div>
                <div class="d-flex justify-space-between align-center">
                  <div class="float-left">
                    <VChip
                      v-if="subtask.due_date"
                      color="error"
                      size="x-small"
                    >
                      {{ formatDate(subtask.due_date) }}
                      <VTooltip
                        activator="parent"
                        location="top"
                      >
                        <span>Subtask is due on {{ formatDate(subtask.due_date) }}</span>
                      </VTooltip>
                    </VChip>
                  </div>
                  <div class="float-right">
                    <VIcon
                      :color="showKanbanTaskIcon === subtask.uuid ? 'primary' : 'white'"
                      variant="text"
                      rounded
                      class="tabler-edit me-1"
                      size="small"
                      @click.stop="startEditing(subtask)"
                    >
                      <VTooltip
                        activator="parent"
                        location="top"
                      >
                        <span class="text-xs">Edit Subtask</span>
                      </VTooltip>
                    </VIcon>
                    <VIcon
                      :color="showKanbanTaskIcon === subtask.uuid ? 'primary' : 'white'"
                      variant="text"
                      rounded
                      class="tabler-trash"
                      size="small"
                      @click.stop="deleteTask(subtask)"
                    >
                      <VTooltip
                        activator="parent"
                        location="top"
                      >
                        <span class="text-xs">Delete Subtask</span>
                      </VTooltip>
                    </VIcon>
                  </div>
                </div>
              </VCard>
            </div>
            <div class="justify-center mt-2">
              <VCard
                v-if="showAddKanbanSubTaskField == task.id"
                class="ms-4 mb-2"
              >
                <div class="d-flex align-center px-3">
                  <VIcon
                    icon="tabler-playstation-circle"
                    color="primary"
                    size="small"
                  />
                  <VTextField
                    :ref="el => quickKanbanSubTaskInput[task.id] = el"
                    v-model="quickKanbanSubTaskName"
                    class="kanban-input"
                    variant="plan"
                    placeholder="Subtask Name"
                    @keyup.enter="saveKanbanSubTask(list.uuid, task.id)"
                  />
                </div>
                <div class="float-right px-3 mb-2">
                  <VIcon
                    class="tabler-calendar me-1"
                    color="primary"
                    size="small"
                  >
                    <VTooltip
                      activator="parent"
                      location="top"
                    >
                      <span class="text-xs">Select Due Date</span>
                    </VTooltip>
                    <AppDateTimePicker v-model="dueDate" />
                  </VIcon>
                  <VIcon
                    color="success"
                    class="tabler-check me-1"
                    @click="saveKanbanSubTask(list.uuid, task.id)"
                  >
                    <VTooltip
                      activator="parent"
                      location="top"
                    >
                      <span class="text-xs">Save</span>
                    </VTooltip>
                  </VIcon>
                  <VIcon
                    color="error"
                    class="tabler-x"
                    @click="cancelQuickKanbanSubTask(task.id)"
                  >
                    <VTooltip
                      activator="parent"
                      location="top"
                    >
                      <span class="text-xs">Cancel</span>
                    </VTooltip>
                  </VIcon>
                </div>
              </VCard>
            </div>
          </div>
        </VueDraggableNext>
        <div class="justify-center mt-2">
          <VCard v-if="showAddKanbanListTaskInput === list.id">
            <div class="d-flex align-center px-3 py-2">
              <VIcon
                icon="tabler-playstation-circle"
                color="primary"
                size="small"
              />
              <VTextField
                :ref="el => kanbanListTaskInputRef[list.id] = el"
                v-model="kanbanListTaskName"
                class="kanban-input"
                variant="plan"
                placeholder="Task Name"
                @keyup.enter="saveKanbanListTask(list)"
              />
            </div>
            <div class="float-right px-3 mb-2">
              <VIcon
                class="tabler-calendar me-1"
                color="primary"
                size="small"
              >
                <VTooltip
                  activator="parent"
                  location="top"
                >
                  <span class="text-xs">Select Due Date</span>
                </VTooltip>
                <AppDateTimePicker v-model="dueDate" />
              </VIcon>
              <VIcon
                color="success"
                class="tabler-check me-1"
                @click="saveKanbanListTask(list)"
              >
                <VTooltip
                  activator="parent"
                  location="top"
                >
                  <span class="text-xs">Save</span>
                </VTooltip>
              </VIcon>
              <VIcon
                color="error"
                class="tabler-x"
                @click="cancelKanbanListTask(list)"
              >
                <VTooltip
                  activator="parent"
                  location="top"
                >
                  <span class="text-xs">Cancel</span>
                </VTooltip>
              </VIcon>
            </div>
          </VCard>
          <div v-else>
            <VBtn
              color="primary"
              variant="plain"
              size="small"
              @click="activateAddKanbanListTask(list)"
            >
              <VIcon icon="tabler-plus" />
              Add Task
            </VBtn>
          </div>
        </div>
      </VCol>
      <!-- Add column button -->
      <VCol cols="4">
        <div class="justify-center">
          <VBtn
            color="primary"
            variant="plain"
            @click="isAddListDialogVisible = !isAddListDialogVisible"
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
    :fetch-project-lists="fetchProjectLists"
    :get-load-status="getLoadStatus"
  />
</template>

<script setup="js">
import moment from 'moment'
import Swal from 'sweetalert2'
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
import { useTheme } from 'vuetify'

const toast = useToast()
const vuetifyTheme = useTheme()
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

const showAddSubtaskIcon= ref(null)
const showKanbanTaskIcon= ref(null)
const showAddTaskField = ref(false)
const showAddListTaskField = ref([])
const showAddSubTaskField = ref([])
const showAddKanbanSubTaskField = ref(null)
const quickListTaskInput = ref([])
const quickListTaskName = ref([])
const quickSubTaskInput = ref([])
const quickSubTaskName = ref([])
const quickKanbanSubTaskInput = ref([])
const quickKanbanSubTaskName = ref('')
const isExpandedSubTasks = ref([])
const isExpandedKanbanSubTasks = ref([])
const quickTaskName = ref('')
const quickTaskInput = ref(null)
const dueDate = ref(null)
const isListEditing = ref([])
const editListTitleInput = ref([])
const editListTitle = ref(null)
const showAddKanbanListTaskInput = ref(null)
const kanbanListTaskName = ref(null)
const kanbanListTaskInputRef = ref([])
const isDragging = ref(false)

const isLoading = ref(false)

const projectId = computed(() => router.currentRoute.value.params.id)

const formatDate = date => moment(date).format('MMM DD, YYYY')

onBeforeMount(async () => {
  await fetchProjectTasks()
  await fetchProjectLists()
  await fetchProjectDetails()
})

onMounted(() => {
  const searchParams = new URLSearchParams(window.location.search)
  const savedViewType = searchParams.get('view')
  if (savedViewType && ['list', 'grid'].includes(savedViewType)) {
    viewType.value = savedViewType
  }
})

watch(viewType, newValue => {
  router.push({ query: { view: newValue } })
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
    await projectTaskStore.getUnlistedTasks(projectId.value)
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

function activateQuickKanbanSubTask(index) {
  showAddKanbanSubTaskField.value = index
  isExpandedKanbanSubTasks.value[index] = true
  nextTick(() => {
    const inputRef = quickKanbanSubTaskInput.value[index]
    if (inputRef && inputRef.focus) {
      inputRef.focus()
    }
  })
}

function toggleKanbanSubtasks(index)
{
  isExpandedKanbanSubTasks.value[index] = !isExpandedKanbanSubTasks.value[index]
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

async function addQuickListTask(list_uuid, index) {
  const taskName = quickListTaskName.value[index]

  if (taskName.trim() === '') {
    toast.error('Task name cannot be empty.')

    return
  }

  try {
    activateQuickListTask(index)

    const newTaskListDetails = {
      name: taskName.trim(),
      due_date: dueDate.value,
      list_uuid: list_uuid,
    }

    await listTaskStore.create(newTaskListDetails)
    quickListTaskName.value[index] = ''
    dueDate.value = null
    toast.success('Task added successfully', { timeout: 1000 })
    fetchProjectLists()
  } catch (error) {
    toast.error('Failed to add task:', error)
  }
}

async function addQuickSubTask(list_uuid, index) {
  const taskName = quickSubTaskName.value[index]

  if (taskName.trim() === '') {
    toast.error('Task name cannot be empty.')

    return
  }

  try {
    activateQuickSubTask(index)

    const newTaskListDetails = {
      name: taskName.trim(),
      due_date: dueDate.value,
      list_uuid: list_uuid,
      parent_id: index,
    }

    await listTaskStore.create(newTaskListDetails)
    quickSubTaskName.value[index] = ''
    dueDate.value = null
    toast.success('Task added successfully', { timeout: 1000 })
    fetchProjectLists()
  } catch (error) {
    toast.error('Failed to add task:', error)
  }
}

async function saveKanbanSubTask(list_uuid, index) {
  const taskName = quickKanbanSubTaskName.value

  if (taskName.trim() === '') {
    toast.error('Task name cannot be empty.')

    return
  }

  try {
    activateQuickKanbanSubTask(index)

    const newTaskListDetails = {
      name: taskName.trim(),
      due_date: dueDate.value,
      list_uuid: list_uuid,
      parent_id: index,
    }

    await listTaskStore.create(newTaskListDetails)
    quickKanbanSubTaskName.value = ''
    dueDate.value = null
    toast.success('Task added successfully', { timeout: 1000 })
    fetchProjectLists()
  } catch (error) {
    toast.error('Failed to add task:', error)
  }
}

async function saveKanbanListTask(list) {
  const taskName = kanbanListTaskName.value

  if (taskName.trim() === '') {
    toast.error('Task name cannot be empty.')

    return
  }

  try {
    activateAddKanbanListTask(list)

    const newTaskListDetails = {
      name: taskName.trim(),
      due_date: dueDate.value,
      list_uuid: list.uuid,
    }

    await listTaskStore.create(newTaskListDetails)
    kanbanListTaskName.value = ''
    dueDate.value = null
    toast.success('Task added successfully', { timeout: 1000 })
    fetchProjectLists()
  } catch (error) {
    toast.error('Failed to add task:', error)
  }
}

function cancelQuickTask() {
  showAddTaskField.value = false
  quickTaskName.value = ''
}

function cancelQuickListTask(index) {
  showAddListTaskField.value[index] = false
  quickListTaskName.value[index] = ''
}

function cancelQuickSubTask(index) {
  showAddSubTaskField.value[index] = false
  quickSubTaskName.value[index] = ''
}

function cancelQuickKanbanSubTask(index) {
  showAddKanbanSubTaskField.value = null
  quickKanbanSubTaskName.value = ''
}

function startEditing(task) {
  editingTask.value = { ...task, project_uuid: projectId.value }
  isEditTaskDrawerOpen.value = true
}

function activateAddKanbanListTask(list) {
  showAddKanbanListTaskInput.value = list.id
  nextTick(() => {
    const inputRef = kanbanListTaskInputRef.value[list.id]
    if (inputRef && inputRef.focus) {
      inputRef.focus()
    }
  })
}

function cancelKanbanListTask(list) {
  showAddKanbanListTaskInput.value = null
  kanbanListTaskName.value = ""
}

async function deleteTask(task) {
  try {
    task.isDeleting = true

    const taskWithProjectId = { ...task, project_uuid: projectId.value }

    await projectTaskStore.delete(taskWithProjectId)
    toast.success('Task deleted successfully', { timeout: 1000 })
    task.isDeleting = false
    fetchProjectLists()
    fetchProjectTasks()
  } catch (error) {
    toast.error('Failed to delete task:', error)
  }
}

const deleteProjectList = async list => {
  try {
    const listWithProjectId = { ...list, project_uuid: projectId.value }

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
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
    })

    if (confirmDelete.isConfirmed) {

      const res = await projectListStore.delete(listWithProjectId)

      isLoading.value = true
      toast.success('Project list deleted successfully', { timeout: 1000 })
      await fetchProjectLists()
      isLoading.value = false
    }
  } catch (error) {
    toast.error('Failed to delete project list:', error)
  }
}

const getProjectTasks = computed(() => projectTaskStore.getProjectTasks)
const getProjectLists = computed(() => projectListStore.getProjectLists)
const project = computed(() => projectStore.getProject)

getProjectLists.value.forEach(() => {
  showAddListTaskField.value.push(false)
  quickListTaskInput.value.push(null)
  quickListTaskName.value.push('')
})

const getLoadStatus = computed(() => {
  return projectTaskStore.getLoadStatus
})

function toggleSubtasks(index){
  isExpandedSubTasks.value[index] = !isExpandedSubTasks.value[index]
  cancelQuickSubTask(index)
}

const headers = [
  { title: 'Task Name', key: 'name', sortable: false, width: '60%' },
  { title: 'Status', key: 'status',  sortable: false },
  { title: 'Due Date', key: 'due_date', sortable: false },
  { title: 'Start Date', key: 'created_at', sortable: false },
  { title: 'Action', key: 'action', sortable: false },
]


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
        uuid: list.uuid,
        project_uuid: projectId.value,
      }

      await projectListStore.update(editListDetails)
      toast.success('Title updated successfully', { timeout: 1000 })
      await fetchProjectLists()
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

const onDrop = (targetListId, event) => {
  const taskToMove = event.added ? event.added.element : event.removed.element
  if (!taskToMove) {
    toast.error('Task to move is undefined')

    return
  }

  const sourceList = getProjectLists.value.find(list => list.tasks.some(task => task.uuid === taskToMove.uuid))
  if (!sourceList) {
    toast.error('Source list not found')

    return
  }

  const targetList = getProjectLists.value.find(list => list.id === targetListId)
  if (!targetList) {
    toast.error('Target list not found')

    return
  }
  if (sourceList.id === targetListId) {
    const taskUpdateData = {
      list_id: targetList.id,
      list_tasks: targetList.tasks,
      uuid: taskToMove.uuid,
      name: taskToMove.name,
      project_uuid: projectId.value,
    }

    updateTaskOrder(taskUpdateData)
  }else{
    const taskUpdateData = {
      list_id: targetList.id,
      list_tasks: targetList.tasks[0],
      uuid: targetList.tasks[0].uuid,
      name: targetList.tasks[0].name,
      project_uuid: projectId.value,
    }

    updateTaskOrder(taskUpdateData)
  }
}

const updateTaskOrder = async taskUpdateData => {
  try {
    await projectTaskStore.updateTaskOrdering(taskUpdateData)
    await fetchProjectLists()
  } catch (error) {
    toast.error('Failed to update order')
  }
}

const expandedRows = ref([])

expandedRows.value[0] = true

const toggleRow = index => {
  expandedRows.value[index] = !expandedRows.value[index]
  cancelQuickListTask(index)
}

function onDragStart() {
  isDragging.value = true
}

function onDragEnd() {
  isDragging.value = false
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

.kanban-dropzone{
    overflow-y: auto;
    max-height: 468px;
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
    border-radius: 8px !important;
    font-size: 14px;
    height: auto;
    margin-right: 5px;
}

.task-card:hover {
    border: 1px solid #f69636;
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
</style>

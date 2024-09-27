<template>
  <!-- Toggle -->
  <Loader v-if="isLoading" />
  <VRow>
    <VCol
      cols="12"
      md="6"
      class="d-flex align-center"
    >
      <VBtnToggle
        v-model="viewType"
        class="d-toggle align-center-important"
        rounded="0"
      >
        <div class="d-flex justify-center align-center">
          <VAvatar
            :size="30"
            class="me-1"
            :image="sketch"
          />
          <h3 class="text-primary">
            {{ project?.title }}
          </h3>
        </div>
        <VIcon
          icon="tabler-list"
          class="me-1 ms-2"
          :class="{ 'bg-primary': viewType === 'list' }"
          @click="viewType = 'list'"
        />
        <VIcon
          icon="tabler-layout-grid"
          :class="{ 'bg-primary': viewType === 'grid' }"
          @click="viewType = 'grid'"
        />
        <VIcon
          icon="tabler-filter"
          class="bg-primary ms-2"
          @click="isFilterDrawerOpen = !isFilterDrawerOpen"
        />
        <span
          v-if="filtersApplied"
          class="filter-indicator"
          @click.stop="resetFilter"
        >
          <VTooltip bottom>
            <template #activator="{ props }">
              <VIcon
                color="error"
                icon="tabler-circle-x-filled"
                size="20"
                class="cursor-pointer"
                v-bind="props"
              />
            </template>
            <span>Reset Filter</span>
          </VTooltip>
        </span>
      </VBtnToggle>
    </VCol>
    <VCol
      cols="12"
      md="6"
    >
      <div class="d-flex flex-column flex-md-row align-center justify-end">
        <!-- Dropdown -->
        <AppAutocomplete
          v-if="viewType === 'grid'"
          v-model="selectedList"
          autocomplete="off"
          placeholder="Select List"
          :items="getProjectLists"
          item-title="name"
          item-value="uuid"
          class="mb-3 ms-16 mb-md-0 me-md-3"
          style="max-width:70%;"
        />

        <!-- Button and Dialog -->
        <VBtn
          icon
          color="td-hover"
          class="ma-2"
          size="small"
          rounded="pills"
          @click.prevent
        >
          <VIcon icon="tabler-dots" />
          <VMenu activator="parent">
            <VList>
              <VListItem
                value="add-list"
                @click="isAddListDialogVisible = true"
              >
                Add List
              </VListItem>
              <VListItem
                v-if="authStore.isAdmin"
                value="save-template"
                @click="isSaveTemplateModalOpen = true"
              >
                Save as Template
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
      </div>
    </VCol>
  </VRow>
  <VRow class="mt-5 pt-0 pb-0 align-center">
    <VCol
      cols="6"
      class="pt-0 ps-4 pb-0"
    >
      <h3>
        Manage Tasks
      </h3>
    </VCol>
  </VRow>


  <!-- List View -->
  <VRow
    v-if="viewType === 'list'"
    class="mt-1"
  >
    <VCol cols="12">
      <div v-if="getProjectLists? getProjectLists.length > 0 : 0">
        <VCard
          v-for="(list, index) in getProjectLists"
          :key="index"
          :class="`px-4 py-4 mt-2 ${expandedRows[index] ? '' : 'list-side-border'}`"
        >
          <VRow
            class="cursor-pointer"
            @click="toggleRow(index)"
          >
            <VCol cols="4">
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
                    v-if="!showAddListTaskField[index]"
                    color="primary"
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
              <VCol
                cols="8"
                class="d-flex justify-center align-items-center"
              >
                <div
                  class="text-center text-h6 mb-2"
                  style="position: absolute;top:15px;margin-left:20px;"
                >
                  <small>{{ list.total_tasks }}</small>
                </div>

                <div class="d-flex justify-between align-center w-100">
                  <div class="text-body-1 text-high-emphasis">
                    <small>{{ list.progress }}%</small>
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
                    <small>{{ list.completed_tasks }}</small>
                  </div>
                </div>
              </VCol>
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
                v-if="getProjectLists? getProjectLists.length > 1 : 0"
                icon
                size="small"
                :color="focusDeleteListId === list.id ? 'error' : 'grey-500'"
                @mouseenter="focusDeleteListId = list.id"
                @mouseleave="focusDeleteListId = null"
                @click.stop="deleteProjectList(list)"
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
                    @click="startEditing(item)"
                  >
                    <VIcon
                      v-if="item.subtasks && item.subtasks.length"
                      :icon="isExpandedSubTasks[item.id] ? 'tabler-chevron-down' : 'tabler-chevron-right'"
                      color="primary"
                      @click.stop="toggleSubtasks(item.id)"
                    />
                    <VIcon
                      v-if="item.is_bucks_allowed"
                      color="primary"
                      variant="text"
                      class="tabler-coin-filled me-1"
                      rounded
                    >
                      <VTooltip
                        activator="parent"
                        location="top"
                      >
                        <span class="text-xs">Bucks Task</span>
                      </VTooltip>
                    </VIcon>
                    <VIcon
                      v-else
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
                      @click.stop="startEditing(item)"
                    >
                      <VTooltip
                        activator="parent"
                        location="top"
                      >
                        <span class="text-xs">Edit Task</span>
                      </VTooltip>
                    </VIcon>
                    <span
                      v-if="item?.files_count > 0"
                      class="text-sm font-weight-bold cursor-pointer ms-1"
                      @click.stop="startEditing(item)"
                    >
                      <VIcon
                        size="small"
                        class="tabler-paperclip"
                        :color="showAddSubtaskIcon === item.uuid ? 'primary' : 'white'"
                      >
                        <VTooltip
                          activator="parent"
                          location="top"
                        >
                          <span class="text-xs">{{ item?.files_count }} Attachments</span>
                        </VTooltip>
                      </VIcon>
                      <span :class="showAddSubtaskIcon === item.uuid ? 'text-primary font-weight-bold' : 'text-white font-weight-bold'">
                        {{ item?.files_count }}
                      </span>
                    </span>
                  </td>
                  <td>
                    <VMenu
                      v-model="menu[item.id]"
                      :close-on-content-click="false"
                      transition="scale-transition"
                      offset-y
                    >
                      <template #activator="{ props }">
                        <div class="v-avatar-group demo-avatar-group">
                          <div
                            v-for="(user, index) in item.assignees.slice(0, 2)"
                            :key="index"
                          >
                            <VAvatar :size="30">
                              <VAvatar
                                size="25"
                                class="text-white bg-primary"
                                variant="tonal"
                                v-bind="props"
                              >
                                <small>{{ avatarText(user.name_first + ' ' + user.name_last) }}</small>
                              </VAvatar>
                              <VTooltip
                                activator="parent"
                                location="top"
                              >
                                <span>{{ user.name_first + ' ' + user.name_last }}</span>
                              </VTooltip>
                            </VAvatar>
                          </div>
                          <VAvatar
                            v-if="item.assignees.length > 2"
                            :size="30"
                            v-bind="props"
                          >
                            <VAvatar
                              size="25"
                              class="text-white bg-primary"
                              variant="tonal"
                              v-bind="props"
                            >
                              <small>+{{ item.assignees.length - 2 }}</small>
                            </VAvatar>
                          </VAvatar>
                        </div>

                        <VChip
                          v-if="!item.assignees.length"
                          color=""
                          size="small"
                          v-bind="props"
                          class="cursor-pointer"
                          rounded="md"
                        >
                          <VIcon
                            icon="tabler-user-plus"
                            size="small"
                            class="text-primary"
                          />
                        </VChip>
                      </template>
                      <VList class="assignee-list">
                        <VTextField
                          v-model="searchUser"
                          label="Search"
                          placeholder="Search name or email"
                          class="mx-4"
                          dense
                          hide-details
                          autofocus
                          @input="onMemberSearchInpt(item)"
                        />
                        <template v-if="filteredUsers.length > 0 && !usersLoading && searchUser">
                          <VListItem
                            v-for="user in filteredUsers"
                            :key="user.id"
                            class="assignee-list-item"
                            @click="assignTask(user, item)"
                          >
                            <VListItemAvatar>
                              <VAvatar
                                size="34"
                                class="text-white bg-primary"
                                variant="tonal"
                              >
                                <span>{{ avatarText(user.name_first + ' ' + user.name_last) }}</span>
                              </VAvatar>
                            </VListItemAvatar>
                            <VListItemTitle class="ms-2">
                              {{ user.name_first + ' ' + user.name_last }}
                              <p class="text-xs mb-0">
                                {{ user.role }}
                              </p>
                            </VListItemTitle>
                          </VListItem>
                        </template>
                        <template v-else>
                          <div
                            v-if="usersLoading"
                            class="members-loading"
                          >
                            <div class="dots-loader" />
                          </div>
                          <div v-else>
                            <div v-if="item.assignees.length > 0 && !searchUser">
                              <VListItem
                                v-for="user in item.assignees"
                                :key="user.id"
                                class="assignee-list-item"
                                :disabled="isAssigneeRemoving"
                                @mouseenter="showDeleteIcon = user.id"
                                @mouseleave="showDeleteIcon = null"
                              >
                                <VListItemAvatar>
                                  <VAvatar
                                    size="34"
                                    class="text-white bg-primary"
                                    variant="tonal"
                                  >
                                    <span>{{ avatarText(user.name_first + ' ' + user.name_last) }}</span>
                                  </VAvatar>
                                </VListItemAvatar>
                                <VListItemTitle class="ms-2">
                                  {{ user.name_first + ' ' + user.name_last }}
                                  <p class="text-xs mb-0">
                                    {{ user.role }}
                                  </p>
                                </VListItemTitle>
                                <VIcon
                                  v-if="showDeleteIcon === user.id"
                                  icon="tabler-circle-x-filled"
                                  color="error"
                                  class="cursor-pointer member-delete-icon"
                                  @click.stop="removeAssignee(user, item)"
                                />
                              </VListItem>
                            </div>
                            <div v-else>
                              <VListItem class="assignee-search-list-item">
                                <VListItemTitle>
                                  <small v-if="searchUser && filteredUsers.length === 0 && !usersLoading">No Member found</small>
                                  <small v-else>Search Team Members...</small>
                                </VListItemTitle>
                              </VListItem>
                            </div>
                          </div>
                        </template>
                      </VList>
                    </VMenu>
                  </td>
                  <td>
                    <VMenu
                      v-model="statusMenu[item.id]"
                      :close-on-content-click="false"
                      transition="scale-transition"
                      offset-y
                    >
                      <template #activator="{ props }">
                        <VChip
                          :color="getStatusColor(item.status)"
                          size="small"
                          v-bind="props"
                          class="cursor-pointer d-flex justify-center"
                          style="width: 105px;"
                        >
                          {{ item.status.name }}
                        </VChip>
                      </template>
                      <VList>
                        <VListItem
                          v-for="status in getStatuses"
                          :key="status.name"
                          @click="updateStatus(item, status)"
                        >
                          <VListItemContent>
                            <VListItemTitle :class="`text-${status.color}`">
                              {{ status.name }}
                            </VListItemTitle>
                          </VListItemContent>
                        </VListItem>
                      </VList>
                    </VMenu>
                  </td>
                  <td>
                    <VMenu
                      v-model="dueDateMenu[item.id]"
                      :close-on-content-click="false"
                      transition="scale-transition"
                      offset-y
                    >
                      <template #activator="{ props }">
                        <VChip
                          class="cursor-pointer"
                          style="background: #d7e3fb; color: #3f51b5; width: 115px;"
                          size="small"
                          v-bind="props"
                        >
                          <VIcon
                            icon="tabler-calendar"
                            class="me-1"
                            left
                          />
                          <span v-if="item.due_date">{{ formatDate(item.due_date) }}</span>
                          <span v-else><small>Set Due Date</small></span>
                        </VChip>
                      </template>
                      <VDatePicker
                        v-model="dueDate"
                        :config="{ dateFormat: 'F j, Y' }"
                        @update:model-value="closeDueDateMenu(item)"
                      />
                    </VMenu>
                  </td>
                  <td>
                    <VMenu
                      v-model="inputTimeRef[item.id]"
                      transition="scale-transition"
                      offset-y
                      :close-on-content-click="false"
                      class="p-0"
                    >
                      <template #activator="{ props }">
                        <VBadge
                          v-if="item.is_bucks_allowed && (!item.est_time || item.est_time == '')"
                          dot
                          color="primary"
                        >
                          <VChip
                            color="primary"
                            size="small"
                            v-bind="props"
                            class="cursor-pointer"
                            style="width: 110px;"
                            @click="setInputTime(item)"
                          >
                            <VIcon
                              size="x-small"
                              class="tabler-clock me-1"
                            />
                            <small>{{ item.est_time || 'Set EST Time' }}</small>
                          </VChip>
                        </VBadge>
                        <VChip
                          v-else
                          color="primary"
                          size="small"
                          v-bind="props"
                          class="cursor-pointer"
                          style="width: 110px;"
                          @click="setInputTime(item)"
                        >
                          <VIcon
                            size="x-small"
                            class="tabler-clock me-1"
                          />
                          <small>{{ item.est_time || 'Set EST Time' }}</small>
                        </VChip>
                      </template>
                      <VList class="pt-0">
                        <VListItem class="time-field-list">
                          <AppTextField
                            v-model="inputHoursRef[item.id]"
                            v-mask="'##'"
                            label="Hours"
                            type="number"
                            suffix="h"
                            placeholder="00"
                            min="0"
                            class="time-field me-2 no-arrows"
                            density="compact"
                            autofocus
                            @keydown.enter="saveTime(item)"
                          />
                          <AppTextField
                            v-model="inputMinutesRef[item.id]"
                            v-mask="'##'"
                            label="Minutes"
                            type="number"
                            suffix="m"
                            placeholder="00"
                            min="0"
                            max="59"
                            class="time-field no-arrows"
                            density="compact"
                            @keydown.enter="saveTime(item)"
                          />
                        </VListItem>
                        <VListItem class="me-3">
                          <VBtn
                            color="primary"
                            size="x-small"
                            class="me-2"
                            :disabled="isSavingTime"
                            @click="saveTime(item)"
                          >
                            <span v-if="isSavingTime">
                              <VProgressCircular
                                :size="16"
                                width="3"
                                indeterminate
                              />
                              Loading...
                            </span>
                            <span v-else>
                              Save
                            </span>
                          </VBtn>
                          <VBtn
                            color="secondary"
                            size="x-small"
                            @click="inputTimeRef[item.id] = false"
                          >
                            Cancel
                          </VBtn>
                        </VListItem>
                      </VList>
                    </VMenu>
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
                    :class="{ 'bg-td-hover': showAddSubtaskIcon === subtask.uuid }"
                    @mouseenter="showAddSubtaskIcon = subtask.uuid"
                    @mouseleave="showAddSubtaskIcon = null"
                  >
                    <td
                      class="cursor-pointer"
                      @click.stop="startEditing(subtask)"
                    >
                      <VIcon
                        v-if="subtask.is_bucks_allowed"
                        color="primary"
                        variant="text"
                        size="small"
                        class="tabler-coin-filled ms-6 me-2"
                        rounded
                      >
                        <VTooltip
                          activator="parent"
                          location="top"
                        >
                          <span class="text-xs">Bucks Task</span>
                        </VTooltip>
                      </VIcon>
                      <VIcon
                        v-else
                        class="tabler-playstation-circle"
                        size="x-small"
                        color="primary ms-6 me-2"
                      />
                      <span class="text-grey-600">{{ subtask.name.length > 50 ? subtask.name.substring(0, 50) + '...' : subtask.name }}</span>
                      <span
                        v-if="subtask?.files_count > 0"
                        class="text-sm font-weight-bold cursor-pointer ms-1"
                        @click.stop="startEditing(subtask)"
                      >
                        <VIcon
                          size="small"
                          color="primary"
                          class="tabler-paperclip"
                        >
                          <VTooltip
                            activator="parent"
                            location="top"
                          >
                            <span class="text-xs">{{ subtask?.files_count }} Attachments</span>
                          </VTooltip>
                        </VIcon>
                        <span class="text-primary font-weight-bold">{{ subtask?.files_count }}</span>
                      </span>
                    </td>
                    <td>
                      <VMenu
                        v-model="menu[subtask.id]"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                      >
                        <template #activator="{ props }">
                          <div class="v-avatar-group demo-avatar-group">
                            <div
                              v-for="(user, index) in subtask.assignees.slice(0, 2)"
                              :key="index"
                            >
                              <VAvatar :size="30">
                                <VAvatar
                                  size="25"
                                  class="text-white bg-primary"
                                  variant="tonal"
                                  v-bind="props"
                                >
                                  <small>{{ avatarText(user.name_first + ' ' + user.name_last) }}</small>
                                </VAvatar>
                                <VTooltip
                                  activator="parent"
                                  location="top"
                                >
                                  <span>{{ user.name_first + ' ' + user.name_last }}</span>
                                </VTooltip>
                              </VAvatar>
                            </div>
                            <VAvatar
                              v-if="subtask.assignees.length > 2"
                              :size="30"
                              v-bind="props"
                            >
                              <VAvatar
                                size="25"
                                class="text-white bg-primary"
                                variant="tonal"
                                v-bind="props"
                              >
                                <small>+{{ subtask.assignees.length - 2 }}</small>
                              </VAvatar>
                            </VAvatar>
                          </div>

                          <VChip
                            v-if="!subtask.assignees.length"
                            color=""
                            size="small"
                            v-bind="props"
                            class="cursor-pointer"
                            rounded="md"
                          >
                            <VIcon
                              icon="tabler-user-plus"
                              size="small"
                              class="text-primary"
                            />
                          </VChip>
                        </template>
                        <VList class="assignee-list">
                          <VTextField
                            v-model="searchUser"
                            label="Search"
                            placeholder="Search name or email"
                            class="mx-4"
                            dense
                            hide-details
                            autofocus
                            @input="onMemberSearchInpt(subtask)"
                          />
                          <template v-if="filteredUsers.length > 0 && !usersLoading && searchUser">
                            <VListItem
                              v-for="user in filteredUsers"
                              :key="user.id"
                              class="assignee-list-item"
                              @click="assignTask(user, subtask)"
                            >
                              <VListItemAvatar>
                                <VAvatar
                                  size="34"
                                  class="text-white bg-primary"
                                  variant="tonal"
                                >
                                  <span>{{ avatarText(user.name_first + ' ' + user.name_last) }}</span>
                                </VAvatar>
                              </VListItemAvatar>
                              <VListItemTitle class="ms-2">
                                {{ user.name_first + ' ' + user.name_last }}
                                <p class="text-xs mb-0">
                                  {{ user.role }}
                                </p>
                              </VListItemTitle>
                            </VListItem>
                          </template>
                          <template v-else>
                            <div
                              v-if="usersLoading"
                              class="members-loading"
                            >
                              <div class="dots-loader" />
                            </div>
                            <div v-else>
                              <div v-if="subtask.assignees.length > 0 && !searchUser">
                                <VListItem
                                  v-for="user in subtask.assignees"
                                  :key="user.id"
                                  class="assignee-list-item"
                                  :disabled="isAssigneeRemoving"
                                  @mouseenter="showDeleteIcon = user.id"
                                  @mouseleave="showDeleteIcon = null"
                                >
                                  <VListItemAvatar>
                                    <VAvatar
                                      size="34"
                                      class="text-white bg-primary"
                                      variant="tonal"
                                    >
                                      <span>{{ avatarText(user.name_first + ' ' + user.name_last) }}</span>
                                    </VAvatar>
                                  </VListItemAvatar>
                                  <VListItemTitle class="ms-2">
                                    {{ user.name_first + ' ' + user.name_last }}
                                    <p class="text-xs mb-0">
                                      {{ user.role }}
                                    </p>
                                  </VListItemTitle>
                                  <VIcon
                                    v-if="showDeleteIcon === user.id"
                                    icon="tabler-circle-x-filled"
                                    color="error"
                                    class="cursor-pointer member-delete-icon"
                                    @click.stop="removeAssignee(user, subtask)"
                                  />
                                </VListItem>
                              </div>
                              <div v-else>
                                <VListItem class="assignee-search-list-item">
                                  <VListItemTitle>
                                    <small v-if="searchUser && filteredUsers.length === 0 && !usersLoading">No Member found</small>
                                    <small v-else>Search Team Members...</small>
                                  </VListItemTitle>
                                </VListItem>
                              </div>
                            </div>
                          </template>
                        </VList>
                      </VMenu>
                    </td>
                    <td>
                      <VMenu
                        v-model="statusMenu[subtask.id]"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                      >
                        <template #activator="{ props }">
                          <VChip
                            :color="getStatusColor(subtask.status)"
                            size="small"
                            v-bind="props"
                            class="cursor-pointer d-flex justify-center"
                            style="width: 105px;"
                          >
                            {{ subtask.status.name }}
                          </VChip>
                        </template>
                        <VList>
                          <VListItem
                            v-for="status in getStatuses"
                            :key="status.name"
                            @click="updateStatus(subtask, status)"
                          >
                            <VListItemContent>
                              <VListItemTitle :class="`text-${status.color}`">
                                {{ status.name }}
                              </VListItemTitle>
                            </VListItemContent>
                          </VListItem>
                        </VList>
                      </VMenu>
                    </td>
                    <td>
                      <VMenu
                        v-model="dueDateMenu[subtask.id]"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                      >
                        <template #activator="{ props }">
                          <VChip
                            class="cursor-pointer"
                            style="background: #d7e3fb; color: #3f51b5; width: 115px;"
                            size="small"
                            v-bind="props"
                          >
                            <VIcon
                              icon="tabler-calendar"
                              class="me-1"
                              left
                            />
                            <span v-if="subtask.due_date">{{ formatDate(subtask.due_date) }}</span>
                            <span v-else><small>Set Due Date</small></span>
                          </VChip>
                        </template>
                        <VDatePicker
                          v-model="dueDate"
                          :config="{ dateFormat: 'F j, Y' }"
                          @update:model-value="closeDueDateMenu(subtask)"
                        />
                      </VMenu>
                    </td>
                    <td>
                      <VMenu
                        v-model="inputTimeRef[subtask.id]"
                        transition="scale-transition"
                        offset-y
                        :close-on-content-click="false"
                        class="p-0"
                      >
                        <template #activator="{ props }">
                          <VBadge
                            v-if="subtask.is_bucks_allowed && (!subtask.est_time || subtask.est_time == '')"
                            dot
                            color="primary"
                          >
                            <VChip
                              color="primary"
                              size="small"
                              v-bind="props"
                              class="cursor-pointer"
                              style="width: 110px;"
                              @click="setInputTime(subtask)"
                            >
                              <VIcon
                                size="x-small"
                                class="tabler-clock me-1"
                              />
                              <small>{{ subtask.est_time || 'Set EST Time' }}</small>
                            </VChip>
                          </VBadge>
                          <VChip
                            v-else
                            color="primary"
                            size="small"
                            v-bind="props"
                            class="cursor-pointer"
                            style="width: 110px;"
                            @click="setInputTime(subtask)"
                          >
                            <VIcon
                              size="x-small"
                              class="tabler-clock me-1"
                            />
                            <small>{{ subtask.est_time || 'Set EST Time' }}</small>
                          </VChip>
                        </template>
                        <VList class="pt-0">
                          <VListItem class="time-field-list">
                            <AppTextField
                              v-model="inputHoursRef[subtask.id]"
                              v-mask="'##'"
                              label="Hours"
                              type="number"
                              suffix="h"
                              placeholder="00"
                              min="0"
                              class="time-field me-2 no-arrows"
                              density="compact"
                              autofocus
                              @keydown.enter="saveTime(subtask)"
                            />
                            <AppTextField
                              v-model="inputMinutesRef[subtask.id]"
                              label="Minutes"
                              type="number"
                              suffix="m"
                              placeholder="00"
                              min="0"
                              max="59"
                              class="time-field no-arrows"
                              density="compact"
                              @keydown.enter="saveTime(subtask)"
                            />
                          </VListItem>
                          <VListItem class="me-3">
                            <VBtn
                              color="primary"
                              size="x-small"
                              class="me-2"
                              :disabled="isSavingTime"
                              @click="saveTime(subtask)"
                            >
                              <span v-if="isSavingTime">
                                <VProgressCircular
                                  :size="16"
                                  width="3"
                                  indeterminate
                                />
                                Loading...
                              </span>
                              <span v-else>
                                Save
                              </span>
                            </VBtn>
                            <VBtn
                              color="secondary"
                              size="x-small"
                              @click="inputTimeRef[subtask.id] = false"
                            >
                              Cancel
                            </VBtn>
                          </VListItem>
                        </VList>
                      </VMenu>
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
  </VRow>
  <!-- Kanban View -->
  <div v-if="viewType === 'grid'">
    <div
      v-for="(list, key) in filteredProjectLists"
      :key="key"
      class="mt-3"
    >
      <!--
        <p class="text-h6">
        {{ list.name }} ({{ list.tasks ? list.tasks.length : 0 }})
        </p>
      -->
      <VRow
        class="kanban-columns"
        no-gutters
      >
        <VCol
          v-for="(status, index) in getStatuses"
          :key="index"
          cols="4"
          class="kanban-column"
        >
          <VCard class="mb-5">
            <VCardTitle class="bg-primary">
              <div class="d-flex align-center">
                <span class="text-h6 text-white">{{ status.name }} ({{ list.tasks.filter((task) => task.status.id == status.id).length }})</span>
              </div>
            </VCardTitle>
          </VCard>
          <VueDraggableNext
            class="kanban-dropzone light"
            :list="list.tasks"
            group="tasks"
            @change="onDrop(status.id, $event)"
            @start="onDragStart"
            @end="onDragEnd"
          >
            <div
              v-for="(task) in list.tasks.filter((task) => task.status.id == status.id)"
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
                            <span class="text-xs">{{ task.subtasks.length }} Subtasks</span>
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
                      @keyup.enter="saveKanbanSubTask(list.uuid, task.id, status.id)"
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
                      @click="saveKanbanSubTask(list.uuid, task.id, status.id)"
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
            <VCard v-if="showAddKanbanListTaskInput === status.id">
              <div class="d-flex align-center px-3 py-2">
                <VIcon
                  icon="tabler-playstation-circle"
                  color="primary"
                  size="small"
                />
                <VTextField
                  :ref="el => kanbanListTaskInputRef[status.id] = el"
                  v-model="kanbanListTaskName"
                  class="kanban-input"
                  variant="plan"
                  placeholder="Task Name"
                  @keyup.enter="saveKanbanListTask(list, status.id)"
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
                  @click="saveKanbanListTask(list, status.id)"
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
                  @click="cancelKanbanListTask(status)"
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
                @click="activateAddKanbanListTask(status)"
              >
                <VIcon icon="tabler-plus" />
                Add Task
              </VBtn>
            </div>
          </div>
        </VCol>
      </VRow>
    </div>
  </div>
  <EditTaskDrawer
    v-model:is-edit-task-drawer-open="isEditTaskDrawerOpen"
    :fetch-project-tasks="fetchProjectTasks"
    :editing-task="editingTask"
    :fetch-project-lists="fetchProjectLists"
    :users-assigned-bucks="usersAssignedBucks"
    :get-load-status="getLoadStatus"
  />
  <FilterTaskDrawer
    v-model:is-filter-drawer-open="isFilterDrawerOpen"
    :selected-project="projectId"
    :assignees-list="assigneesList"
    :get-load-status="getLoadStatus"
    @apply-filters="fetchProjectLists"
  />
  <SaveTemplateModal
    v-model:is-save-template-modal-open="isSaveTemplateModalOpen"
    :selected-project="projectId"
  />
</template>

<script setup="js">
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import moment from 'moment'
import Swal from 'sweetalert2'
import NoTaskInList from '@images/darby/tasks_list.svg?raw'
import EditTaskDrawer from '@/pages/projects/web-designs/_partials/update-project-task-drawer.vue'
import FilterTaskDrawer from '@/pages/projects/web-designs/_partials/filter-task-drawer.vue'
import SaveTemplateModal from '@/pages/projects/web-designs/_partials/save-template-modal.vue'
import { computed, onBeforeMount, nextTick, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useAuthStore } from "@/store/auth"
import { useProjectStore } from "@/store/projects"
import { useProjectTaskStore } from "@/store/project_tasks"
import { useListTaskStore } from "@/store/list_tasks"
import { useStatusStore } from "@/store/status"
import { useUserStore } from "@/store/users"
import { useProjectListStore } from "@/store/project_lists"
import { useRouter } from 'vue-router'
import { VueDraggableNext } from 'vue-draggable-next'
import { useTheme } from 'vuetify'
import { VIcon } from 'vuetify/lib/components/index.mjs'
import { debounce, truncate } from 'lodash'
import sketch from '@images/icons/project-icons/sketch.png'
import Loader from "@/components/Loader.vue"

const toast = useToast()
const vuetifyTheme = useTheme()
const authStore = useAuthStore()
const projectStore = useProjectStore()
const projectTaskStore = useProjectTaskStore()
const listTaskStore = useListTaskStore()
const projectListStore = useProjectListStore()
const statusStore = useStatusStore()
const userStore = useUserStore()
const router = useRouter()
const usersAssignedBucks = ref(null)

const viewType = ref('list')

const editingTask = ref({})
const isEditTaskDrawerOpen = ref(false)
const isFilterDrawerOpen = ref(false)
const filtersApplied = ref(false)

const isSaveTemplateModalOpen = ref(false)

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
const searchQuery = ref([])
const isDragging = ref(false)
const statusMenu = ref([])
const dueDateMenu = ref([])
const selectedList = ref(null)
const inputTimeRef = ref([])
const inputHoursRef = ref([])
const inputMinutesRef = ref([])
const isSavingTime = ref(null)
const searchUser = ref('')
const usersLoading = ref(false)
const isAssigneeRemoving = ref(false)
const menu = ref([])
const selectedAssignee = ref(null)
const filteredUsers = ref([])
const showDeleteIcon = ref(null)
const focusDeleteListId = ref(null)

const isLoading = ref(false)

const projectId = computed(() => router.currentRoute.value.params.id)

const formatDate = date => moment(date).format('MM/DD/YYYY')

const validateMinutes = (event, item) => {
  const inputValue = event.target.value
  const minutes = parseInt(inputValue.split(' ')[1])
  if (isNaN(minutes) || minutes < 0 || minutes > 59) {
    const sanitizedValue = inputValue.replace(/\d{2}m/, '00m')

    event.target.value = sanitizedValue
    inputHours[item.id] = sanitizedValue
  }
}

onBeforeMount(async () => {
  isLoading.value = true
  await fetchProjectTasks()
  await fetchProjectLists()
  await fetchStatus()
  await fetchProjectDetails()
  await fetchAsigneesUsers()
  isLoading.value = false

  const searchParams = new URLSearchParams(window.location.search)
  const savedViewType = searchParams.get('view')
  if (savedViewType && ['list', 'grid'].includes(savedViewType)) {
    viewType.value = savedViewType
  }

  const savedType = searchParams.get('type')
  if (savedType && getProjectLists.value.some(list => list.uuid === savedType)) {
    selectedList.value = savedType
  }
  else{
    selectedList.value = getProjectLists.value[0].uuid
  }

  const selectedExpandedIndex = searchParams.get('expanded')

  selectedExpandedIndex ?  toggleRow(selectedExpandedIndex, true) : toggleRow(0, true)
})

watch([viewType, selectedList], ([newViewType, newSelectedList]) => {
  router.push({
    query: {
      view: newViewType,
      type: newSelectedList,
    },
  })
})

const fetchProjectDetails = async () => {
  try {
    await projectStore.show(projectId.value)
  } catch (error) {
    toast.error('Error fetching project details:', error)
  }
}

const fetchFiles = async taskUuid => {
  await projectTaskStore.fetchFiles(taskUuid)
}

const fetchStatus = async () => {
  try {
    await statusStore.getAll()
  } catch (error) {
    toast.error('Error fetching statuses:', error)
  }
}

const fetchProjectTasks = async () => {
  try {
    await projectTaskStore.getUnlistedTasks(projectId.value)
  } catch (error) {
    toast.error('Error fetching project tasks:', error)
  }
}

const fetchProjectLists = async (filters = {}) => {
  try {
    await projectListStore.getAll(projectId.value, filters)
    if (Object.keys(filters).length > 0) {
      filtersApplied.value = true
    } else {
      filtersApplied.value = false
    }
  } catch (error) {
    toast.error('Error fetching project lists:', error)
  }
}

const resetFilter = async () => {
  filtersApplied.value = false
  await fetchProjectLists()
}

const fetchAsigneesUsers = async () => {
  try {
    await userStore.getAllByProject(projectId.value)
  } catch (error) {
    toast.error('Error fetching assigneesList:', error)
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

async function saveKanbanSubTask(list_uuid, index, status) {
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
      status: status,
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

async function saveKanbanListTask(list, status) {
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
      status: status,
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
  fetchFiles(task.uuid)
  editingTask.value = { ...task, project_uuid: projectId.value }
  usersAssignedBucks.value = task.assignees_bucks
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
      confirmButtonColor: "#a12592",
      cancelButtonColor: "#808390",
      confirmButtonText: "Yes, delete it!",
      didOpen: () => {
        document.querySelector('.swal2-confirm').blur()
      },
    })

    if (confirmDelete.isConfirmed) {

      const res = await projectListStore.delete(listWithProjectId)

      toast.success('Project list deleted successfully', { timeout: 1000 })
      await fetchProjectLists()
    }
  } catch (error) {
    toast.error('Failed to delete project list:', error)
  }
}

function filteredTasks(index, tasks) {
  const query = searchQuery.value[index] || ''

  return tasks.filter(task => task.name.toLowerCase().includes(query.toLowerCase()))
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

const getStatuses = computed(() => {
  return statusStore.getStatuses
})

function toggleSubtasks(index){
  isExpandedSubTasks.value[index] = !isExpandedSubTasks.value[index]
  cancelQuickSubTask(index)
}

const headers = [
  { title: 'Task Name', key: 'name', sortable: false, width: '60%' },
  { title: 'Assignee', key: 'assignee', sortable: false },
  { title: 'Status', key: 'status',  sortable: false },
  { title: 'Due Date', key: 'due_date', sortable: false },
  { title: 'EST Time', key: 'est_time', sortable: false },
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

const onDrop = (statusId, event) => {
  const taskToMove = event.added ? event.added.element : event.removed.element
  if (!taskToMove) {
    toast.error('Task to move is undefined')

    return
  }

  if(event.added )
  {
    const taskUpdateData = {
      uuid: taskToMove.uuid,
      name: taskToMove.name,
      project_uuid: projectId.value,
      status_id: statusId,
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

function onDragStart() {
  isDragging.value = true
}

function onDragEnd() {
  isDragging.value = false
}

const updateStatus = async (item, status) => {
  try {
    statusMenu.value[item.id] = false

    if (item.status.id === status.id) {
      return
    }

    item.status = status

    const payload = {
      status: status.id,
    }

    await projectTaskStore.updateAttributes(item.uuid, payload)
    await fetchProjectLists()
    toast.success('Task status updated successfully', { timeout: 1000 })
  } catch (error) {
    toast.error('Failed to update task status:', error)
  }
}

const setInputTime = item => {
  inputHoursRef.value[item.id] = item.est_time_hours
  inputMinutesRef.value[item.id] = item.est_time_minutes
}

const saveTime = async item => {

  try {
    const hours = inputHoursRef.value[item.id]
    const minutes = inputMinutesRef.value[item.id]

    if(!hours && !minutes)
    {
      toast.error('Please enter hours or minutes')

      return
    }

    isSavingTime.value = true

    const time = `${hours ? hours : '00'}h ${minutes ? minutes : '00'}m`

    item.name = `${item.name}`

    const payload = {
      est_time: time,
    }

    await projectTaskStore.updateAttributes(item.uuid, payload)
    fetchProjectLists()
    toast.success('Task EST time updated successfully', { timeout: 1000 })
    inputTimeRef.value[item.id] = false
  } catch (error) {
    toast.error('Failed to update task est time:', error)
  } finally {
    isSavingTime.value = false
  }
}

const getStatusColor = status => {
  const statusObj = getStatuses.value.find(s => s.uuid === status.uuid)

  return statusObj ? statusObj.color : 'secondary'
}

const closeDueDateMenu = async item => {
  try {
    dueDateMenu.value[item.id] = false

    item.due_date = dueDate.value

    const payload = {
      due_date: dueDate.value,
    }

    payload.due_date = moment(payload.due_date).format('YYYY-MM-DD')

    await projectTaskStore.updateAttributes(item.uuid, payload)
    dueDate.value = ''
    toast.success('Task status updated successfully', { timeout: 1000 })
  } catch (error) {
    toast.error('Failed to update task status:', error)
  }
}

const fetchMembers = async task => {
  try {
    if (!searchUser.value) {
      filteredUsers.value = []
      usersLoading.value = false

      return
    }

    usersLoading.value = true

    await userStore.fetchMembersForTask(projectId.value, task.uuid, searchUser.value)
    filteredUsers.value = userStore.getMembersForTask
  } catch (error) {
    console.error('Error fetching members:', error)
    toast.error('Failed to fetch members:', error)
  } finally {
    usersLoading.value = false
  }
}

const debouncedFilter = debounce(fetchMembers, 300)

const onMemberSearchInpt = task => {
  debouncedFilter(task)
}

const assignTask = async (user, task) => {
  try {
    menu.value[task.id] = false

    const payload = {
      assignee: user.id,
    }

    await projectTaskStore.assignTask(task.uuid, payload)
    fetchProjectLists()
    filteredUsers.value = []
    searchUser.value = ''
    toast.success('Task assigned successfully', { timeout: 1000 })
  } catch (error) {
    toast.error('Failed to assign task:', error)
  }
}

const removeAssignee = async (user, task) => {
  try {
    showDeleteIcon.value = false
    isAssigneeRemoving.value = true

    const payload = {
      assignee: user.id,
    }

    await projectTaskStore.removeAssignee(task.uuid, payload)
    await fetchProjectLists()
    toast.success('Assignee removed successfully', { timeout: 1000 })
  } catch (error) {
    toast.error('Failed to remove assignee:', error)
  } finally {
    isAssigneeRemoving.value = false
  }
}

const filteredProjectLists = computed(() => {
  return getProjectLists.value.filter(list => {
    return list.uuid == selectedList.value
  })
})

const assigneesList = computed(() => {
  const uniqueUsers = {}

  return userStore.getAllUsersByProjects.reduce((acc, user) => {
    if (!uniqueUsers[user.id]) {
      uniqueUsers[user.id] = truncate
      acc.push({
        id: user.id,
        name: `${user.name_first} ${user.name_last}`,
        avatar: user.avatar,
        name_first: user.name_first,
        name_last: user.name_last,
      })
    }

    return acc
  }, [])
})


watch(project, () => {
  useHead({ title: `${layoutConfig.app.title} | ${project?.value?.title} - Tasks` })
})
</script>

<style scoped>
.kanban-columns {
  display: flex;
  flex-wrap: nowrap;
  overflow-x: auto;
  padding-bottom: 1rem;
  margin-top: 27px !important;
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
    border: 1px solid #a12592;
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

.assignee-list {
  width: 250px;
  overflow-x: hidden !important;
  max-height: 300px !important;
}

.assignee-list-item {
  width: 240px;
  overflow: hidden !important;
}

.assignee-list-item :deep(.v-list-item__content) {
  display: flex;
  align-items: center;
}

.assignee-search-list-item {
  height: 60px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.members-loading {
  height: 60px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.member-delete-icon {
  position: absolute;
  left: 37px;
  top: 2px;
  font-size: 18px;
}

.time-field-list :deep(.v-list-item__content) {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.time-field-list :deep(.v-list-item__content .time-field) {
  width: 85px;
}

.time-field-list :deep(.v-text-field__suffix) {
  color: lightgray;
}
.filter-indicator{
  position: relative;
  right: 10px;
  bottom: 8px;
}
</style>

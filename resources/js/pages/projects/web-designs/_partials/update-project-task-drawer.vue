<template>
  <VNavigationDrawer
    :model-value="props.isEditTaskDrawerOpen"
    temporary
    location="end"
    width="700"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Edit Task Details"
      @cancel="$emit('update:isEditTaskDrawerOpen', false)"
    />

    <VDivider />

    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
        <VCardText>
          <VRow>
            <VCol
              cols="12"
              md="4"
            >
              <div class="d-flex align-center">
                <p class="mt-4 text-muted">
                  <VIcon
                    class="me-1"
                    icon="tabler-users"
                    color="primary"
                  />
                  <span class="me-1">Assigned to:</span> 
                </p>
                <VMenu
                  v-model="userMenu"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                >
                  <template #activator="{ props }">
                    <div class="v-avatar-group demo-avatar-group">
                      <div
                        v-for="(user, index) in editingTask?.assignees?.slice(0, 2)"
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
                        v-if="editingTask?.assignees?.length > 2"
                        :size="30"
                        v-bind="props"
                      >
                        <VAvatar
                          size="25"
                          class="text-white bg-primary"
                          variant="tonal"
                          v-bind="props"
                        >
                          <small>+{{ editingTask?.assignees?.length - 2 }}</small>
                        </VAvatar>
                      </VAvatar>
                    </div>

                    <VChip
                      v-if="!editingTask?.assignees?.length"
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
                      @input="onMemberSearchInpt(editingTask)"
                    />
                    <template v-if="filteredUsers.length > 0 && !usersLoading && searchUser">
                      <VListItem
                        v-for="user in filteredUsers"
                        :key="user.id"
                        class="assignee-list-item"
                        @click="assignTask(user, editingTask)"
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
                        <div v-if="editingTask?.assignees?.length > 0 && !searchUser">
                          <VListItem
                            v-for="user in editingTask?.assignees"
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
                              @click.stop="removeAssignee(user, editingTask)"
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
              </div>
            </VCol>
            <VCol
              cols="12"
              md="8"
            >
              <div class="d-flex align-center">
                <p class="mt-4 text-muted">
                  <VIcon
                    class="me-1"
                    icon="tabler-user-check"
                    color="primary"
                  />
                  <span class="me-1">Created By:</span> <span class="text-high-emphasis">{{ props.editingTask?.created_by?.name_first }} {{ props.editingTask?.created_by?.name_last }} on {{ formatTaskDate(props.editingTask?.created_at) }}</span>
                </p>
              </div>
            </VCol>
          </VRow>
        </VCardText>
        <VDivider />
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm
            ref="editTaskForm"
            @submit.prevent="submitEditTaskForm"
          >
            <VRow>
              <VCol cols="12">
                <AppTextField
                  ref="focusInput"
                  v-model="props.editingTask.name"
                  label="Task Name*"
                  :rules="[requiredValidator]"
                  placeholder="Task Name"
                />
              </VCol>
              <VCol cols="12">
                <TiptapEditor
                  v-model="props.editingTask.description"
                  class="border rounded basic-editor"
                />
              </VCol>
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="props.editingTask.start_date"
                  label="Start Date"
                  placeholder="Select Date"
                  :config="{ dateFormat: 'm/d/Y' }"
                  clearable
                />
              </VCol>
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="props.editingTask.due_date"
                  label="Due Date"
                  placeholder="Select Date"
                  :config="{ dateFormat: 'm/d/Y' }"
                  clearable
                />
              </VCol>
              <!-- Bucks Section -->
              <VCol
                v-if="props.editingTask.has_bucks_share"
                cols="12"
                calss="pb-0"
                :class="{'d-none': !authStore.isAdmin && !authStore.isManager, 'd-flex align-center': authStore.isAdmin || authStore.isManager}"
              >
                <VSwitch
                  v-model="props.editingTask.is_bucks_allowed"
                  :label="props.editingTask.assignees?.length == 0 ? 'Assign Bucks (Assignee required)' : 'Assign Bucks'"
                  inset
                  class="me-4"
                  hide-details
                  :disabled="props.editingTask.assignees?.length == 0"
                />
              </VCol>

              <template v-if="props.editingTask.is_bucks_allowed">
                <VCol
                  v-if="!authStore.isAdmin && !authStore.isManager"
                  cols="12"
                  class="pb-0"
                >
                  <h4>
                    Bucks
                  </h4>
                </VCol>
                <VCol
                  v-for="(assignee, index) in props.editingTask.assignees_bucks"
                  :key="index"
                  md="6"
                  cols="12"
                >
                  <VTextField
                    v-model="assignee.bucks_amount"
                    type="number"
                    :label="`${assignee.user_name} (${assignee.role_name})`"
                    prepend-inner-icon="tabler-currency-dollar"
                    :suffix="authStore.isAdmin || authStore.isManager ? `($${getRemainingBucks(assignee.role_id)} remaining)` : ''"
                    :disabled="!authStore.isAdmin && !authStore.isManager"
                    class="flex-grow-1 no-arrows"
                    outlined
                    dense
                    hide-details
                    :autofocus="index === 0"
                  />
                </VCol>
              </template>

              <VCol cols="12 pb-0">
                <input
                  ref="fileInputRef"
                  type="file"
                  hidden
                  accept=".jpeg, .jpg, .png, .pdf, .docx, .txt"
                  multiple
                  @change="uploadFiles"
                >
                <h4 class="mb-1">
                  Attachments
                </h4>
                <div
                  class="upload-files-sec"
                  @click="$refs.fileInputRef.click()"
                >
                  <span>Upload Files</span>
                </div>
              </VCol>
              <VCol cols="12">
                <div
                  v-for="(file, index) in taskFiles"
                  :key="index"
                  class="image-wrapper cursor-pointer"
                  @click="openFileViewer(file)"
                >
                  <VImg
                    :src="getImageUrl(file.path)"
                    width="100"
                    height="100"
                    class="mb-2"
                  />
                  <IconBtn
                    class="delete-media-menu"
                    @click.prevent
                  >
                    <VIcon icon="tabler-dots" />
                    <VMenu activator="parent">
                      <VList>
                        <VListItem
                          value="delete"
                          @click="deleteFile(file)"
                        >
                          Delete
                        </VListItem>
                      </VList>
                    </VMenu>
                  </IconBtn>
                </div>
              </VCol>
              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2"
                    :disabled="props.getLoadStatus === 1"
                    @click="editTaskForm?.validate()"
                  >
                    <span v-if="props.getLoadStatus === 1">
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
                    color="error"
                    variant="tonal"
                    @click="resetForm"
                  >
                    Reset
                  </VBtn>
                </div>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </PerfectScrollbar>
    </VCard>
  </VNavigationDrawer>
  <FileViewer
    :show="isViewerOpen"
    :file="selectedFile"
    @update:show="isViewerOpen = $event"
  />
</template>

<script setup>
import moment from 'moment'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'
import { ref } from 'vue'
import { useToast } from "vue-toastification"
import Swal from 'sweetalert2'
import { useProjectTaskStore } from "@/store/project_tasks"
import { useAuthStore } from '@/store/auth'
import { useFileStore } from '@/store/files'
import { useUserStore } from "@/store/users"
import FileViewer from '@/pages/projects/web-designs/_partials/file-viewer.vue'
import { debounce, truncate } from 'lodash'

const props = defineProps({
  isEditTaskDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchProjectTasks: Function,
  fetchProjectLists: Function,
  editingTask: Object,
  usersAssignedBucks: Array,
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isEditTaskDrawerOpen'])

onMounted(async () => {
  await fetchFiles()
})

const toast = useToast()
const projectTaskStore = useProjectTaskStore()
const authStore = useAuthStore()
const fileStore = useFileStore()
const userStore = useUserStore()

const formatTaskDate = date => moment(date).format('MMM DD, YYYY')

const focusInput = ref(null)
const editTaskForm = ref()
const isLoading= ref(false)
const fileInputRef = ref(null)
const isViewerOpen = ref(false)
const selectedFile = ref(null)
const userMenu = ref(false)
const showDeleteIcon = ref(false)
const filteredUsers = ref([])
const searchUser = ref('')
const usersLoading = ref(false)
const isAssigneeRemoving = ref(false)

const handleDrawerModelValueUpdate = val => {
  emit('update:isEditTaskDrawerOpen', val)
}

const resetForm = () => {
  editProjectForm.value?.reset()
  emit('update:isEditTaskDrawerOpen', false)
}

const fetchFiles = async () => {
  await projectTaskStore.fetchFiles(props.editingTask.uuid)
}

const uploadFiles = async e => {
  isLoading.value = true

  const files = e.target.files
  let filesArray = []
  for (let i = 0; i < files.length; i++) {
    filesArray.push(files[i])
  }

  try {
    await projectTaskStore.uploadFiles(props.editingTask.uuid, filesArray)
    toast.success('Task updated successfully', { timeout: 1000 })
    await fetchFiles()
    await props.fetchProjectLists()
    isLoading.value = false
  } catch (error) {
    toast.error('Failed to upload files:', error.message || error)
    isLoading.value = false
  }
}

const deleteFile = async file => {
  const confirmDelete = await Swal.fire({
    title: "Are you sure?",
    html: `<small>Do you want to delete this file?</small>`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "rgba(var(--v-theme-primary))",
    cancelButtonColor: "#808390",
    confirmButtonText: "Yes, remove it!",
    didOpen: () => {
      document.querySelector('.swal2-confirm').blur()
    },
  })

  if (confirmDelete.isConfirmed) {
    isLoading.value = true
    try {
      await fileStore.delete(file.uuid)
      toast.success('File deleted successfully')
      await fetchFiles()
      await props.fetchProjectLists()
    } catch (error) {
      toast.error('Failed to delete file')
    } finally {
      isLoading.value = false
    }
  }
}

async function submitEditTaskForm() {
  editTaskForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        isLoading.value = true

        const payload = {
          uuid: props.editingTask.uuid,
          name: props.editingTask.name,
          project_uuid: props.editingTask.project_uuid,
          description: props.editingTask.description,
          start_date: props.editingTask.start_date,
          due_date: props.editingTask.due_date,
          is_bucks_allowed: props.editingTask.is_bucks_allowed,
        }

        if(payload.is_bucks_allowed) {
          const roleIds = [...new Set(props.editingTask.assignees_bucks.map(assignee => assignee.role_id))]
          for (const roleId of roleIds) {
            const assigneeBucks = props.editingTask.assignees_bucks.filter(assignee => assignee.role_id === roleId)
            const bucksAmount = assigneeBucks.reduce((acc, assignee) => acc + parseFloat(assignee.bucks_amount), 0)
            const remainingBucks = getRemainingBucks(roleId)
            if (bucksAmount > remainingBucks) {
              toast.error('Bucks amount should not be greater than remaining bucks amount.')

              return
            }
          }

          payload.assignees_bucks = props.editingTask.assignees_bucks
        }
        const roleIds = [...new Set(props.editingTask.assignees_bucks.map(assignee => assignee.role_id))]
        for (const roleId of roleIds) {
          const assigneeBucks = props.editingTask.assignees_bucks.filter(assignee => assignee.role_id === roleId)
          const bucksAmount = assigneeBucks.reduce((acc, assignee) => acc + parseFloat(assignee.bucks_amount), 0)
          const remainingBucks = getRemainingBucks(roleId)
          if (bucksAmount > remainingBucks) {
            toast.error('Bucks amount should not be greater than remaining bucks amount.')
            this.isLoading = false

            return
          }
        }

        const res = await projectTaskStore.update(payload)

        if(projectTaskStore.getErrors){
          toast.error('Failed to update task')
          isLoading.value = false
        } else {
          emit('update:isEditTaskDrawerOpen', false)
          toast.success('Task updated successfully', { timeout: 1000 })
          await props.fetchProjectLists()
          await props.fetchProjectTasks()
          isLoading.value = false
        }
      } catch (error) {
        toast.error('Failed to update task:', error.message || error)
      }
    }
  })
}

const fetchMembers = async task => {
  try {
    if (!searchUser.value) {
      filteredUsers.value = []
      usersLoading.value = false

      return
    }

    usersLoading.value = true

    await userStore.fetchMembersForTask(props.editingTask.project_uuid, task.uuid, searchUser.value)
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
    userMenu.value = false

    const payload = {
      assignee: user.id,
    }

    await projectTaskStore.assignTask(task.uuid, payload)
    filteredUsers.value = []
    searchUser.value = ''
    toast.success('Task assigned successfully', { timeout: 1000 })
    await props.fetchProjectLists()
    emit('update:isEditTaskDrawerOpen', false)
  } catch (error) {
    toast.error('Failed to assign task:', error)
  }
}

const removeAssignee = async (user, task) => {
  try {
    showDeleteIcon.value = false
    isAssigneeRemoving.value = true
    userMenu.value = false

    const payload = {
      assignee: user.id,
    }

    await projectTaskStore.removeAssignee(task.uuid, payload)
    await props.fetchProjectLists()
    emit('update:isEditTaskDrawerOpen', false)
    toast.success('Assignee removed successfully', { timeout: 1000 })
  } catch (error) {
    toast.error('Failed to remove assignee:', error)
  } finally {
    isAssigneeRemoving.value = false
  }
}

const openFileViewer = file => {
  file.url = getImageUrl(file.path)
  selectedFile.value = file
  isViewerOpen.value = true
}

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

const getRemainingBucks = roleId => {
  const assignee = props.editingTask.remaining_bucks?.find(assignee => assignee.role_id === roleId)

  return assignee ? assignee.remaining_bucks : 0.00
}

const taskFiles = computed(() => {
  return projectTaskStore.getTaskFiles
})

watch(() => props.isEditTaskDrawerOpen, val => {
  if (val) {
    nextTick(() => {
      const inputEl = focusInput.value.$el.querySelector('input')
      if (inputEl) {
        inputEl.focus()
      }
    })
  }
})
</script>

<style lang="scss">
.v-navigation-drawer__content {
overflow-y: hidden !important;
}
.basic-editor {
  .ProseMirror {
    block-size: 200px;
    outline: none;
    overflow-y: auto;
    padding-inline: 0.5rem;
  }
}
.upload-files-sec {
  font-size: 12px;
  font-weight: 400;
  line-height: 16px;
  -webkit-user-select: none;
  user-select: none;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 40px;
  padding: 8px 16px;
  border-radius: 8px;
  border: 1px solid rgb(173 179 196);
  color: rgb(146 148 151);
}
.image-wrapper {
  position: relative;
  display: inline-block;
  margin-right: 25px;
}
.image-wrapper img {
  object-fit: cover;
}
.delete-media-menu {
  position: absolute;
  top: 0;
  right: 0;
  background: #fff;
  padding: 0.2rem;
  border-radius: 50%;
  cursor: pointer;
  z-index: 1;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
  height: 30px !important;
  width: 30px !important;
}
.d-none {
  display: none !important;
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

.v-list-item__content {
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
.align-center-important {
  align-items: center !important;
}
</style>

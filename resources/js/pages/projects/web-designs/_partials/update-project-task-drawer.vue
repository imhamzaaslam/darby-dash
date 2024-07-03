<template>
  <VNavigationDrawer
    :model-value="props.isEditTaskDrawerOpen"
    temporary
    location="end"
    width="600"
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
                  label="Title*"
                  :rules="[requiredValidator]"
                  placeholder="Title"
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
                  v-model="props.editingTask.created_at"
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
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'
import { ref } from 'vue'
import { useToast } from "vue-toastification"
import Swal from 'sweetalert2'
import { useProjectTaskStore } from "@/store/project_tasks"
import { useFileStore } from '@/store/files'
import FileViewer from '@/pages/projects/web-designs/_partials/file-viewer.vue'

const props = defineProps({
  isEditTaskDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchProjectTasks: Function,
  fetchProjectLists: Function,
  editingTask: Object,
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isEditTaskDrawerOpen'])

onMounted(async () => {
  await fetchFiles()
})

const toast = useToast()
const projectTaskStore = useProjectTaskStore()
const fileStore = useFileStore()

const focusInput = ref(null)
const editTaskForm = ref()
const isLoading= ref(false)
const fileInputRef = ref(null)
const isViewerOpen = ref(false)
const selectedFile = ref(null)

const handleDrawerModelValueUpdate = val => {
  emit('update:isEditTaskDrawerOpen', val)
}

const resetForm = () => {
  editProjectForm.value?.reset()
  emit('update:isEditTaskDrawerOpen', false)
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
    confirmButtonColor: "#a12592",
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

        const payload = {
          uuid: props.editingTask.uuid,
          name: props.editingTask.name,
          project_uuid: props.editingTask.project_uuid,
          description: props.editingTask.description,
          start_date: props.editingTask.created_at,
          due_date: props.editingTask.due_date,
        }

        const res = await projectTaskStore.update(payload)

        isLoading.value = true
        emit('update:isEditTaskDrawerOpen', false)
        toast.success('Task updated successfully', { timeout: 1000 })
        await props.fetchProjectLists()
        await props.fetchProjectTasks()
        isLoading.value = false
      } catch (error) {
        toast.error('Failed to update task:', error.message || error)
      }
    }
  })
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

const fetchFiles = async () => {
  await projectTaskStore.fetchFiles(props.editingTask.uuid)
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
</style>

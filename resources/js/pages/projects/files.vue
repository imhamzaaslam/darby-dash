<template>
  <Loader v-if="isLoading" />
  <VRow>
    <VCol cols="12">
      <div class="d-flex align-center">
        <VAvatar
          icon="tabler-cube"
          size="36"
          class="me-2"
          color="primary"
          variant="tonal"
        />
        <h3 class="text-primary">
          {{ project?.title }}
          <span class="d-block text-xs text-secondary">{{ project?.project_type }}</span>
        </h3>
      </div>
    </VCol>
  </VRow>
  <VRow class="mt-0 pb-0">
    <VCol
      cols="12"
      class="d-flex justify-space-between align-center"
    >
      <h3>Media Files</h3>
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
              value="create-folder"
              @click="isCreateFolderDialogOpen = true"
            >
              Create Folder
            </VListItem>
            <VListItem
              value="upload file"
              @click="chooseFile"
            >
              Upload File
            </VListItem>
          </VList>
        </VMenu>
      </VBtn>
      <input
        ref="fileInputRef"
        type="file"
        hidden
        accept=".jpeg, .jpg, .png, .pdf, .docx, .txt, .webp"
        multiple
        @change="filePicked"
      >
    </VCol>
  </VRow>

  <div v-if="openFolder">
    <VRow class="mt-4">
      <VCol
        cols="12"
        class="d-flex align-center justify-space-between"
      >
        <div>
          <VIcon
            class="tabler-arrow-left"
            size="large"
            color="primary"
            @click="openFolder = null"
          />
          <span class="ml-2">Back</span>
        </div>
        <h3 class="text-primary">
          {{ openFolder.name }}
        </h3>
      </VCol>
    </VRow>
    <VRow
      v-if="folderFiles.length > 0"
      class="mt-4"
    >
      <VCol
        v-for="(file, index) in folderFiles"
        :key="file.id"
        cols="12"
        md="2"
      >
        <div
          class="image-container"
          @click="file.mime_type.includes('image/') ? openFileViewer(file) : copyPath(file, true)"
        >
          <img
            v-if="file.mime_type.includes('image/')"
            :src="getImageUrl(file.path)"
            :alt="file.name"
            class="uploaded-image"
          >
          <div
            v-else
            class="file-placeholder"
          >
            <img
              src="../../../images/logos/document.png"
              class="default-image"
            >
            <span class="file-type">.{{ file.name.split('.').pop().toLowerCase() }}</span>
          </div>
          <div class="d-flex align-center justify-space-between mt-2">
            <span class="text-xs">{{ file.created_at }}</span>
            <div class="d-flex align-center">
              <VIcon
                class="tabler-download"
                size="small"
                color="primary"
                @click.stop="downloadFile(file)"
              />
              <VIcon
                class="tabler-trash-filled ms-1"
                size="small"
                color="error"
                @click.stop="deleteImage(file)"
              />
              <VIcon
                class="tabler-copy ms-1"
                size="small"
                color="info"
                @click.stop="copyPath(file)"
              />
            </div>
          </div>
        </div>
      </VCol>
    </VRow>
    <VRow
      v-if="folderFiles.length === 0 && images.length === 0"
      class="mt-4"
    >
      <VCol
        cols="12"
        class="d-flex flex-column align-center justify-center text-center" 
      >
        <span v-html="emptyFileImg" />
        <span class="mt-n11">No Files added yet.</span>
      </VCol>
    </VRow>
  </div>

  <div v-else>
    <VRow
      v-if="folders.length > 0"
      class="mt-4 justify-start"
    >
      <VCol
        v-for="folder in folders"
        :key="folder.id"
        cols="12"
        sm="6"
        md="4"
        lg="2"
        class="folder-col"
      >
        <div class="folder-container">
          <IconBtn
            class="action-icon mb-5"
            size="x-small"
            @click.prevent
          >
            <VIcon icon="tabler-dots-vertical" />
            <VMenu
              activator="parent"
              class="p-0"
            >
              <VList class="p-0">
                <VListItem @click.stop="editFolder(folder)">
                  Edit
                </VListItem>
                <VListItem @click.stop="deleteFolder(folder)">
                  Delete
                </VListItem>
              </VList>
            </VMenu>
          </IconBtn>
          <div
            class="folder-content"
            @click="setOpenFolder(folder)"
          >
            <VIcon
              class="folder-icon"
              size="large"
              color="primary"
              icon="tabler-folder-filled"
            />
            <span
              class="folder-name"
              :title="folder.name"
            >
              {{ folder.name }} ({{ folder.filesCount }})
            </span>
          </div>
        </div>
      </VCol>
    </VRow>
    <VRow
      v-if="allFiles.length > 0"
      class="mt-4"
    >
      <VCol
        v-for="(file, index) in allFiles"
        :key="file.id"
        cols="12"
        md="2"
      >
        <div
          class="image-container"
          @click="file.mime_type.includes('image/') ? openFileViewer(file) : copyPath(file, true)"
        >
          <img
            v-if="file.mime_type.includes('image/')"
            :src="getImageUrl(file.path)"
            :alt="file.name"
            class="uploaded-image"
          >
          <div
            v-else
            class="file-placeholder"
          >
            <img
              src="../../../images/logos/document.png"
              class="default-image"
            >
            <span class="file-type">.{{ file.name.split('.').pop().toLowerCase() }}</span>
          </div>
          <div class="d-flex align-center justify-space-between mt-2">
            <span class="text-xs">{{ file.created_at }}</span>
            <div class="d-flex align-center">
              <VIcon
                class="tabler-download"
                size="small"
                color="primary"
                @click.stop="downloadFile(file)"
              />
              <VIcon
                class="tabler-trash-filled ml-1"
                size="small"
                color="error"
                @click.stop="deleteImage(file)"
              />
              <VIcon
                class="tabler-copy ms-1"
                size="small"
                color="info"
                @click.stop="copyPath(file)"
              />
            </div>
          </div>
        </div>
      </VCol>
    </VRow>

    <VRow
      v-if="images.length === 0 && folders.length === 0 && allFiles.length === 0"
      class="mt-4"
    >
      <VCol
        cols="12"
        class="d-flex flex-column align-center justify-center text-center" 
      >
        <span v-html="emptyFileImg" />
        <span class="mt-n11">No Files added yet.</span>
      </VCol>
    </VRow>
  </div>

  <VRow
    v-if="images.length > 0" 
    class="mt-2"
  >
    <VCol
      v-for="(image, index) in images"
      :key="image.id"
      cols="12"
      md="2"
    > 
      <div
        class="image-container"
        @click="image.mime_type.includes('image/') ? openFileViewer(image) : copyPath(image, true)"
      >
        <img
          v-if="image.type.startsWith('image/')"
          :src="image.url"
          :alt="image.name"
          class="uploaded-image"
        >
        <div
          v-else
          class="file-placeholder"
        >
          <img
            src="../../../images/logos/document.png"
            class="default-image"
          >
          <span class="file-type">.{{ image.name.split('.').pop().toLowerCase() }}</span>
        </div>
        <div
          v-if="uploadProgress[index] && !uploadProgress[index].isCompleted"
          class="progress"
        >
          <div
            class="progress-bar"
            role="progressbar"
            :style="{ width: uploadProgress[index].progress + '%' }"
            :aria-valuenow="uploadProgress[index].progress"
            aria-valuemin="0"
            aria-valuemax="100"
          />
          <span class="progress-text">{{ uploadProgress[index].progress }}%</span>
        </div>
        <div
          v-else
          class="d-flex align-center justify-space-between mt-2"
        >
          <span class="text-sm">2 secs ago</span>
          <div class="d-flex align-center">
            <VIcon
              class="tabler-download"
              size="small"
              color="primary"
              @click.stop="downloadFile(image)"
            />
            <VIcon
              class="tabler-trash-filled ml-1"
              size="small"
              color="error"
              @click.stop="deleteImage(index)"
            />
            <VIcon
              class="tabler-copy ms-1"
              size="small"
              color="info"
              @click.stop="copyPath(image)"
            />
          </div>
        </div>
      </div>
    </VCol>
  </VRow>

  <FileViewer
    :show="isViewerOpen"
    :file="selectedFile"
    @update:show="isViewerOpen = $event"
  />
  <CreateFolderDialog
    :is-open="isCreateFolderDialogOpen"
    :folder-load-status="folderLoadStatus"
    :get-errors="getErrors"
    @get-folders="getFolders"
    @update:is-open="isCreateFolderDialogOpen = $event"
  />
  <EditFolderDialog
    :is-open="isEditFolderDialogOpen"
    :folder-load-status="folderLoadStatus"
    :folder="selectedFolder"
    :get-errors="getErrors"
    @get-folders="getFolders"
    @update:is-open="isEditFolderDialogOpen = $event"
  />
</template>

<script setup>
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import { ref } from 'vue'
import Swal from 'sweetalert2'
import FileViewer from './web-designs/_partials/file-viewer.vue'
import emptyFileImg from '../../../images/darby/empty_file.svg?raw'
import CreateFolderDialog from '@/components/dialogs/CreateFolderDialog.vue'
import EditFolderDialog from '@/components/dialogs/EditFolderDialog.vue'
import Loader from '@/components/Loader.vue'
import { useProjectStore } from "@/store/projects"
import { useFolderStore } from '@/store/folders'
import { useFileStore } from '@/store/files'
import { useRoute } from 'vue-router'
import { useToast } from "vue-toastification"
import sketch from '@images/icons/project-icons/sketch.png'
import { VListItemMedia } from 'vuetify/lib/components/VList/index.mjs'

onBeforeMount(async () => {
  await getFolders()
  await getFiles()
  await fetchProject()
})

const isCreateFolderDialogOpen = ref(false)
const isEditFolderDialogOpen = ref(false)
const isLoading = ref(false)

const folderStore = useFolderStore()
const fileStore = useFileStore()
const projectStore = useProjectStore()
const $route = useRoute()
const toast = useToast()
const projectUuid = $route.params.id
const fileInputRef = ref(null)
const images = ref([])
const uploadProgress = ref([])
const isViewerOpen = ref(false)
const selectedFile = ref(null)
const selectedFolder = ref(null)
const files = ref([])
const openFolder = ref(null)

const getFolders = async () => {
  isLoading.value = true
  try {
    await folderStore.getAll(projectUuid)
  } catch (error) {
    toast.error('Failed to fetch folders')
  } finally {
    isLoading.value = false
  }
}

const getFiles = async () => {
  isLoading.value = true
  images.value = []

  uploadProgress.value = []
  if (openFolder.value) {
    getFolderFiles(openFolder.value)
  } else {
    try {
      await fileStore.getAll(projectUuid)
    } catch (error) {
      toast.error('Failed to fetch files')
    } finally {
      isLoading.value = false
    }
  }
}

const fetchProject = async () => {
  try {
    await projectStore.show(projectUuid)
  } catch (error) {
    toast.error('Error fetching project:', error)
  }
}

const chooseFile = () => {
  fileInputRef.value.click()
}

const filePicked = event => {
  const files = event.target.files

  uploadFiles(files)
  for (let i = 0; i < files.length; i++) {
    const file = files[i]
    const uniqueId = `${file.name}-${Date.now()}`
    const fileReader = new FileReader()

    // Initialize upload progress for the file with a unique id
    uploadProgress.value.push({ id: uniqueId, name: file.name, progress: 0, isCompleted: false })

    if (file.type.startsWith('image/')) {
      fileReader.onload = () => {
        images.value.push({
          url: fileReader.result,
          name: file.name,
          id: uniqueId,
          type: file.type,
        })
      }
      fileReader.readAsDataURL(file)
    } else {
      // For non-image files, add a placeholder with file type
      const fileURL = URL.createObjectURL(file)

      images.value.push({
        url: fileURL,
        name: file.name,
        id: uniqueId,
        type: file.type,
      })
    }

    // Simulate upload progress
    simulateUploadProgress(uniqueId)
  }

  // Reset the file input value
  event.target.value = ''
}

const uploadFiles = async files => {
  isLoading.value = true
  let fileData = []

  for (let i = 0; i < files.length; i++) {
    fileData.push(files[i])
  }

  try {
    if (openFolder.value) {
      await folderStore.uploadFiles(fileData, openFolder.value.uuid)
    } else {
      await fileStore.upload(fileData, projectUuid)
    }

    const isAllCompleted = uploadProgress.value.every(f => f.isCompleted)
    if (isAllCompleted) {
      await getFiles()
      await getFolders()
      isLoading.value = false
    } else {
      const interval = setInterval(async () => {
        const isAllCompleted = uploadProgress.value.every(f => f.isCompleted)
        if (isAllCompleted) {
          clearInterval(interval)
          await getFiles()
          await getFolders()
          isLoading.value = false
        }
      }, 500)
    }
  } catch (error) {
    toast.error('Failed to upload files')
    isLoading.value = false
  }
}

const simulateUploadProgress = uniqueId => {
  const progressIndex = uploadProgress.value.findIndex(f => f.id === uniqueId)
  if (progressIndex !== -1) {
    let progress = 0

    const interval = setInterval(() => {
      if (progress < 100) {
        progress += 10
        uploadProgress.value[progressIndex].progress = progress
      } else {
        uploadProgress.value[progressIndex].progress = 100
        uploadProgress.value[progressIndex].isCompleted = true
        clearInterval(interval)
      }
    }, 500)
  }
}

const openFileViewer = file => {
  file.url = getImageUrl(file.path)
  selectedFile.value = file
  isViewerOpen.value = true
}

const downloadFile = file => {
  const link = document.createElement('a')

  link.href = getImageUrl(file.path)
  link.download = file.name
  link.click()
}

const deleteImage = async file => {
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
      await getFiles()
    } catch (error) {
      toast.error('Failed to delete file')
    } finally {
      isLoading.value = false
    }
  }
}

const editFolder = folder => {
  selectedFolder.value = folder
  isEditFolderDialogOpen.value = true
}

const deleteFolder = async folder => {
  const confirmDelete = await Swal.fire({
    title: "Are you sure?",
    html: `<p class="mb-0">Do you want to delete this <b>${folder.name}</b> folder?</p><small>All files inside this folder will also be deleted.</small>`,
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
    await folderStore.delete(folder.uuid)
    await getFolders()
  }
}

const setOpenFolder = async folder => {
  openFolder.value = folder
  await getFolderFiles(folder)
}

const copyPath = (image, open = false) => {
  const origin = window.location.origin
  const path = getImageUrl(image.path)
  const fullUrl = `${origin}${path}`

  if(open){
    window.open(fullUrl, '_blank')
  }else{
    navigator.clipboard.writeText(fullUrl)

    toast.success('Link copy successfully!')
  }
}

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

const getFolderFiles = async folder => {
  isLoading.value = true
  try {
    await folderStore.getFiles(folder.uuid)
  } catch (error) {
    toast.error('Failed to fetch folder files')
  } finally {
    isLoading.value = false
  }
}

const folders = computed(() => {
  return folderStore.folders
})

const allFiles = computed(() => {
  return fileStore.files
})

const folderLoadStatus = computed(() => {
  return folderStore.getLoadStatus
})

const fileLoadStatus = computed(() => {
  return fileStore.getLoadStatus
})

const getErrors = computed(() => {
  return folderStore.getError
})

const folderFiles = computed(() => {
  return folderStore.getFolderFiles
})

const project = computed(() =>{
  return projectStore.getProject
})

watch(project, () => {
  useHead({ title: `${layoutConfig.app.title} | ${project?.value?.title} - Files` })
})
</script>

<style scoped>
.image-container {
  position: relative;
  border: 2px solid rgba(var(--v-theme-primary));
  border-radius: 10px;
  padding: 5px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
  max-width: 210px; /* Reduced size */
  cursor: pointer;
}

.image-container:hover {
  transform: scale(1.05);
}

.uploaded-image {
  height: 100px;
  width: 100%;
  object-fit: contain; 
  border-radius: 8px;
  display: block;
  margin: 0 auto;
}

.file-placeholder {
  position: relative;
  height: 100px;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background-color: #f8f9fa;
  text-align: center;
}

.default-image {
  max-height: 80px;
  max-width: 100%;
  object-fit: contain;
  border-radius: 8px;
}

.file-type {
  position: absolute;
  bottom: 33px;
  left: 50%;
  transform: translateX(-50%);
  font-weight: bold;
  font-size: 14px;
  color: black;
}

.progress {
  height: 15px; 
  margin-top: 5px; 
  background-color: #e9ecef;
  border-radius: 0.25rem;
  position: relative;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background-color: rgba(var(--v-theme-primary));
  transition: width 0.6s ease;
  position: relative;
}

.progress-text {
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  color: black ;
  font-size: 10px; 
  font-weight: bold;
}

.folder-container {
  position: relative; 
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 12px;
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  background-color: #ffffff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  cursor: pointer;
}

.folder-container:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.folder-content {
  display: flex;
  align-items: center;
  gap: 8px; 
}

.folder-icon {
  font-size: 36px;
}

.folder-name {
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  width: 100%;
  max-width: 120px;
}

.action-icon {
  position: absolute;
  top: 8px;
  right: 8px;
  z-index: 10;
  background: none;
  border: none;
  cursor: pointer;
}
</style>

<template>
  <Loader v-if="isLoading" />
  <VRow>
    <VCol
      cols="12"
      class="d-flex justify-space-between align-center"
    >
      <h3>Media Files</h3>
      <input
        ref="fileInputRef"
        type="file"
        hidden
        accept=".jpeg, .jpg, .png, .pdf, .docx, .txt"
        multiple
        @change="filePicked"
      >
      <div class="d-flex gap-2">
        <VBtn
          v-if="!openFolder"
          prepend-icon="tabler-folder-plus"
          color="primary"
          @click="isCreateFolderDialogOpen = true"
        >
          Create Folder
        </VBtn>
        <VBtn
          prepend-icon="tabler-upload"
          color="primary"
          @click="chooseFile"
        >
          Upload File
        </VBtn>
      </div>
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
        <h3 class="text-primary">{{ openFolder.name }}</h3>
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
          @click="openFileViewer(file)"
        >
          <img
            v-if="file.type.includes('image/')"
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
          <div class="d-flex align-center justify-space-between">
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
      class="mt-4"
    >
      <VCol
        v-for="folder in folders"
        :key="folder.id"
        cols="12"
        md="3"
        lg="2"
        @dblclick="setOpenFolder(folder)"
      >
        <div class="folder-container">
          <IconBtn @click.prevent class="action-icon">
            <VIcon icon="tabler-dots-vertical" />
            <VMenu 
              activator="parent" 
              class="p-0"
            >
              <VList class="p-0">
                <VListItem
                  value="edit"
                  class="p-0"
                  @click="editFolder(folder)"
                >
                  Edit
                </VListItem>
                <VListItem
                  value="delete"
                  class="p-0"
                  @click="deleteFolder(folder)"
                >
                  Delete
                </VListItem>
              </VList>
            </VMenu>
          </IconBtn>
          <div class="folder-icon">
            <VIcon 
              class="tabler-folder"
              size="large"
              color="primary"
            />
          </div>
          <div class="folder-name">
            {{ folder.name }}
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
          @click="openFileViewer(file)"
        >
          <img
            v-if="file.type.includes('image/')"
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
          <div class="d-flex align-center justify-space-between">
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
        @click="openFileViewer(image)"
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
          class="d-flex align-center justify-space-between"
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
import { ref } from 'vue'
import FileViewer from './web-designs/_partials/file-viewer.vue'
import emptyFileImg from '../../../images/darby/empty_file.svg?raw'
import CreateFolderDialog from '@/components/dialogs/CreateFolderDialog.vue'
import EditFolderDialog from '@/components/dialogs/EditFolderDialog.vue'
import Loader from '@/components/Loader.vue'
import { useFolderStore } from '@/store/folders'
import { useFileStore } from '@/store/files'
import { useRoute } from 'vue-router'
import { useToast } from "vue-toastification"

const isCreateFolderDialogOpen = ref(false)
const isEditFolderDialogOpen = ref(false)
const isLoading = ref(false)

const folderStore = useFolderStore()
const fileStore = useFileStore()
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

onBeforeMount(async () => {
  await getFolders()
  await getFiles()
})

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
      await fileStore.upload(fileData, projectUuid, openFolder.value.uuid)
    } else {
      await fileStore.upload(fileData, projectUuid)
    }

    const isAllCompleted = uploadProgress.value.every(f => f.isCompleted)
    if (isAllCompleted) {
      await getFiles()
      isLoading.value = false
    } else {
      const interval = setInterval(async () => {
        const isAllCompleted = uploadProgress.value.every(f => f.isCompleted)
        if (isAllCompleted) {
          clearInterval(interval)
          await getFiles()
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

const deleteImage = async files => {
  isLoading.value = true
  try {
    await fileStore.delete(files.uuid)
    toast.success('File deleted successfully')
    await getFiles()
  } catch (error) {
    toast.error('Failed to delete file')
  } finally {
    isLoading.value = false
  }
}

const editFolder = folder => {
  selectedFolder.value = folder
  isEditFolderDialogOpen.value = true
}

const deleteFolder = async folder => {
  await folderStore.delete(folder.uuid)
  await getFolders()
}

const setOpenFolder = async folder => {
  openFolder.value = folder
  await getFolderFiles(folder)
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
</script>

<style scoped>
.image-container {
  position: relative;
  border: 2px solid #F48D27;
  border-radius: 10px;
  padding: 5px;
  margin-bottom: 10px; /* Reduced margin */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
  max-width: 150px; /* Reduced size */
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
  background-color: #F48D27;
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
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
  cursor: pointer;
  margin-bottom: 16px;
  position: relative;
}

.action-icon {
  position: absolute;
  top: -4px;
  right: -8px;
}

.folder-container:hover {
  transform: scale(1.05);
}

.folder-icon {
  font-size: 48px;
  margin-bottom: 8px;
}

.folder-name {
  font-weight: bold;
  text-align: center;
}
</style>

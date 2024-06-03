<template>
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
      <VBtn
        prepend-icon="tabler-upload"
        color="primary"
        @click="chooseFile"
      >
        Upload File
      </VBtn>
    </VCol>
  </VRow>
  <VRow class="mt-2">
    <VCol
      v-for="(image, index) in images"
      v-if="images.length > 0"
      :key="image.id"
      cols="12"
      md="2"> 
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
          class="d-flex align-center justify-space-between">
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
    <VCol
      v-else
      cols="12"
      class="d-flex flex-column align-center justify-center text-center" 
    >
      <span v-html="emptyFileImg" />
      <span class="mt-n11">No Files added yet.</span>
    </VCol>
  </VRow>

  <FileViewer
    :show="isViewerOpen"
    :file="selectedFile"
    @update:show="isViewerOpen = $event"
  />
</template>

<script setup>
import { ref } from 'vue'
import FileViewer from './web-designs/_partials/file-viewer.vue'
import emptyFileImg from '../../../images/darby/empty_file.svg?raw'

const fileInputRef = ref(null)
const images = ref([])
const uploadProgress = ref([])
const isViewerOpen = ref(false)
const selectedFile = ref(null)

const chooseFile = () => {
  fileInputRef.value.click()
}

const filePicked = event => {
  const files = event.target.files
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
  selectedFile.value = file
  isViewerOpen.value = true
}

const downloadFile = file => {
  const link = document.createElement('a')

  link.href = file.url
  link.download = file.name
  link.click()
}

const deleteImage = index => {
  images.value.splice(index, 1)
  uploadProgress.value.splice(index, 1)
}
</script>

<style scoped>
/* .image-row {
  display: flex;
  flex-wrap: wrap;
}

.image-col {
  flex: 1 1 20%; 
  max-width: 20%;
  box-sizing: border-box;
  padding: 8px; 
} */

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
</style>

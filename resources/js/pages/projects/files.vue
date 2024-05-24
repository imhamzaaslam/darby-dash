<script setup>
import { ref } from 'vue'

const fileInputRef = ref(null)
const images = ref([])
const uploadProgress = ref([])

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
    uploadProgress.value.push({ id: uniqueId, name: file.name, progress: 0 })

    fileReader.onload = () => {
      images.value.push({
        url: fileReader.result,
        name: file.name,
        id: uniqueId,
      })
    }
    fileReader.readAsDataURL(file)

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
        clearInterval(interval)
      }
    }, 500)
  }
}

const deleteImage = index => {
  images.value.splice(index, 1)
  uploadProgress.value.splice(index, 1)
}
</script>

<template>
  <VRow>
    <VCol
      cols="12"
      class="d-flex justify-space-between align-center"
    >
      <h3>
        Media Files
      </h3>
      <input
        ref="fileInputRef"
        type="file"
        hidden
        accept=".jpeg, .jpg, .png, .pdf"
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
      :key="image.id"
      cols="12"
      md="2"
    >
      <div class="image-container">
        <img
          :src="image.url"
          :alt="image.name"
          class="uploaded-image"
        >
        <div
          v-if="uploadProgress[index]"
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
        <span
          class="delete-icon"
          @click="deleteImage(index)"
        >
          <i class="tabler-square-x-filled" />
        </span>
      </div>
    </VCol>
  </VRow>
</template>

<style scoped>
.image-container {
  position: relative;
  border: 2px solid #007bff;
  border-radius: 10px;
  padding: 5px;
  margin-bottom: 10px; /* Reduced margin */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
  max-width: 150px; /* Reduced size */
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
  background-color: #f48d27;
  transition: width 0.6s ease;
  position: relative;
}

.progress-text {
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 10px; 
  font-weight: bold;
}

.delete-icon {
  position: absolute;
  top: 0;
  right: 0;
  transform: translate(45%, -53%);
  cursor: pointer;
  color: red;
  border-radius: 50%;
  padding: 5px;
}
</style>

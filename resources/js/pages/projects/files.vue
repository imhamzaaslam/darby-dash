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
    const fileReader = new FileReader()

    // Initialize upload progress for the file
    uploadProgress.value.push({ name: file.name, progress: 0 })

    fileReader.onload = () => {
      images.value.push({
        url: fileReader.result,
        name: file.name,
      })
    }
    fileReader.readAsDataURL(file)

    // Simulate upload progress
    simulateUploadProgress(file.name)
  }

  // Reset the file input value
  event.target.value = ''
}

const simulateUploadProgress = fileName => {
  const progressIndex = uploadProgress.value.findIndex(f => f.name === fileName)
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
      <h5 class="text-h5">
        Files
      </h5>
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
        size="small"
        @click="chooseFile"
      >
        Upload File
      </VBtn>
    </VCol>
  </VRow>
  <VRow class="mt-3">
    <VCol
      v-for="(image, index) in images"
      :key="index"
      cols="12"
      md="3"
      sm="3"
    >
      <div class="image-container">
        <img
          :src="image.url"
          :alt="image.name"
          class="uploaded-image"
        >
        <div class="progress" v-if="uploadProgress[index]">
          <div
            class="progress-bar"
            role="progressbar"
            :style="{ width: uploadProgress[index].progress + '%' }"
            :aria-valuenow="uploadProgress[index].progress"
            aria-valuemin="0"
            aria-valuemax="100"
          ></div>
          <span class="progress-text">{{ uploadProgress[index].progress }}%</span>
        </div>
        <span class="delete-icon" @click="deleteImage(index)">
          <i class="tabler-square-x-filled"></i>
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
  margin-bottom: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.image-container:hover {
  transform: scale(1.05);
}

.uploaded-image {
  max-width: 100%;
  max-height: 80px;
  border-radius: 8px;
  display: block;
  margin: 0 auto;
}

.progress {
  height: 20px;
  margin-top: 10px;
  background-color: #e9ecef;
  border-radius: 0.25rem;
  position: relative;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background-color: rgb(246 150 54);
  transition: width 0.6s ease;
  position: relative;
}

.progress-text {
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 12px;
  font-weight: bold;
}

.delete-icon {
  position: absolute;
  top: 5px;
  right: 5px;
  cursor: pointer;
  color: red;
}
</style>

<template>
  <VNavigationDrawer
    :model-value="props.isEditServiceDrawerOpen"
    temporary
    location="end"
    width="700"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Edit Service Details"
      @cancel="$emit('update:isEditServiceDrawerOpen', false)"
    />

    <VDivider />

    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm
            ref="editServiceForm"
            @submit.prevent="submitEditServiceForm"
          >
            <VRow>
              <VCol
                md="6"
                cols="12"
              >
                <AppTextField
                  ref="focusInput"
                  v-model="props.editServiceDetails.title"
                  label="Title*"
                  :rules="[requiredValidator]"
                  placeholder="Title"
                />
              </VCol>
              <VCol
                md="6"
                cols="12"
              >
                <AppAutocomplete
                  v-model="props.editServiceDetails.project_type_id"
                  label="Type*"
                  placeholder="Select Type"
                  :rules="[requiredValidator]"
                  :items="props.getProjectTypes"
                  item-title="name"
                  item-value="id"
                />
              </VCol>
              <VCol cols="12">
                <TiptapEditor
                  v-model="props.editServiceDetails.description"
                  :rules="[requiredValidator]"
                  label="Description*"
                  class="border rounded basic-editor"
                />
              </VCol>
              <VCol
                md="9"
                cols="12"
                class="pb-0"
              >
                <input
                  ref="fileInputRef"
                  type="file"
                  hidden
                  accept=".jpeg, .jpg, .png, .gif"
                  @change="handleFileChange"
                >
                <h4 class="mb-1">
                  Image
                </h4>
                <div
                  class="upload-files-sec"
                  @click="$refs.fileInputRef.click()"
                >
                  <span>Upload Image</span>
                </div>
                <div v-if="selectedFile">
                  {{ selectedFile.name }}
                </div>
              </VCol>
              <VCol
                v-if="props.editServiceDetails.image"
                md="3"
                cols="12"
                class="pb-0"
              >
                <div
                  class="image-wrapper cursor-pointer"
                  @click="openFileViewer(props.editServiceDetails.image)"
                >
                  <VImg
                    :src="getImageUrl(props.editServiceDetails.image.path)"
                    width="100"
                    rounded="md"
                  />
                </div>
              </VCol>
              <VCol
                v-else
                md="3"
                cols="12"
                class="pb-0"
              >
                <div class="image-wrapper cursor-pointer">
                  <VImg
                    :src="placeholderImg"
                    width="100"
                    rounded="md"
                  />
                </div>
              </VCol>
              <VCol
                cols="12"
                class="pb-0"
              >
                <VSwitch
                  v-model="props.editServiceDetails.status"
                  color="primary"
                  label="Status"
                  :true-value="1"
                  :false-value="0"
                />
              </VCol>
              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2"
                    :disabled="props.getLoadStatus === 1"
                    @click="editServiceForm?.validate()"
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
    :file="isSelectedFile"
    @update:show="isViewerOpen = $event"
  />
</template>

<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import placeholderImg from '@images/pages/servicePlaceholder.png'
import { VForm } from 'vuetify/components/VForm'
import { ref } from 'vue'
import { useToast } from "vue-toastification"
import { useUserSettingStore } from "@/store/user_settings"
import FileViewer from '@/pages/projects/web-designs/_partials/file-viewer.vue'

const props = defineProps({
  isEditServiceDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchServices: Function,
  editServiceDetails: Object,
  getProjectTypes: Object,
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isEditServiceDrawerOpen'])

const toast = useToast()
const userSettingStore = useUserSettingStore()

const focusInput = ref(null)
const editServiceForm = ref()
const isLoading= ref(false)
const fileInputRef = ref(null)
const selectedFile = ref(null)
const isViewerOpen = ref(false)
const isSelectedFile = ref(null)

const handleDrawerModelValueUpdate = val => {
  emit('update:isEditServiceDrawerOpen', val)
  fileInputRef.value.value = ''
}

const resetForm = () => {
  editServiceForm.value?.reset()
  selectedFile.value = null
  fileInputRef.value.value = ''
  emit('update:isEditServiceDrawerOpen', false)
}

const handleFileChange = e => {
  const file = e.target.files[0]
  if (file) {
    selectedFile.value = file
  }
}

async function submitEditServiceForm() {
  editServiceForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        isLoading.value = true

        const formData = new FormData()

        formData.append('title', props.editServiceDetails.title)
        formData.append('description', props.editServiceDetails.description)
        formData.append('project_type_id', props.editServiceDetails.project_type_id)
        formData.append('status', props.editServiceDetails.status)

        if (selectedFile.value) {
          formData.append('serviceImage', selectedFile.value)
        }

        const res = await userSettingStore.updateService(props.editServiceDetails.uuid, formData)

        await props.fetchServices()
        emit('update:isEditServiceDrawerOpen', false)
        selectedFile.value = null
        fileInputRef.value.value = ''
        toast.success('Service updated successfully', { timeout: 1000 })
      } catch (error) {
        toast.error('Failed to update service:', error.message || error)
      }
    }
  })
}

const openFileViewer = file => {
  file.url = getImageUrl(file.path)
  isSelectedFile.value = file
  isViewerOpen.value = true
}

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

watch(() => props.isEditServiceDrawerOpen, val => {
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
.d-none {
display: none !important;
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
</style>

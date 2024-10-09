<template>
  <VNavigationDrawer
    :model-value="props.isAddServiceDrawerOpen"
    temporary
    location="end"
    width="700"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Add Service Details"
      @cancel="$emit('update:isAddServiceDrawerOpen', false)"
    />

    <VDivider />

    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm
            ref="addServiceForm"
            @submit.prevent="submitAddServiceForm"
          >
            <VRow>
              <VCol
                md="6"
                cols="12"
              >
                <AppTextField
                  ref="focusInput"
                  v-model="title"
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
                  v-model="projectTypeId"
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
                  v-model="description"
                  :rules="[requiredValidator]"
                  label="Description*"
                  class="border rounded basic-editor"
                />
              </VCol>
              <VCol
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
                cols="12"
                class="pb-0"
              >
                <VSwitch
                  v-model="serviceStatus"
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
                    @click="addServiceForm?.validate()"
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
</template>

<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'
import { ref } from 'vue'
import { useToast } from "vue-toastification"
import { useUserSettingStore } from "@/store/user_settings"

const props = defineProps({
  isAddServiceDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchServices: Function,
  getProjectTypes: Object,
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isAddServiceDrawerOpen'])

const toast = useToast()
const userSettingStore = useUserSettingStore()

const focusInput = ref(null)
const addServiceForm = ref()
const isLoading= ref(false)
const fileInputRef = ref(null)
const selectedFile = ref(null)
const title = ref('')
const description = ref('')
const serviceStatus = ref(1)
const projectTypeId = ref(null)

const handleDrawerModelValueUpdate = val => {
  emit('update:isAddServiceDrawerOpen', val)
  if(val)
  {
    resetForm()
  }
}

const resetForm = () => {
  addServiceForm.value?.reset()
  selectedFile.value = null
  fileInputRef.value.value = ''
  title.value = ''
  description.value = ''
  serviceStatus.value = 1
  projectTypeId.value = null
}

const handleFileChange = e => {
  const file = e.target.files[0]
  if (file) {
    selectedFile.value = file
  }
}

async function submitAddServiceForm() {
  addServiceForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        isLoading.value = true

        const formData = new FormData()

        formData.append('title', title.value)
        formData.append('description', description.value)
        formData.append('project_type_id', projectTypeId.value)
        formData.append('status', serviceStatus.value)

        if (selectedFile.value) {
          formData.append('serviceImage', selectedFile.value)
        }

        const res = await userSettingStore.createService(formData)

        await props.fetchServices()
        emit('update:isAddServiceDrawerOpen', false)
        resetForm()
        toast.success('Service added successfully', { timeout: 1000 })
      } catch (error) {
        toast.error('Failed to added service:', error.message || error)
      }
    }
  })
}

watch(() => props.isAddServiceDrawerOpen, val => {
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

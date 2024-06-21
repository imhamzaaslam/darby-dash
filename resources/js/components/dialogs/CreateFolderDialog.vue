<template>
  <VDialog
    v-model="props.isOpen"
    max-width="500px"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="closeDialog" />
    <VCard title="Create Folder">
      <VForm
        ref="createFolderForm"
        @submit.prevent="submit"
      >
        <VCardText>
          <VTextField
            v-model="folderName"
            autofocus
            label="Folder Name"
            :rules="[requiredValidator]"
            :error-messages="addingErrors.name"
          />
        </VCardText>
        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            type="submit"
            color="primary"
            :disabled="folderLoadStatus === 1"
          >
            <span v-if="folderLoadStatus === 1">
              <VProgressCircular
                :size="16"
                width="3"
                indeterminate
              />
              Loading...
            </span>
            <span v-else>
              Create
            </span>
          </VBtn>
          <VBtn
            color="secondary"
            @click="closeDialog"
          >
            Cancel
          </VBtn>
        </VCardText>
      </VForm>
    </VCard>
  </VDialog>
</template>

<script setup>
import { useFolderStore } from '@/store/folders'
import { useRoute } from 'vue-router'
import { useToast } from "vue-toastification"

const props = defineProps({
  isOpen: Boolean,
  folderLoadStatus: String,
  getErrors: Object,
})

const emit = defineEmits(['update:isOpen', 'getFolders'])

const folderStore = useFolderStore()
const $route = useRoute()
const toast = useToast()
const projectUuid = $route.params.id
const folderName = ref('')
const createFolderForm = ref()

const addingErrors = ref({
  name: null,
})

// Close dialog function
const closeDialog = () => {
  emit('update:isOpen', false)
}

const submit = () => {
  createFolderForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        resetErrors()

        const payload = {
          'name': folderName.value,
        }

        await folderStore.create(projectUuid, payload)
        if(props.getErrors) {
          showError()
        } else{
          emit('getFolders')
          closeDialog()
          toast.success('Folder created successfully')
        }
      } catch (error) {
        console.error(error)
      }
    }
  })
}

const showError = () => {
  if (props.getStatusCode === 500) {
    toast.error('Something went wrong. Please try again later.')
  } else {
    addingErrors.value = props.getErrors.response.data.errors
  }
}

const resetErrors = () => {
  addingErrors.value = {
    name: null,
  }
}

// Watch for prop changes to reset form state
watch(() => props.isOpen, newVal => {
  if (newVal === true) {
    folderName.value = ''
  }
})
</script>

  <style scoped>
  /* Add any specific styles if needed */
  </style>

<template>
  <VDialog
    v-model="props.isOpen"
    max-width="500px"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="closeDialog" />
    <VCard title="Update Folder">
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
            :error-messages="updatingErrors.name"
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
              Update
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
  folder: Object,
  getErrors: Object,
})

const emit = defineEmits(['update:isOpen', 'getFolders'])

const folderStore = useFolderStore()
const $route = useRoute()
const toast = useToast()
const projectUuid = $route.params.id
const folderName = ref(props.folder?.name ?? '')
const createFolderForm = ref()

const updatingErrors = ref({
  name: null,
})

// Close dialog function
const closeDialog = () => {
  emit('update:isOpen', false)
}

const submit = () => {
  createFolderForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      resetErrors()

      try {
        const payload = {
          'name': folderName.value,
        }

        await folderStore.update(props.folder.uuid, payload)
        if(props.getErrors) {
          showError()
        } else{
          emit('getFolders')
          closeDialog()
          toast.success('Folder updated successfully')
        }

        // toast.success('Folder updated successfully')
        // closeDialog()
        // emit('getFolders')
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
    updatingErrors.value = props.getErrors.response.data.errors
  }
}

const resetErrors = () => {
  updatingErrors.value = {
    name: null,
  }
}

// Watch for prop changes to reset form state
watch(() => props.isOpen, newVal => {
  if (newVal === true) {
    folderName.value = ''
  }
  if(props.folder){
    folderName.value = props.folder?.name
  }
})
</script>

<style scoped>
/* Add any specific styles if needed */
</style>

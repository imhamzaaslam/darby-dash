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
            label="Folder Name"
            :rules="[requiredValidator]"
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
})

const emit = defineEmits(['update:isOpen'])

const folderStore = useFolderStore()
const $route = useRoute()
const toast = useToast()
const projectUuid = $route.params.id
const folderName = ref('')
const createFolderForm = ref()
  
// Close dialog function
const closeDialog = () => {
  emit('update:isOpen', false)
}

const submit = () => {
  createFolderForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        const payload = {
          'name': folderName.value,
        }

        await folderStore.create(projectUuid, payload)
        toast.success('Folder created successfully')
        closeDialog()
      } catch (error) {
        console.error(error)
      }
    }
  })
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
  
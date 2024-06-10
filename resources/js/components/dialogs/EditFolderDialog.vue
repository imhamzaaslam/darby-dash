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
})
  
const emit = defineEmits(['update:isOpen', 'getFolders'])
  
const folderStore = useFolderStore()
const $route = useRoute()
const toast = useToast()
const projectUuid = $route.params.id
const folderName = ref(props.folder?.name ?? '')
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
  
        await folderStore.update(props.folder.uuid, payload)
        toast.success('Folder updated successfully')
        closeDialog()
        emit('getFolders')
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
  if(props.folder){
    folderName.value = props.folder?.name
  }
})
</script>
    
<style scoped>
/* Add any specific styles if needed */
</style>

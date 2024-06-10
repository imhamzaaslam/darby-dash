<template>
  <VDialog
    v-model="props.isOpen"
    max-width="500px"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="closeDialog" />
    <VCard title="Create Folder">
      <VForm
        ref="form"
        @submit.prevent="submit"
      >
        <VCardText>
          <VTextField
            v-model="folderName"
            label="Folder Name"
            required
          />
        </VCardText>
        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            type="submit"
            color="primary"
          >
            Create
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
import { ref, watch } from 'vue'
  
// Define props
const props = defineProps({
  isOpen: Boolean,
})
  
// Define emits
const emit = defineEmits(['update:isOpen', 'createFolder'])
  
const folderName = ref('')
  
// Close dialog function
const closeDialog = () => {
  emit('update:isOpen', false)
  folderName.value = ''
}
  
// Submit function
const submit = () => {
  emit('createFolder', folderName.value)
  closeDialog()
}
  
// Watch for prop changes to reset form state
watch(() => props.isOpen, newVal => {
  if (newVal === false) {
    folderName.value = ''
  }
})
</script>
  
  <style scoped>
  /* Add any specific styles if needed */
  </style>
  
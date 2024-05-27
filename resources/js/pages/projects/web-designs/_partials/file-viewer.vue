<template>
  <VDialog
    :model-value="show"
    max-width="900"
    @update:model-value="handleClose"
  >
    <VCard>
      <VCardTitle>
        <span>{{ file.name }}</span>
        <VIcon
          size="small"
          class="float-right tabler-square-rounded-x-filled"
          @click="handleClose"
        />
      </VCardTitle>
      <VCardText>
        <template v-if="file.type.startsWith('image/')">
          <img
            :src="file.url"
            :alt="file.name"
            class="full-width-image"
          >
        </template>
        <template v-else>
          <iframe
            v-if="file.type === 'application/pdf'"
            :src="file.url"
            width="100%"
            height="600px"
          />
          <template v-else>
            <p>{{ fileContent }}</p>
          </template>
        </template>
      </VCardText>
    </VCard>
  </VDialog>
</template>
    
<script setup>
import { ref, watch } from 'vue'
    
const props = defineProps({
  show: Boolean,
  file: Object,
})
  
const emit = defineEmits(['update:show'])
    
const fileContent = ref('')
    
const handleClose = () => {
  emit('update:show', false)
}
    
watch(props.file, newFile => {
  if (newFile && !newFile.type.startsWith('image/') && newFile.type !== 'application/pdf') {
    const reader = new FileReader()
  
    reader.onload = e => {
      fileContent.value = e.target.result
    }
    reader.readAsText(new File([newFile.url], newFile.name))
  }
}, { immediate: true })
</script>
    
    <style scoped>
    .full-width-image {
      width: 100%;
      height: auto;
    }
    </style>
    
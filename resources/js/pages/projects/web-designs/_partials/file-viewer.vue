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
            class="uploaded-image"
          >
        </template>
        <template v-else-if="file.type === 'application/pdf'">
          <embed
            :src="file.url"
            width="100%"
            height="600px"
            type="application/pdf"
          >
        </template>
        <template v-else-if="file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'">
          <div v-html="wordContent" />
        </template>
        <template v-else-if="file.type === 'text/plain'">
          <pre>{{ textContent }}</pre>
        </template>
        <template v-else>
          <p>File format not supported for preview.</p>
        </template>
      </VCardText>
    </VCard>
  </VDialog>
</template>
<script setup>
import { ref, watch } from 'vue'
import mammoth from 'mammoth'

const props = defineProps({
  show: {
    type: Boolean,
    required: true,
  },
  file: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['update:show'])

const wordContent = ref('')
const textContent = ref('')

const handleClose = () => {
  emit('update:show', false)
}

watch(() => props.file, async newFile => {
  if (newFile.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
    const response = await fetch(newFile.url)
    const arrayBuffer = await response.arrayBuffer()
    const result = await mammoth.convertToHtml({ arrayBuffer })

    wordContent.value = result.value
  } else if (newFile.type === 'text/plain') {
    const response = await fetch(newFile.url)

    textContent.value = await response.text()
  }
})
</script>
<style scoped>
.uploaded-image {
  width: 100%;
  height: auto;
}
</style>

<template>
  <VDialog
    :model-value="props.isSaveTemplateModalOpen"
    :width="$vuetify.display.smAndDown ? 'auto' : 600"
    @update:model-value="dialogVisibleUpdate"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="$emit('update:isSaveTemplateModalOpen', false)" />

    <VCard
      title="Save as Template"
      class="pricing-dialog"
    >
      <VForm
        ref="saveTemplateForm"
        @submit.prevent="saveTemplate"
      >
        <VCardText>
          <AppTextField
            v-model="templateName"
            label="Template Name *"
            autofocus
            :rules="[requiredValidator]"
          />
        </VCardText>
        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            class="custom-secondary-btn"
            @click="$emit('update:isSaveTemplateModalOpen', false)"
          >
            Cancel
          </VBtn>
          <VBtn
            :disabled="loadStatus === 1"
            class="custom-btn-style"
            type="submit"
            @click="saveTemplateForm?.validate()"
          >
            <VProgressCircular
              v-if="loadStatus === 1"
              indeterminate
              size="16"
              color="white"
            />
            <span v-if="loadStatus === 1">Processing...</span>
            <span v-else>Save</span>
          </VBtn>
        </VCardText>
      </VForm>
    </VCard>
  </VDialog>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue'
import { useToast } from "vue-toastification"
import { useTemplateStore } from "@/store/templates"


const props = defineProps({
  isSaveTemplateModalOpen: {
    type: Boolean,
    required: true,
  },
  selectedProject: {
    type: String,
    required: true,
  },
})


const emit = defineEmits(['update:isSaveTemplateModalOpen'])

const dialogVisibleUpdate = val => {
  emit('update:isSaveTemplateModalOpen', val)
}

const templateStore = useTemplateStore()
const toast = useToast()

const saveTemplateForm = ref()
const templateName = ref(null)

const loadStatus = computed(() => templateStore.getLoadStatus)

async function saveTemplate() {
  saveTemplateForm.value?.validate().then(async ({ valid: isValid }) => {
    if (isValid) {
      try {
        const payload = {
          template_name: templateName.value.trim(),
        }

        await templateStore.create(props.selectedProject, payload)
        templateName.value = null

        dialogVisibleUpdate(false)

        toast.success('Save as template successfully', { timeout: 1000 })
      } catch (error) {
        console.error('Error saving template:', error)
        toast.error('Failed to saving template:', error)
      }
    }
  })
}
</script>

<template>
  <VNavigationDrawer
    :model-value="props.isEditTaskDrawerOpen"
    temporary
    location="end"
    width="600"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Edit Task Details"
      @cancel="$emit('update:isEditTaskDrawerOpen', false)"
    />

    <VDivider />

    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm
            ref="editTaskForm"
            @submit.prevent="submitEditTaskForm"
          >
            <VRow>
              <VCol cols="12">
                <AppTextField
                  ref="focusInput"
                  v-model="props.editingTask.name"
                  label="Task Name*"
                  :rules="[requiredValidator]"
                  placeholder="Task Name"
                />
              </VCol>
              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2 custom-btn-style"
                    :disabled="props.getLoadStatus === 1"
                    @click="editTaskForm?.validate()"
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
                    class="error-btn-customer-style"
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
import { useTemplateStore } from "@/store/templates"

const props = defineProps({
  isEditTaskDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchTemplate: Function,
  editingTask: Object,
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isEditTaskDrawerOpen'])

const toast = useToast()
const templateStore = useTemplateStore()

const focusInput = ref(null)
const editTaskForm = ref()
const isLoading= ref(false)

const handleDrawerModelValueUpdate = val => {
  emit('update:isEditTaskDrawerOpen', val)
}

const resetForm = () => {
  editProjectForm.value?.reset()
  emit('update:isEditTaskDrawerOpen', false)
}

async function submitEditTaskForm() {
  editTaskForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        isLoading.value = true

        const payload = {
          name: props.editingTask.name,
        }

        const res = await templateStore.updateTask(props.editingTask.uuid, payload)

        await props.fetchTemplate()
        emit('update:isEditTaskDrawerOpen', false)
        toast.success('Task updated successfully', { timeout: 1000 })
      } catch (error) {
        toast.error('Failed to update task:', error.message || error)
      }
    }
  })
}

watch(() => props.isEditTaskDrawerOpen, val => {
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
</style>

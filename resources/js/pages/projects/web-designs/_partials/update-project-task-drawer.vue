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
                  v-model="props.editingTask.name"
                  label="Title*"
                  :rules="[requiredValidator]"
                  placeholder="Title"
                />
              </VCol>
              <VCol cols="12">
                <TiptapEditor
                  v-model="props.editingTask.description"
                  class="border rounded basic-editor"
                />
              </VCol>
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="props.editingTask.start_date"
                  label="Start Date"
                  placeholder="Select Date"
                  locale="en-US"
                  clearable
                />
              </VCol>
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="props.editingTask.due_date"
                  label="Due Date"
                  placeholder="Select Date"
                  locale="en-US"
                  clearable
                />
              </VCol>
              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2"
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
import { useTaskStore } from "../../../../store/tasks"

const props = defineProps({
  isEditTaskDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchProjectTasks: Function,
  editingTask: Object,
  getLoadStatus: Boolean,
})

const emit = defineEmits(['update:isEditTaskDrawerOpen'])
const toast = useToast()
const taskStore = useTaskStore()

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

        const payload = {
          uuid: props.editingTask.uuid,
          name: props.editingTask.name,
          project_id: props.editingTask.project_id,
          description: props.editingTask.description,
          start_date: props.editingTask.start_date,
          due_date: props.editingTask.due_date,
        }

        const res = await taskStore.update(payload)

        isLoading.value = true
        emit('update:isEditTaskDrawerOpen', false)
        toast.success('Task updated successfully', { timeout: 1000 })
        await props.fetchProjectTasks()
        isLoading.value = false
      } catch (error) {
        toast.error('Failed to update task:', error.message || error)
      }
    }
  })
}
</script>

<style lang="scss">
.v-navigation-drawer__content {
overflow-y: hidden !important;
}
.basic-editor {
  .ProseMirror {
    block-size: 200px;
    outline: none;
    overflow-y: auto;
    padding-inline: 0.5rem;
  }
}
</style>
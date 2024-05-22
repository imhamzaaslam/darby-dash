<template>
  <VNavigationDrawer
    :model-value="props.isEditPhaseDrawerOpen"
    temporary
    location="end"
    width="600"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Edit Phase Details"
      @cancel="$emit('update:isEditPhaseDrawerOpen', false)"
    />

    <VDivider />

    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm
            ref="editPhaseForm"
            @submit.prevent="submitEditPhaseForm"
          >
            <VRow>
              <VCol cols="12">
                <AppTextField
                  v-model="props.editingPhase.name"
                  label="Title*"
                  :rules="[requiredValidator]"
                  placeholder="Title"
                />
              </VCol>
              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2"
                    :disabled="props.getLoadStatus === 1"
                    @click="editPhaseForm?.validate()"
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
import { usePhaseStore } from "../../../../store/phases"

const props = defineProps({
  isEditPhaseDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchProjectTasks: Function,
  editingPhase: Object,
  getLoadStatus: Boolean,
})

const emit = defineEmits(['update:isEditPhaseDrawerOpen'])
const toast = useToast()
const phaseStore = usePhaseStore()

const editPhaseForm = ref()
const isLoading= ref(false)

const handleDrawerModelValueUpdate = val => {
  emit('update:isEditPhaseDrawerOpen', val)
}

const resetForm = () => {
  editPhaseForm.value?.reset()
  emit('update:isEditPhaseDrawerOpen', false)
}

async function submitEditPhaseForm() {
  editTaskForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {

        const payload = {
          uuid: props.editingPhase.uuid,
          name: props.editingPhase.name,
          project_uuid: props.editingPhase.project_id,
        }

        const res = await phaseStore.update(payload)

        isLoading.value = true
        emit('update:isEditPhaseDrawerOpen', false)
        toast.success('Phase updated successfully', { timeout: 1000 })
        await props.fetchProjectTasks()
        isLoading.value = false
      } catch (error) {
        toast.error('Failed to update phase:', error.message || error)
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

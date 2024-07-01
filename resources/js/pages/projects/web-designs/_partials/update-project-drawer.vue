<template>
  <VNavigationDrawer
    :model-value="props.isEditDrawerOpen"
    temporary
    location="end"
    width="600"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Edit Project Details"
      @cancel="$emit('update:isEditDrawerOpen', false)"
    />

    <VDivider />

    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm
            ref="editProjectForm"
            @submit.prevent="submitEditProjectForm"
          >
            <VRow>
              <VCol cols="6">
                <AppTextField
                  v-model="props.editProjectDetails.title"
                  label="Title*"
                  :rules="[requiredValidator]"
                  placeholder="Title"
                  autofocus
                />
              </VCol>

              <VCol cols="6">
                <AppAutocomplete
                  v-model="selectedType"
                  label="Project Type*"
                  placeholder="Select Project Type"
                  :rules="[requiredValidator]"
                  :items="props.getProjectTypes"
                  item-title="name"
                  item-value="id"
                  @update:model-value="onProjectTypeChange"
                />
              </VCol>
              <VCol cols="12">
                <AppAutocomplete
                  v-model="props.editProjectDetails.member_ids"
                  :items="props.getMembers"
                  item-title="name"
                  item-value="id"
                  label="Members"
                  placeholder="Select Members"
                  multiple
                  clearable
                  clear-icon="tabler-x"
                />
              </VCol>
              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2"
                    :disabled="getLoadStatus === 1"
                    @click="editProjectForm?.validate()"
                  >
                    <span v-if="getLoadStatus === 1">
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
                    Cancel
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
import { useProjectStore } from "../../../../store/projects"

const props = defineProps({
  isEditDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchProjects: Function,
  getProjectTypes: Object,
  getMembers: Object,
  getProjectManagers: Object,
  editProjectDetails: Object,
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isEditDrawerOpen'])
const toast = useToast()
const projectStore = useProjectStore()

const editProjectForm = ref()
const isLoading= ref(false)

const handleDrawerModelValueUpdate = val => {
  emit('update:isEditDrawerOpen', val)
}

const resetForm = () => {
  editProjectForm.value?.reset()
  emit('update:isEditDrawerOpen', false)
}

function onProjectTypeChange(newValue) {
  props.editProjectDetails.project_type_id = newValue
}

const selectedType = computed(() => {
  return { id: props.editProjectDetails.project_type_id, name: props.editProjectDetails.project_type }
})

async function submitEditProjectForm() {
  editProjectForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {

        const payload = {
          id: props.editProjectDetails.id,
          uuid: props.editProjectDetails.uuid,
          title: props.editProjectDetails.title,
          project_type_id: props.editProjectDetails.project_type_id,
          member_ids: props.editProjectDetails.member_ids,
        }

        await projectStore.update(payload)

        isLoading.value = true
        emit('update:isEditDrawerOpen', false)
        toast.success('Project updated successfully', { timeout: 1000 })
        await props.fetchProjects()
        isLoading.value = false
      } catch (error) {
        toast.error('Failed to update project:', error.message || error)
      }
    }
  })
}
</script>

    <style lang="scss">
    .v-navigation-drawer__content {
      overflow-y: hidden !important;
    }
    </style>

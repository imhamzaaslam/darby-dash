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
              <VCol cols="12">
                <AppTextField
                  v-model="props.editProjectDetails.title"
                  label="Title*"
                  :rules="[requiredValidator]"
                  placeholder="Title"
                />
              </VCol>

              <VCol cols="6">
                <AppAutocomplete
                  v-model="props.editProjectDetails.project_type"
                  label="Project Type*"
                  placeholder="Select Project Type"
                  :rules="[requiredValidator]"
                  :items="props.getProjectTypes"
                  item-title="name"
                  item-value="id"
                />
              </VCol>

              <VCol cols="6">
                <AppAutocomplete
                  v-model="props.editProjectDetails.project_manager"
                  label="Project Manager*"
                  placeholder="Select Project Manager"
                  :rules="[requiredValidator]"
                  :items="props.getProjectManagers"
                  item-title="name"
                  item-value="id"
                />
              </VCol>
              <VCol cols="12">
                <AppAutocomplete
                  v-model="props.editProjectDetails.member_ids"
                  :items="props.getMembers"
                  item-title="name"
                  item-value="id"
                  label="Members*"
                  placeholder="Select Members"
                  multiple
                  clearable
                  clear-icon="tabler-x"
                />
              </VCol>
              <VCol cols="6">
                <AppTextField
                  v-model="props.editProjectDetails.est_hours"
                  label="Estimated Hours*"
                  :rules="[requiredValidator]"
                  placeholder="Estimated Hours"
                />
              </VCol>

              <VCol cols="6">
                <AppTextField
                  v-model="props.editProjectDetails.est_budget"
                  label="Estimated Budget*"
                  :rules="[requiredValidator]"
                  placeholder="Estimated Budget"
                />
              </VCol>
              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2"
                    @click="editProjectForm?.validate()"
                  >
                    Save
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

async function submitEditProjectForm() {
  editProjectForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {

        const payload = {
          id: props.editProjectDetails.id,
          uuid: props.editProjectDetails.uuid,
          title: props.editProjectDetails.title,
          project_type_id: props.editProjectDetails.project_type_id,
          project_manager_id: props.editProjectDetails.project_manager_id,
          member_ids: props.editProjectDetails.member_ids,
          est_hours: props.editProjectDetails.est_hours,
          est_budget: props.editProjectDetails.est_budget,
        }

        const res = await projectStore.update(payload)

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
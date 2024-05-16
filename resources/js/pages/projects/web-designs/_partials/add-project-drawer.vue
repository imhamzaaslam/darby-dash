<template>
  <VNavigationDrawer
    :model-value="props.isDrawerOpen"
    temporary
    location="end"
    width="600"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Add Project Details"
      @cancel="$emit('update:isDrawerOpen', false)"
    />

    <VDivider />

    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm
            ref="addProjectForm"
            @submit.prevent="submitAddProjectForm"
          >
            <VRow>
              <VCol cols="12">
                <AppTextField
                  v-model="newProjectDetails.title"
                  label="Title*"
                  :rules="[requiredValidator]"
                  placeholder="Title"
                />
              </VCol>

              <VCol cols="6">
                <AppAutocomplete
                  v-model="newProjectDetails.project_type_id"
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
                  v-model="newProjectDetails.project_manager_id"
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
                  v-model="newProjectDetails.member_ids"
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
                  v-model="newProjectDetails.est_hours"
                  label="Estimated Hours*"
                  :rules="[requiredValidator]"
                  placeholder="Estimated Hours"
                />
              </VCol>

              <VCol cols="6">
                <AppTextField
                  v-model="newProjectDetails.est_budget"
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
                    @click="addProjectForm?.validate()"
                  >
                    Save & Add Task
                  </VBtn>
                  <VBtn
                    color="error"
                    variant="tonal"
                    @click="resetForm"
                  >
                    Discard
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
import { useRouter } from 'vue-router'
import { useProjectStore } from "../../../../store/projects"

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchProjects: Function,
  getProjectTypes: Object,
  getMembers: Object,
  getProjectManagers: Object,
})

const emit = defineEmits(['update:isDrawerOpen'])
const toast = useToast()
const router = useRouter()
const projectStore = useProjectStore()

const addProjectForm = ref()
const isLoading= ref(false)

const newProjectDetails = ref({
  title: '',
  project_type_id: '',
  project_manager_id: '',
  member_ids: [],
  est_hours: '',
  est_budget: '',
})

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}

const resetForm = () => {
  addProjectForm.value?.reset()
  emit('update:isDrawerOpen', false)
}

async function submitAddProjectForm() {
  addProjectForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        const res = await projectStore.create(newProjectDetails.value)

        isLoading.value = true
        emit('update:isDrawerOpen', false)
        toast.success('Project added successfully', { timeout: 1000 })
        await props.fetchProjects()

        const newProjectID = projectStore.getProject.id

        router.push({ name: 'add-project-tasks', params: { project: 'web-designs', id: newProjectID } })

        isLoading.value = false
        newProjectDetails.value = {
          title: '',
          project_type_id: '',
          project_manager_id: '',
          member_ids: [],
          est_hours: '',
          est_budget: '',
        }
      } catch (error) {
        toast.error('Failed to add project:', error)
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

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
        <VCardText style="block-size: calc(100vh - 80px);">
          <VForm
            ref="addProjectForm"
            @submit.prevent="submitAddProjectForm"
          >
            <VRow>
              <VCol cols="6">
                <AppTextField
                  ref="focusInput"
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
              <VCol cols="12">
                <label>Select Members</label>
                <Multiselect
                  v-model="newProjectDetails.member_ids"
                  mode="tags"
                  placeholder="Select Members"
                  close-on-select
                  searchable
                  :options="props.getMembers"
                  class="bg-background multiselect-purple"
                  style="color: #000 !important;"
                />
              </VCol>
              <VCol cols="4">
                <AppTextField
                  v-model="newProjectDetails.budget_amount"
                  label="Project Budget*"
                  :rules="[requiredValidator]"
                  placeholder="0.00"
                  type="number"
                  class="no-arrows"
                  prepend-inner-icon="tabler-currency-dollar"
                />
              </VCol>
              <VCol
                cols="8"
                class="d-flex align-center"
              >
                <div class="d-flex">
                  <!-- Input Field -->
                  <AppTextField
                    v-model="newProjectDetails.bucks_share"
                    label="Darby Bucks Share*"
                    :rules="[requiredValidator]"
                    placeholder="0.00"
                    type="number"
                    class="no-arrows me-1"
                    :prepend-inner-icon="newProjectDetails.bucks_share_type === 'fixed' ? 'tabler-currency-dollar' : 'tabler-percentage'"
                  />

                  <!-- Dropdown to Select Type -->
                  <AppSelect
                    v-model="newProjectDetails.bucks_share_type"
                    :items="budgetTypes"
                    class="budget-type-select"
                    label="Share Type"
                    :rules="[requiredValidator]"
                    dense
                    hide-details
                  />
                </div>
              </VCol>

              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2"
                    :disabled="props.getLoadStatus === 1"
                    @click="addProjectForm?.validate()"
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
                      Save & Add Tasks
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
import { ref, nextTick } from 'vue'
import Multiselect from '@vueform/multiselect'
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
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isDrawerOpen'])
const toast = useToast()
const router = useRouter()
const projectStore = useProjectStore()

const focusInput = ref(null)
const addProjectForm = ref()
const isLoading= ref(false)

const newProjectDetails = ref({
  title: '',
  project_type_id: null,
  member_ids: [],
  budget_amount: '',
  bucks_share: '',
  bucks_share_type: 'fixed',
})

const budgetTypes = [
  { title: 'Fixed', value: 'fixed' },
  { title: 'Percentage', value: 'percentage' },
]

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

        const newProjectID = projectStore.getProject.uuid

        router.push({ name: 'add-project-tasks', params: { id: newProjectID } })

        isLoading.value = false
        newProjectDetails.value = {
          title: '',
          project_type_id: '',
          member_ids: [],
          budget_amount: '',
          bucks_share: '',
          bucks_share_type: 'fixed',
        }
      } catch (error) {
        toast.error('Failed to add project:', error)
      }
    }
  })
}

watch(() => props.isDrawerOpen, val => {
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
  </style>

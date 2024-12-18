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
              <VCol
                md="6"
                cols="12"
              >
                <AppAutocomplete
                  ref="focusField"
                  v-model="newProjectDetails.client_id"
                  label="Client*"
                  placeholder="Select Client"
                  :rules="[requiredValidator]"
                  :items="props.getClients"
                  item-title="name"
                  item-value="id"
                />
              </VCol>
              <VCol
                md="6"
                cols="12"
              >
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
              <VCol
                md="6"
                cols="12"
              >
                <AppTextField
                  v-model="newProjectDetails.title"
                  label="Title*"
                  :rules="[requiredValidator]"
                  placeholder="Title"
                />
              </VCol>
              <VCol
                md="6"
                cols="12"
              >
                <AppAutocomplete
                  v-model="newProjectDetails.project_manager_id"
                  label="Project Manager*"
                  placeholder="Select Project Manager"
                  :rules="[requiredValidator]"
                  :items="props.getProjectManagersList"
                  item-title="name"
                  item-value="id"
                />
              </VCol>
              <VCol cols="12">
                <label>Select Staff Members</label>
                <Multiselect
                  v-model="newProjectDetails.staff_ids"
                  mode="tags"
                  placeholder="Select Staff Members"
                  close-on-select
                  searchable
                  :options="props.getStaffList"
                  class="bg-background multiselect-purple"
                  style="color: #000 !important;"
                />
              </VCol>
              <VCol
                v-if="showTemplateField"
                cols="12"
              >
                <AppAutocomplete
                  v-model="newProjectDetails.template_id"
                  label="Choose From Templates"
                  placeholder="Select Template"
                  :items="props.getTemplates"
                  item-title="name"
                  item-value="id"
                />
              </VCol>
              <VCol
                md="6"
                cols="12"
              >
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
                v-if="showBucksShare"
                md="6"
                cols="12"
              >
                <AppTextField
                  v-model="newProjectDetails.bucks_share"
                  :label="`${generalSetting?.bucks_label} Share*`"
                  :rules="[requiredValidator]"
                  placeholder="0.00"
                  type="number"
                  class="no-arrows me-1"
                  prepend-inner-icon="tabler-percentage"
                />
              </VCol>

              <VCol
                v-if="generalSetting?.is_bucks_setting == 1"
                cols="12"
              >
                <VSwitch
                  v-model="showBucksShare"
                  :label="`Enable ${generalSetting?.bucks_label} Share`"
                  class="mb-3"
                />
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
import { useProjectStore } from "@/store/projects"
import { useAuthStore } from "@/store/auth"

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchProjects: Function,
  getProjectTypes: Object,
  getStaffList: Object,
  getClients: Object,
  getTemplates: Object,
  getProjectManagersList: Object,
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isDrawerOpen'])
const toast = useToast()
const router = useRouter()
const projectStore = useProjectStore()
const authStore = useAuthStore()

const focusField = ref(null)
const addProjectForm = ref()
const isLoading= ref(false)
const showTemplateField = ref(false)
const showBucksShare = ref(false)

const newProjectDetails = ref({
  client_id: null,
  project_manager_id: null,
  title: '',
  project_type_id: null,
  template_id: null,
  staff_ids: [],
  budget_amount: '',
  bucks_share: null,
})

// const budgetTypes = [
//   { title: 'Fixed', value: 'fixed' },
//   { title: 'Percentage', value: 'percentage' },
// ]

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
        // if (newProjectDetails.value.bucks_share_type === 'fixed') {
        //   if (parseFloat(newProjectDetails.value.bucks_share) > parseFloat(newProjectDetails.value.budget_amount)) {
        //     toast.error('Darby Bucks Share cannot be greater than Project Budget')

        //     return
        //   }
        // } else {
        //   if (parseFloat(newProjectDetails.value.bucks_share) > 100) {
        //     toast.error('Darby Bucks Share cannot be greater than 100%')

        //     return
        //   }
        // }
        if (parseFloat(newProjectDetails.value.bucks_share) > 100) {
          toast.error(`${generalSetting?.bucks_label} Share cannot be greater than 100%`)

          return
        }

        isLoading.value = true
        await projectStore.create(newProjectDetails.value)

        if(projectStore.getErrors) {
          toast.error('Failed to add project')
          isLoading.value = false

          return
        } else {
          emit('update:isDrawerOpen', false)
          toast.success('Project added successfully', { timeout: 1000 })
          await props.fetchProjects()

          const newProjectID = projectStore.getProject.uuid

          router.push({ name: 'add-project-tasks', params: { id: newProjectID } })

          isLoading.value = false
          newProjectDetails.value = {
            title: '',
            project_type_id: '',
            staff_ids: [],
            budget_amount: '',
            bucks_share: '',
          }
        }
      } catch (error) {
        toast.error('Failed to add project:', error)
      }
    }
  })
}

const generalSetting = computed(() => {
  return authStore.getGeneralSetting
})

watch(() => props.isDrawerOpen, val => {
  if (val) {
    nextTick(() => {
      const inputEl = focusField.value.$el.querySelector('input')
      if (inputEl) {
        inputEl.focus()
      }
    })
  }
})

watch(() => newProjectDetails.value.project_type_id, newTypeId => {
  showTemplateField.value = !!newTypeId
})
</script>

  <style lang="scss">
  .v-navigation-drawer__content {
    overflow-y: hidden !important;
  }
  </style>

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
                  ref="focusInput"
                  v-model="props.editProjectDetails.title"
                  label="Title*"
                  :rules="[requiredValidator]"
                  placeholder="Title"
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
                <label>Select Members</label>
                <Multiselect
                  v-model="props.editProjectDetails.member_ids"
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
                  v-model="props.editProjectDetails.budget_amount"
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
                    v-model="props.editProjectDetails.bucks_share"
                    label="Darby Bucks Share*"
                    :rules="[requiredValidator]"
                    placeholder="0.00"
                    type="number"
                    class="no-arrows me-1"
                    :prepend-inner-icon="props.editProjectDetails.bucks_share_type === 'fixed' ? 'tabler-currency-dollar' : 'tabler-percentage'"
                  />

                  <!-- Dropdown to Select Type -->
                  <AppSelect
                    v-model="props.editProjectDetails.bucks_share_type"
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
import { ref, nextTick } from 'vue'
import Multiselect from '@vueform/multiselect'
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

const focusInput = ref(null)
const editProjectForm = ref()
const isLoading= ref(false)

const handleDrawerModelValueUpdate = val => {
  emit('update:isEditDrawerOpen', val)
}

const resetForm = () => {
  editProjectForm.value?.reset()
  emit('update:isEditDrawerOpen', false)
}

const budgetTypes = [
  { title: 'Fixed', value: 'fixed' },
  { title: 'Percentage', value: 'percentage' },
]

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
          budget_amount: props.editProjectDetails.budget_amount,
          bucks_share: props.editProjectDetails.bucks_share,
          bucks_share_type: props.editProjectDetails.bucks_share_type,
        }

        if (payload.bucks_share_type === 'fixed') {
          if (parseFloat(payload.bucks_share) > parseFloat(payload.budget_amount)) {
            toast.error('Darby Bucks Share cannot be greater than Project Budget')

            return
          }
        } else {
          if (parseFloat(payload.bucks_share) > 100) {
            toast.error('Darby Bucks Share cannot be greater than 100%')

            return
          }
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

watch(() => props.isEditDrawerOpen, val => {
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

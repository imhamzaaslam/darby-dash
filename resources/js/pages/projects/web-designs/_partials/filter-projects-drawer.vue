<template>
  <VNavigationDrawer
    :model-value="isFilterDrawerOpen"
    temporary
    location="end"
    width="400"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Filter Projects"
      @cancel="$emit('update:isFilterDrawerOpen', false)"
    />

    <VDivider />

    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm
            ref="filterProjectsForm"
            @submit.prevent="applyFilters"
          >
            <VRow>
              <!-- Search filter -->
              <VCol cols="12">
                <AppTextField
                  v-model="filterDetails.query"
                  label="Name"
                  placeholder="Project Name"
                />
              </VCol>

              <!-- Project Type filter -->
              <VCol cols="12">
                <AppAutocomplete
                  v-model="filterDetails.project_type_id"
                  label="Project Type"
                  placeholder="Select Project Type"
                  :items="props.getProjectTypes"
                  item-title="name"
                  item-value="id"
                />
              </VCol>

              <!-- Project Manager filter -->
              <VCol cols="12">
                <AppAutocomplete
                  v-model="filterDetails.project_manager_id"
                  label="Project Manager"
                  placeholder="Select Project Manager"
                  :items="props.getProjectManagers"
                  item-title="name"
                  item-value="id"
                />
              </VCol>

              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2"
                    @click="filterProjectsForm?.validate()"
                  >
                    Apply
                  </VBtn>
                  <VBtn
                    color="error"
                    variant="tonal"
                    @click="resetFilters"
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
import { useRouter } from 'vue-router'
import { useProjectStore } from "../../../../store/projects"

const props = defineProps({
  isFilterDrawerOpen: {
    type: Boolean,
    required: true,
  },
  fetchProjects: Function,
  getProjectTypes: Object,
  getProjectManagers: Object,
})

const emit = defineEmits(['update:isFilterDrawerOpen'])

const filterDetails = ref({
  query: '',
  project_type_id: null,
  project_manager_id: null,
})

const projectStore = useProjectStore()

const handleDrawerModelValueUpdate = val => {
  emit('update:isFilterDrawerOpen', val)
}

const applyFilters = () => {
  const payload = {
    query: filterDetails.value.query,
    project_type_id: filterDetails.value.project_type_id,
    project_manager_id: filterDetails.value.project_manager_id,
  }
}

const resetFilters = () => {
  filterDetails.value = {
    query: '',
    project_type_id: null,
    project_manager_id: null,
  }
}
</script>

<style lang="scss">
.v-navigation-drawer__content {
    overflow-y: hidden !important;
}
</style>

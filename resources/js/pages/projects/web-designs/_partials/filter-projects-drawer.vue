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
          <VForm @submit.prevent="filter">
            <VRow>
              <!-- Search filter -->
              <VCol cols="12">
                <AppTextField
                  v-model="filterDetails.searchQuery"
                  label="Name"
                  placeholder="Project Name"
                  autofocus
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
                    :disabled="props.getLoadStatus === 1"
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
                      Apply
                    </span>
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

const props = defineProps({
  isFilterDrawerOpen: {
    type: Boolean,
    required: true,
  },
  applyFilters: Function,
  getProjectTypes: Object,
  selectedProjectType: Number,
  getProjectManagers: Object,
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isFilterDrawerOpen'])

watch(() => props.selectedProjectType, (val) => {
  filterDetails.value.project_type_id = val
})

const filterDetails = ref({
  searchQuery: '',
  project_type_id: null,
  project_manager_id: null,
})

const handleDrawerModelValueUpdate = val => {
  emit('update:isFilterDrawerOpen', val)
}

const filter = () => {
  props.selectedProjectType = filterDetails.value.project_type_id
  props.applyFilters(filterDetails.value.searchQuery, filterDetails.value.project_type_id, filterDetails.value.project_manager_id)
  emit('update:isFilterDrawerOpen', false)
}

const resetFilters = () => {
  filterDetails.value = {
    searchQuery: '',
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

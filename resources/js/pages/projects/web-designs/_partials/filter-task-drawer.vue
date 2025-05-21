<template>
  <VNavigationDrawer
    :model-value="isFilterDrawerOpen"
    temporary
    location="end"
    width="600"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <AppDrawerHeaderSection
      title="Filter Tasks"
      @cancel="handleDrawerModelValueUpdate(false)"
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
                  ref="focusInput"
                  v-model="filterDetails.searchQuery"
                  label="Task Name"
                />
              </VCol>

              <!-- Sort by field -->
              <VCol cols="6">
                <AppSelect
                  v-model="filterDetails.sortBy"
                  :items="sortOptions"
                  label="Sort By"
                  item-title="name"
                  item-value="value"
                />
              </VCol>

              <!-- Sort direction -->
              <VCol cols="6">
                <AppSelect
                  v-model="filterDetails.sortDirection"
                  :items="sortDirections"
                  label="Sort Direction"
                  item-title="name"
                  item-value="value"
                />
              </VCol>

              <!-- Assignee list (multi-select) -->
              <VCol cols="6">
                <AppSelect
                  v-model="filterDetails.assignees"
                  :items="props.assigneesList"
                  item-title="name"
                  item-value="id"
                  label="Assignees"
                  placeholder="Select Assignees"
                  multiple
                  clearable
                  clear-icon="tabler-x"
                >
                  <template #selection="{ item }">
                    <VChip>
                      <template #prepend>
                        <VAvatar
                          v-if="item.raw?.info?.avatar"
                          start
                          :image="getImageUrl(item.raw?.info?.avatar.path)"
                        />
                        <VAvatar
                          v-else
                          size="25"
                          class="text-white bg-primary"
                          variant="tonal"
                        >
                          <small>{{ avatarText(item.raw.name_first + ' ' + item.raw.name_last) }}</small>
                        </VAvatar>
                      </template>

                      <span class="ms-1">{{ item.raw.name_first }} {{ item.raw.name_last }}</span>
                    </VChip>
                  </template>
                </AppSelect>
              </VCol>

              <!-- Status list (multi-select) -->
              <VCol cols="6">
                <AppSelect
                  v-model="filterDetails.statuses"
                  :items="statusList"
                  label="Statuses"
                  placeholder="Select Status"
                  item-title="name"
                  item-value="value"
                  multiple
                  clearable
                />
              </VCol>

              <!-- Due date range -->
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="filterDetails.dueDate"
                  label="Due Date"
                  placeholder="Select date"
                  :config="{ mode: 'range' }"
                  clearable
                />
              </VCol>

              <!-- Created at range -->
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="filterDetails.createdAt"
                  label="Created At"
                  placeholder="Select date"
                  :config="{ mode: 'range' }"
                  clearable
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
import { ref } from 'vue'

const props = defineProps({
  isFilterDrawerOpen: {
    type: Boolean,
    required: true,
  },
  selectedProject: {
    type: String,
    required: true,
  },
  assigneesList: {
    type: Array,
  },
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isFilterDrawerOpen', 'applyFilters'])

const focusInput = ref(null)

const filterDetails = ref({
  searchQuery: '',
  assignees: [],
  statuses: [],
  createdAt: null,
  dueDate: null,
  estTimeFrom: null,
  estTimeTo: null,
  sortBy: 'created_at',
  sortDirection: 'asc',
})

const statusList = ref([
  { name: 'Pending', value: '1' },
  { name: 'In Progress', value: '2' },
  { name: 'Completed', value: '3' },
])

const sortOptions = ref([
  { name: 'Task Name', value: 'name' },
  { name: 'Assignee', value: 'assignee' },
  { name: 'Status', value: 'status' },
  { name: 'EST Time', value: 'est_time' },
  { name: 'Created At', value: 'created_at' },
  { name: 'Due Date', value: 'due_date' },
])

const sortDirections = ref([
  { name: 'Ascending', value: 'asc' },
  { name: 'Descending', value: 'desc' },
])

const handleDrawerModelValueUpdate = val => {
  emit('update:isFilterDrawerOpen', val)
  resetFilters()
}

const filter = () => {
  emit('applyFilters', filterDetails.value)
  handleDrawerModelValueUpdate(false)
}

const resetFilters = () => {
  filterDetails.value = {
    searchQuery: '',
    assignees: [],
    statuses: [],
    createdAt: null,
    dueDate: null,
    estTimeFrom: null,
    estTimeTo: null,
    sortBy: 'created_at',
    sortDirection: 'asc',
  }
}

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

watch(() => props.isFilterDrawerOpen, val => {
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

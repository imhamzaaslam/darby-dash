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
      title="Filter Members"
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
                  ref="focusInput"
                  v-model="filterDetails.name"
                  label="Name"
                  placeholder="Search by Name"
                />
              </VCol>

              <!-- Email filter -->
              <VCol cols="12">
                <AppTextField
                  v-model="filterDetails.email"
                  label="Email"
                  placeholder="Search by Email"
                />
              </VCol>

              <!-- Project Type filter -->
              <VCol cols="12">
                <AppAutocomplete
                  v-model="filterDetails.roleId"
                  label="Role"
                  placeholder="Select Type"
                  :items="props.getRoles"
                  item-title="name"
                  item-value="id"
                />
              </VCol>

              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    class="me-2 custom-btn-style"
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
                    class="error-btn-customer-style"
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
import { ref, nextTick } from 'vue'

const props = defineProps({
  isFilterDrawerOpen: {
    type: Boolean,
    required: true,
  },
  applyFilters: Function,
  getRoles: Object,
  selectedRole: Number,
  getLoadStatus: Number,
})

const emit = defineEmits(['update:isFilterDrawerOpen'])

const focusInput = ref(null)

const filterDetails = ref({
  name: '',
  email: '',
  roleId: null,
})

const handleDrawerModelValueUpdate = val => {
  emit('update:isFilterDrawerOpen', val)
}

const filter = () => {
  props.selectedRole = filterDetails.value.roleId
  props.applyFilters(filterDetails.value.name, filterDetails.value.email, filterDetails.value.roleId)
  emit('update:isFilterDrawerOpen', false)
}

const resetFilters = () => {
  filterDetails.value = {
    name: '',
    email: '',
    roleId: null,
  }
  filter()
}

watch([() => props.selectedRole, () => props.isFilterDrawerOpen], ([newSelectedRole, newIsFilterDrawerOpen]) => {
  if (newSelectedRole !== filterDetails.value.roleId) {
    filterDetails.value.roleId = newSelectedRole
  }

  if (newIsFilterDrawerOpen) {
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

<template>
  <VCard>
    <VCardText class="d-flex justify-space-between align-center flex-wrap gap-4">
      <h3>
        Bucks Overview
      </h3>
      <div style="inline-size: 272px;">
        <!--
          <AppTextField
          v-model="search"
          placeholder="Search Payments"
          @input="onInput($event.target.value)"
          />
        -->
      </div>
    </VCardText>

    <VDivider />
    <VDataTable
      :headers="headers"
      :items="bucksData"
      hide-default-footer
      class="text-no-wrap"
      :loading="isLoading"
    >
      <template #item.role="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.role }}</span>
        </div>
      </template>
      <template #item.percentage="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.percentage }}%</span>
        </div>
      </template>
      <template #item.actions="{ item }">
        <div class="d-flex gap-1">
          <VBtn
            color="primary"
            size="small"
            @click="editRole(item)"
          >
            <VIcon
              icon="tabler-edit"
              size="18"
              class="me-1"
            />
            <span class="d-none d-sm-block">Edit</span>
          </VBtn>
        </div>
      </template>
      <template #bottom>
        <div class="mb-4" />
      </template>
    </VDataTable>
  </VCard>
</template>

<script setup>
import { ref } from 'vue'
import Loader from '@/components/Loader.vue'

const avatarImage = 'path/to/your/avatar/image.png' // Update with your image path

// Static data for roles
const bucksData = ref([
  { role: 'Admin', percentage: 40 },
  { role: 'Project Manager', percentage: 30 },
  { role: 'Staff', percentage: 20 },
  { role: 'Client', percentage: 10 },
])

const headers = [
  {
    title: 'Role',
    key: 'role',
  },
  {
    title: 'Percentage',
    key: 'percentage',
  },
  {
    title: 'Actions',
    key: 'actions',
  },
]

const isLoading = ref(false)

function editRole(item) {
  // Logic for editing the role
  console.log('Editing role:', item)
}
</script>

<style scoped>
  .table-wrapper {
    inline-size: auto;
    overflow-x: auto;
  }
</style>

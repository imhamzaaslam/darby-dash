<template>
  <Loader v-if="loadStatus === 1" />
  <VCard>
    <VCardText class="d-flex justify-space-between align-center flex-wrap gap-4">
      <h3>
        Bucks Overview
      </h3>
      <div class="d-flex gap-5">
        <h4>
          Total Amount: <span class="font-weight-medium text-primary">${{ projectBucks?.project?.budget_amount }}</span>
        </h4>
        <h4>
          Bucks Shares: <span class="font-weight-medium text-primary">${{ projectBucks?.project?.bucks_share }}</span>
        </h4>
        <h4>
          Share Type: <span class="font-weight-medium text-primary">{{ projectBucks?.project?.bucks_share_type }}</span>
        </h4>
      </div>
    </VCardText>

    <VDivider />
    <VDataTable
      :headers="headers"
      :items="projectBucks?.bucks"
      hide-default-footer
      class="text-no-wrap"
    >
      <template #item.role="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.role_name }}</span>
        </div>
      </template>
      <template #item.percentage="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.bucks_share_type === 'percentage' ? item.bucks_share + '%' : '$' + item.bucks_share }}</span>
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
import { useProjectBucksStore } from "@/store/project_bucks"
import { useRoute } from 'vue-router'
import { useToast } from "vue-toastification"

onBeforeMount(async () => {
  await fetchProjectBucks()
})

const projectBucksStore = useProjectBucksStore()
const route = useRoute()
const toast = useToast()
const projectUuid = route.params.id

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
    sortable: false,
  },
  {
    title: 'Percentage',
    key: 'percentage',
    sortable: false,
  },
  {
    title: 'Actions',
    key: 'actions',
    sortable: false,
  },
]

const fetchProjectBucks = async () => {
  try {
    await projectBucksStore.getBucks(projectUuid)
  } catch (error) {
    toast.error('Error fetching project:', error)
  }
}

function editRole(item) {
  // Logic for editing the role
  console.log('Editing role:', item)
}

const projectBucks = computed(() =>{
  return projectBucksStore.getBucksDetails
})

const loadStatus = computed(() => {
  return projectBucksStore.getLoadStatus
})
</script>

<style scoped>
  .table-wrapper {
    inline-size: auto;
    overflow-x: auto;
  }
</style>

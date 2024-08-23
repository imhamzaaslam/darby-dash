<template>
  <VCard>
    <VCardText class="d-flex justify-space-between align-center flex-wrap gap-4">
      <h3>
        Manage Tasks
      </h3>
      <div style="inline-size: 272px;">
        <!--
          <AppTextField
          v-model="search"
          placeholder="Search Tasks"
          @input="onInput($event.target.value)"
          /> 
        -->
      </div>
    </VCardText>
  
    <VDivider />
    <VDataTable
      :headers="headers"
      :items-per-page="options.itemsPerPage"
      :items="getBucksTasks"
      item-value="name"
      hide-default-footer
      class="text-no-wrap"
      :loading="isLoading"
      @update:options="updateOptions"
    >
      <template #item.name="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.name }}</span>
        </div>
      </template>
      <template #item.status="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.status.name }}</span>
        </div>
      </template>
      <template #item.approval="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0 text-uppercase">{{ item.approval_status }}</span>
        </div>
      </template>
      <template #item.assignee="{ item }">
        <VChip
          v-for="assignee in item.assignees_bucks"
          :key="assignee.id"
          color="primary"
          text-color="white"
          class="me-2"
          size="small"
        >
          {{ assignee.user_name }} (${{ assignee.bucks_amount }})
        </VChip>
      </template>
      <template #item.actions="{ item }">
        <div class="d-flex gap-1">
          <span>
            <VBtn
              color="success"
              icon
              size="x-small"
              :disabled="approvalUpdating || item.approval_status == 'approved' || item.status.name !== 'COMPLETED'"
              @click="approveTask(item)"
            >
              <VIcon
                size="large"
                icon="tabler-check"
              />
            </VBtn>
            <VTooltip
              activator="parent"
              location="top"
            >
              <span class="text-xs">Approve</span>
            </VTooltip>
          </span>
          <span>
            <VBtn
              color="error"
              icon
              size="x-small"
              :disabled="approvalUpdating || item.approval_status == 'rejected' || item.status.name !== 'COMPLETED'"
              @click="rejectTask(item)"
            >
              <VIcon
                size="large"
                icon="tabler-x"
              />
            </VBtn>
            <VTooltip
              activator="parent"
              location="top"
            >
              <span class="text-xs">Reject</span>
            </VTooltip>
          </span>
        </div>
        <!-- <VBtn
          color="success"
          class="me-2"
          size="small"
          :disabled="approvalUpdating || item.approval_status == 'approved' || item.status.name !== 'completed'"
          @click="approveTask(item)"
        >
          Approve
        </VBtn>
        <VBtn
          color="error"
          size="small"
          :disabled="approvalUpdating || item.approval_status == 'rejected' || item.status.name !== 'completed'"
          @click="rejectTask(item)"
        >
          Reject
        </VBtn> -->
      </template>  
      <template #bottom>
        <VCardText class="pt-2">
          <div
            v-if="isLoading"
            class="text-center"
          >
            <VProgressCircular
              :size="30"
              width="3"
              indeterminate
              color="primary"
            />
          </div>
        </VCardText>
        <TablePagination
          v-model:page="options.page"
          :items-per-page="options.itemsPerPage"
          :total-items="getBucksTasks.length"
          @update:page="handlePageChange"
        />
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
import { debounce } from 'lodash'
  
onBeforeMount(async () => {
  await fetchBucksTasks()
})
  
const projectBucksStore = useProjectBucksStore()
const route = useRoute()
const toast = useToast()
const projectUuid = route.params.id
const approvalUpdating = ref(false)
  
const isLoading = ref(false)
const search = ref('')
const options = ref({ page: 1, itemsPerPage: 10, orderBy: '', order: '' })

const headers = [
  { title: 'Task', key: 'name', sortable: false, width: '15%' },
  { title: 'Status', key: 'status', sortable: false, width: '15%' },
  { title: 'Approval', key: 'approval', sortable: false, width: '15%' },
  { title: 'Assignee', key: 'assignee', sortable: false, width: '45%' },
  { title: 'Actions', key: 'actions', sortable: false },
]

const fetchBucksTasks = async () => {
  await projectBucksStore.fetchBucksTasks(projectUuid)
}
  
const handlePageChange = async page => {
  options.value.page = page
  await fetchBucksTasks()
}
  
const onFilter = async value => {
  options.value.page = 1
  search.value = value
  await fetchBucksTasks()
}
  
const debouncedFilter = debounce(onFilter, 300)
  
const onInput = value => {
  debouncedFilter(value)
}
  
const updateOptions = async updateOptions => {
  const sortKeyMap = {
    name: 'name',
    amount: 'amount',
    status: 'status',
  }
  
  const sortByKey = updateOptions.sortBy[0]?.key
  if (sortByKey && sortKeyMap[sortByKey]) {
    options.value.orderBy = sortKeyMap[sortByKey]
  }
    
  options.value.order = updateOptions.sortBy[0]?.order
  await fetchBucksTasks()
}

const approveTask = async task => {
  try {
    approvalUpdating.value = true
    
    const payload = {
      approval_status: 'approved',
    }
    
    await projectBucksStore.updateTaskApproval(projectUuid, task.id, payload)
    if(getErrors.value) {
      toast.error('Something went wrong. Please try again later')
      approvalUpdating.value = false
    } else {
      toast.success('Task approved successfully')
      await fetchBucksTasks()
      approvalUpdating.value = false
    }
  } catch (error) {
    toast.error('Error approving task:', error)
  }
}

const rejectTask = async task => {
  try {
    approvalUpdating.value = true
    
    const payload = {
      approval_status: 'rejected',
    }
    
    await projectBucksStore.updateTaskApproval(projectUuid, task.id, payload)
    if(getErrors.value) {
      toast.error('Something went wrong. Please try again later')
      approvalUpdating.value = false
    } else {
      toast.success('Task rejected successfully')
      await fetchBucksTasks()
      approvalUpdating.value = false
    }
  } catch (error) {
    toast.error('Error rejecting task:', error)
  }
}
  
const getBucksTasks = computed(() => {
  return projectBucksStore.getBucksTasks
})
  
const getErrors = computed(() => {
  return projectBucksStore.getErrors
})
  
// const getStatusCode = computed(() => {
//   return projectBucksStore.getStatusCode
// })
  
// const getLoadStatus = computed(() => {
//   return projectBucksStore.getLoadStatus
// })
</script>
  
  <style scoped>
  .table-wrapper {
      inline-size: auto;
      overflow-x: auto;
  }
  </style>
  
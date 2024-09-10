<template>
  <VCard>
    <VCardText class="d-flex justify-space-between align-center flex-wrap gap-4">
      <!--
        <h3>
        Manage Tasks
        </h3>
        <div style="inline-size: 272px;">
        <AppTextField
        v-model="search"
        placeholder="Search Tasks"
        @input="onInput($event.target.value)"
        /> 
        </div> 
      -->
      <VTabs v-model="currentTab">
        <VTab>Pending Approval</VTab>
        <VTab>Earned</VTab>
        <VTab>Rejected</VTab>
      </VTabs>
    </VCardText>
  
    <VDivider />
    <VWindow 
      v-model="currentTab"
      class="p-4"
    >
      <VWindowItem :value="0">
        <VDataTable
          :headers="headers"
          :items-per-page="options.itemsPerPage"
          :items="pendingApprovalTasks"
          item-value="name"
          hide-default-footer
          class="text-no-wrap"
          :loading="isLoading"
          @update:options="updateOptions"
        >
          <template #item.name="{ item }">
            <span class="text-sm text-truncate mb-0">
              {{ item.name }}
              <VChip
                :label="false"
                color="primary"
                size="x-small"
              >
                #{{ item.id }}
              </VChip>
            </span>
          </template>
          <template #item.status="{ item }">
            <span class="text-sm text-truncate mb-0">{{ item.status.name }}</span>
          </template>
          <template #item.amount="{ item }">
            <span class="text-sm text-truncate mb-0">${{ item.bucks_amount }}</span>
          </template>
          <template #item.approval="{ item }">
            <VChip
              :color="getApprovalStatusColor(item.approval_status)"
              text-color="white"
              class="me-2"
              size="small"
            >
              <span class="text-sm text-truncate mb-0 text-uppercase">{{ item.approval_status }}</span>
            </VChip>
          </template>
          <template 
            v-if="authStore.isAdmin || authStore.isManager"
            #item.assignee="{ item }"
          >
            <VChip>
              <VAvatar
                start
                size="25"
                class="text-white bg-primary"
              >
                <small>{{ avatarText(item.assignee?.name_first + ' ' + item.assignee?.name_last) }}</small>
              </VAvatar>
              <span>{{ item.assignee?.name_first }} {{ item.assignee?.name_last }}</span>
            </VChip>
          </template>
          <template
            v-if="authStore.isAdmin || authStore.isManager"
            #item.actions="{ item }"
          >
            <div class="d-flex gap-1">
              <span>
                <VBtn
                  color="success"
                  icon
                  size="x-small"
                  :disabled="approvalUpdating || item.approval_status == 'approved' || item.status.name !== 'COMPLETED'"
                  @click="approveTask(item, item.assignee?.id)"
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
                  @click="rejectTask(item, item.assignee?.id)"
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
              :total-items="pendingApprovalTasks.length"
              @update:page="handlePageChange"
            />
          </template>
        </VDataTable>
      </VWindowItem>
      <VWindowItem :value="1">
        <VDataTable
          :headers="headers"
          :items-per-page="options.itemsPerPage"
          :items="earnedTasks"
          item-value="name"
          hide-default-footer
          class="text-no-wrap"
          :loading="isLoading"
          @update:options="updateOptions"
        >
          <template #item.name="{ item }">
            <span class="text-sm text-truncate mb-0">
              {{ item.name }}
              <VChip
                :label="false"
                color="primary"
                size="x-small"
              >
                #{{ item.id }}
              </VChip>
            </span>
          </template>
          <template #item.status="{ item }">
            <span class="text-sm text-truncate mb-0">{{ item.status.name }}</span>
          </template>
          <template #item.amount="{ item }">
            <span class="text-sm text-truncate mb-0">${{ item.bucks_amount }}</span>
          </template>
          <template #item.approval="{ item }">
            <VChip
              :color="getApprovalStatusColor(item.approval_status)"
              text-color="white"
              class="me-2"
              size="small"
            >
              <span class="text-sm text-truncate mb-0 text-uppercase">{{ item.approval_status }}</span>
            </VChip>
          </template>
          <template 
            v-if="authStore.isAdmin || authStore.isManager"
            #item.assignee="{ item }"
          >
            <VChip>
              <VAvatar
                start
                size="25"
                class="text-white bg-primary"
              >
                <small>{{ avatarText(item.assignee?.name_first + ' ' + item.assignee?.name_last) }}</small>
              </VAvatar>
              <span>{{ item.assignee?.name_first }} {{ item.assignee?.name_last }}</span>
            </VChip>
          </template>
          <template
            v-if="authStore.isAdmin || authStore.isManager"
            #item.actions="{ item }"
          >
            <div class="d-flex gap-1">
              <span>
                <VBtn
                  color="success"
                  icon
                  size="x-small"
                  :disabled="approvalUpdating || item.approval_status == 'approved' || item.status.name !== 'COMPLETED'"
                  @click="approveTask(item, item.assignee?.id)"
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
                  @click="rejectTask(item, item.assignee?.id)"
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
              :total-items="earnedTasks.length"
              @update:page="handlePageChange"
            />
          </template>
        </VDataTable>
      </VWindowItem>
      <VWindowItem :value="2">
        <VDataTable
          :headers="headers"
          :items-per-page="options.itemsPerPage"
          :items="rejectedTasks"
          item-value="name"
          hide-default-footer
          class="text-no-wrap"
          :loading="isLoading"
          @update:options="updateOptions"
        >
          <template #item.name="{ item }">
            <span class="text-sm text-truncate mb-0">
              {{ item.name }}
              <VChip
                :label="false"
                color="primary"
                size="x-small"
              >
                #{{ item.id }}
              </VChip>
            </span>
          </template>
          <template #item.status="{ item }">
            <span class="text-sm text-truncate mb-0">{{ item.status.name }}</span>
          </template>
          <template #item.amount="{ item }">
            <span class="text-sm text-truncate mb-0">${{ item.bucks_amount }}</span>
          </template>
          <template #item.approval="{ item }">
            <VChip
              :color="getApprovalStatusColor(item.approval_status)"
              text-color="white"
              class="me-2"
              size="small"
            >
              <span class="text-sm text-truncate mb-0 text-uppercase">{{ item.approval_status }}</span>
            </VChip>
          </template>
          <template
            v-if="authStore.isAdmin || authStore.isManager"
            #item.assignee="{ item }"
          >
            <VChip>
              <VAvatar
                start
                size="25"
                class="text-white bg-primary"
              >
                <small>{{ avatarText(item.assignee?.name_first + ' ' + item.assignee?.name_last) }}</small>
              </VAvatar>
              <span>{{ item.assignee?.name_first }} {{ item.assignee?.name_last }}</span>
            </VChip>
          </template>
          <template
            v-if="authStore.isAdmin || authStore.isManager"
            #item.actions="{ item }"
          >
            <div class="d-flex gap-1">
              <span>
                <VBtn
                  color="success"
                  icon
                  size="x-small"
                  :disabled="approvalUpdating || item.approval_status == 'approved' || item.status.name !== 'COMPLETED'"
                  @click="approveTask(item, item.assignee?.id)"
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
                  @click="rejectTask(item, item.assignee?.id)"
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
              :total-items="rejectedTasks.length"
              @update:page="handlePageChange"
            />
          </template>
        </VDataTable>
      </VWindowItem>
    </VWindow>
    <!--
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
      <span class="text-sm text-truncate mb-0">{{ item.name }}</span>
      </template>
      <template #item.status="{ item }">
      <span class="text-sm text-truncate mb-0">{{ item.status.name }}</span>
      </template>
      <template #item.amount="{ item }">
      <span class="text-sm text-truncate mb-0">${{ item.bucks_amount }}</span>
      </template>
      <template #item.approval="{ item }">
      <VChip
      :color="getApprovalStatusColor(item.approval_status)"
      text-color="white"
      class="me-2"
      size="small"
      >
      <span class="text-sm text-truncate mb-0 text-uppercase">{{ item.approval_status }}</span>
      </VChip>
      </template>
      <template #item.assignee="{ item }">
      <VChip>
      <VAvatar
      start
      size="25"
      class="text-white bg-primary"
      >
      <small>{{ avatarText(item.assignee?.name_first + ' ' + item.assignee?.name_last) }}</small>
      </VAvatar>
      <span>{{ item.assignee?.name_first }} {{ item.assignee?.name_last }}</span>
      </VChip>
      </template>
      <template
      v-if="authStore.isAdmin || authStore.isManager"
      #item.actions="{ item }"
      >
      <div class="d-flex gap-1">
      <span>
      <VBtn
      color="success"
      icon
      size="x-small"
      :disabled="approvalUpdating || item.approval_status == 'approved' || item.status.name !== 'COMPLETED'"
      @click="approveTask(item, item.assignee?.id)"
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
      @click="rejectTask(item, item.assignee?.id)"
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
    -->
  </VCard>
</template>
  
<script setup>
import { ref } from 'vue'
import Loader from '@/components/Loader.vue'
import { useProjectBucksStore } from "@/store/project_bucks"
import { useAuthStore } from "@/store/auth"
import { useRoute } from 'vue-router'
import { useToast } from "vue-toastification"
import { debounce } from 'lodash'
  
onBeforeMount(async () => {
  await fetchBucksTasks()
})
  
const projectBucksStore = useProjectBucksStore()
const authStore = useAuthStore()
const route = useRoute()
const toast = useToast()
const projectUuid = route.params.id
const approvalUpdating = ref(false)
  
const isLoading = ref(false)
const search = ref('')
const options = ref({ page: 1, itemsPerPage: 10, orderBy: '', order: '' })

const headers = [
  { title: 'Task', key: 'name', sortable: false, width: '20%' },
  { title: 'Task Status', key: 'status', sortable: false, width: '20%' },
  { title: 'Bucks Amount', key: 'amount', sortable: false, width: '20%' },
  { title: 'Approval', key: 'approval', sortable: false, width: '20%' },
  ...(authStore.isAdmin || authStore.isManager ? [{ title: 'Assignee', key: 'assignee', sortable: false, width: '20%' }] : []),
  ...(authStore.isAdmin || authStore.isManager ? [{ title: 'Actions', key: 'actions', sortable: false }] : []),
]

const currentTab = ref('item-1')
const tabItemContent = 'Candy canes donut chupa chups candy canes lemon drops oat cake wafer. Cotton candy candy canes marzipan carrot cake. Sesame snaps lemon drops candy marzipan donut brownie tootsie roll. Icing croissant bonbon biscuit gummi bears. Pudding candy canes sugar plum cookie chocolate cake powder croissant.'

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

const approveTask = async (task, userId) => {
  try {
    approvalUpdating.value = true
    
    const payload = {
      approval_status: 'approved',
      user_id: userId,
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

const rejectTask = async (task, userId) => {
  try {
    approvalUpdating.value = true
    
    const payload = {
      approval_status: 'rejected',
      user_id: userId,
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

const getApprovalStatusColor = approvalStatus => {
  if (approvalStatus === 'approved') {
    return 'success'
  } else if (approvalStatus === 'rejected') {
    return 'error'
  } else {
    return 'secondary'
  }
}

const pendingApprovalTasks = computed(() => getBucksTasks.value.filter(task => task.status.name === 'COMPLETED' && task.approval_status === 'pending'))
const earnedTasks = computed(() => getBucksTasks.value.filter(task => task.status.name === 'COMPLETED' && task.approval_status === 'approved'))
const rejectedTasks = computed(() => getBucksTasks.value.filter(task => task.status.name === 'COMPLETED' && task.approval_status === 'rejected'))

// const completedTasks = computed(() => getBucksTasks.value.filter(task => task.status.name === 'COMPLETED'))
const inProgressTasks = computed(() => getBucksTasks.value.filter(task => task.status.name === 'IN PROGRESS'))
const pendingTasks = computed(() => getBucksTasks.value.filter(task => task.status.name === 'PENDING'))

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
  
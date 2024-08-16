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
          Bucks Share: <span class="font-weight-medium text-primary">
            {{ projectBucks?.project?.bucks_share_type === 'fixed' 
              ? '$' + projectBucks?.project?.bucks_share 
              : projectBucks?.project?.bucks_share + '%' 
            }} 
            <small v-if="projectBucks?.project?.bucks_share_type !== 'fixed'">
              (${{ projectBucks?.project?.bucks_share_amount }})
            </small>
          </span>
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
      <template #item.share="{ item }">
        <div class="d-flex gap-1">
          <template v-if="editingRole?.role_id === item.role_id">
            <div class="d-flex gap-1 align-center">
              <VTextField
                v-model="editingRole.bucks_share"
                prepend-inner-icon="tabler-currency-dollar"
                placeholder="0.00"
                type="number"
                class="no-arrows"
                autofocus
                density="compact"
                @keydown.enter="saveShare"
                @keydown.esc="editingRole = null"
                @input="limitTwoDecimals"
              />
              <div>
                <VBtn
                  color="success"
                  icon
                  size="x-small"
                  @click="saveShare"
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
                  <span class="text-xs">Save</span>
                </VTooltip>
              </div>
              <div>
                <VBtn
                  color="error"
                  icon
                  size="x-small"
                  @click="editingRole = null"
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
                  <span class="text-xs">Cancel</span>
                </VTooltip>
              </div>
            </div>
          </template>
          <template v-else>
            <span class="text-sm text-truncate mb-0">${{ item.bucks_share }}</span>
          </template>
        </div>
      </template>
      <template #item.actions="{ item }">
        <div class="d-flex justify-center">
          <VBtn
            color="primary"
            icon
            size="small"
            @click="editRole(item)"
          >
            <VIcon
              icon="tabler-edit"
              size="18"
            />
          </VBtn>
        </div>
      </template>
      <template #bottom>
        <div class="mb-4" />
      </template>
    </VDataTable>
    <div class="mt-2 mb-4 px-4">
      <VAlert
        color="primary"
        variant="tonal"
        class="text-center"
      >
        <b>Remaining Bucks: </b>${{ projectBucks?.remaining_bucks }}
      </VAlert>
    </div>
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
const editingRole = ref(null)
const editRoleShare = ref(null)

const headers = [
  {
    title: 'Role',
    key: 'role',
    sortable: false,
    width: '50%',
  },
  {
    title: 'Share',
    key: 'share',
    sortable: false,
    width: '50%',
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
  editingRole.value = item
  editRoleShare.value = item.bucks_share
}

const parseAndRound = value => Math.round(parseFloat(value) * 100) / 100

const saveShare = async () => {
  if (editingRole.value.bucks_share === '') {
    toast.error('Please enter a valid share amount')
    
    return
  }
  
  let remainingBucks = parseAndRound(projectBucks.value?.remaining_bucks) + parseAndRound(editRoleShare.value)
  let share = parseAndRound(editingRole.value.bucks_share)
  
  if (share > remainingBucks) {
    toast.error('Share amount exceeds remaining bucks')
    
    return
  }

  await projectBucksStore.updateProjectBucks(projectUuid, {
    roleId: editingRole.value.role_id,
    shares: editingRole.value.bucks_share,
  })
  
  if(getErrors.value) {
    toast.error('Something went wrong. Please try again later')
  } else {
    editingRole.value = null
    toast.success('Role share updated successfully')
  }
}

const limitTwoDecimals = event => {
  const value = event.target.value
  const parts = value.split('.')
  
  if(parts.length > 1 && parts[1].length > 2) {
    event.target.value = parts[0] + '.' + parts[1].slice(0, 2)
  }
}

const projectBucks = computed(() =>{
  return projectBucksStore.getBucksDetails
})

const loadStatus = computed(() => {
  return projectBucksStore.getLoadStatus
})

const getErrors = computed(() => {
  // show error message in toast
  return projectBucksStore.getErrors
})
</script>

<style scoped>
  .table-wrapper {
    inline-size: auto;
    overflow-x: auto;
  }
</style>

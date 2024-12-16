<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Received Activities</VCardTitle>
      <p class="text-sm mt-2 mb-0">
        View all receiving activities and alerts.
      </p>
    </VCardItem>

    <VCardText>
      <div
        v-if="activities ? activities.length == 0 : 0"
        class="text-center py-5"
      >
        <div v-html="noDataFound" />
        <p class="text-body-2 text-high-emphasis">
          No activities created yet.
        </p>
      </div>
      <div class="d-flex flex-wrap gap-4 mb-4">
        <VRow>
          <VCol cols="12">
            <VDataTable
              class="mt-4"
              density="compact"
              :items="activities"
              :headers="headers"
              :items-per-page="-1"
              :style="{ border: '1px solid #e0e0e0' }"
            >
              <template #[`item`]="{ item }">
                <tr
                  class="cursor-pointer"
                  :class="[{ 'bg-td-hover': hoveredItem === item.id }]"
                  @click="notifyClick(item)"
                  @mouseenter="hoveredItem = item.id"
                  @mouseleave="hoveredItem = null"
                >
                  <td>
                    <div class="d-flex align-center gap-x-2">
                      <VBadge
                        dot
                        location="top end"
                        offset-x="0"
                        offset-y="1"
                        :color="item.is_online ? 'success' : 'warning'"
                      >
                        <VAvatar
                          size="34"
                          color="primary"
                          :image="item.img ? getImageUrl(item.img.path) : undefined"
                          :variant="item.img ? undefined : 'tonal' "
                        >
                          <span v-if="!item.img">{{ avatarText(item.name) }}</span>
                        </VAvatar>
                      </VBadge>
                      <div>
                        <h6
                          class="font-weight-semibold text-sm"
                          style="position: relative; top: 6px;"
                        >
                          <span class="d-block">{{ item.name }}</span>
                        </h6>
                        <small class="text-xs">{{ item.role }}</small>
                      </div>
                    </div>
                  </td>
                  <td class="text-sm text-primary">
                    {{ item.title }}
                  </td>
                  <td class="text-sm">
                    {{ item.subtitle }}
                  </td>
                  <td class="text-sm">
                    {{ item.time }}
                  </td>
                </tr>
              </template>
              <template #bottom />
            </VDataTable>
          </VCol>
        </VRow>
      </div>
    </VCardText>
    <VDivider />
  </VCard>
</template>

<script setup>
import { computed, ref, onBeforeMount } from 'vue'
import { useProjectStore } from "@/store/projects"
import noDataFound from '@images/darby/noData.svg?raw'
import moment from 'moment'
import { useRoute } from 'vue-router'

const $route = useRoute()
const projectStore = useProjectStore()

const projectUuid = $route.params.id
const hoveredItem = ref(null)

const formatDate = date => moment(date).format('MM/DD/YYYY')

onBeforeMount(async () => {
  await fetchActivities()
})

const fetchActivities = async () => {
  try {
    await projectStore.getActivities(projectUuid)
  } catch (error) {
    toast.error('Error fetching activities:', error)
  }
}

const activities = computed(() => {
  return projectStore.getProjectActivities
})

const headers = [
  { title: 'Created By', sortable: false, key: 'name', width: '20%' },
  { title: 'Title', sortable: false, key: 'title', width: '25%' },
  { title: 'Message', sortable: false, key: 'subtitle', width: '25%' },
  { title: 'Created At', sortable: false, key: 'time', width: '15%' },
]

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL
  
  return `${baseUrl}storage/${path}`
}

const avatarText = name => {
  return name ? name.split(' ').map(word => word[0]).join('') : ''
}

const notifyClick = item => {
  console.log('Row clicked')
}
</script>

<style scoped>
.v-data-table {
  border-radius: 8px;
  overflow: hidden;
}

.v-data-table th {
  font-weight: bold;
  color: #4a4a4a;
}
</style>

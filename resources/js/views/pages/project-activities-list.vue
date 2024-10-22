<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Received Activities</VCardTitle>
      <p class="text-sm mt-2 mb-0">
        View all receiving activities and alerts.
      </p>
    </VCardItem>

    <VCardText>
      <div class="d-flex flex-wrap gap-4 mb-4">
        <VRow>
          <VCol cols="12">
            <VTable
              class="mt-4"
              density="compact"
              :style="{ border: '1px solid #e0e0e0' }"
            >
              <thead>
                <tr>
                  <th
                    scope="col"
                    style="width: 20%;"
                  >
                    Created By
                  </th>
                  <th
                    scope="col"
                    style="width: 25%;"
                  >
                    Title
                  </th>
                  <th
                    scope="col"
                    style="width: 25%;"
                  >
                    Message
                  </th>
                  <th
                    scope="col"
                    style="width: 15%;"
                  >
                    Created At
                  </th>
                </tr>
              </thead>
              <tbody
                v-for="activity in activities"
                :key="activity.id"
              >
                <tr>
                  <td>
                    <div class="d-flex align-center gap-x-2">
                      <VBadge
                        dot
                        location="top end"
                        offset-x="0"
                        offset-y="1"
                        :color="activity.is_online ? 'success' : 'warning'"
                      >
                        <VAvatar
                          size="34"
                          color="primary"
                          :image="activity.img ? getImageUrl(activity.img.path) : undefined"
                          :variant="activity.img ? undefined : 'tonal' "
                        >
                          <span v-if="!activity.img">{{ avatarText(activity.name) }}</span>
                        </VAvatar>
                      </VBadge>
                      <div>
                        <h6
                          class="font-weight-semibold text-sm"
                          style="position: relative; top: 6px;"
                        >
                          <span class="d-block">{{ activity.name }}</span>
                        </h6>
                        <small class="text-xs">{{ activity.role }}</small>
                      </div>
                    </div>
                  </td>
                  <td class="text-sm">
                    {{ activity.title }}
                  </td>
                  <td class="text-sm">
                    {{ activity.subtitle }}
                  </td>
                  <td class="text-sm">
                    {{ activity.time }}
                  </td>
                </tr>
              </tbody>
            </VTable>
          </VCol>
        </VRow>
      </div>
    </VCardText>
    <VDivider />
  </VCard>
</template>

<script setup>
import { computed, onBeforeMount } from 'vue'
import { useProjectStore } from "@/store/projects"
import moment from 'moment'
import { useRoute } from 'vue-router'

const $route = useRoute()
const projectStore = useProjectStore()

const projectUuid = $route.params.id

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

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}
</script>

<style scoped>
.v-table {
  border-radius: 8px;
  overflow: hidden;
}
</style>

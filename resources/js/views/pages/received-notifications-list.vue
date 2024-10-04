<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Received Notifications & Alerts</VCardTitle>
      <p class="text-sm mt-2 mb-0">
        View all receiving notifications and alerts.
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
                    Notify By
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
                    Received At
                  </th>
                  <th
                    scope="col"
                    style="width: 15%;"
                  >
                    Read At
                  </th>
                </tr>
              </thead>
              <tbody
                v-for="notification in notifications"
                :key="notification.id"
              >
                <tr
                  class="cursor-pointer"
                  @click="notifyClick(notification)"
                >
                  <td>
                    <div class="d-flex align-center gap-x-2">
                      <VBadge
                        dot
                        location="top end"
                        offset-x="0"
                        offset-y="1"
                        :color="notification.is_online ? 'success' : 'warning'"
                      >
                        <VAvatar
                          size="34"
                          color="primary"
                          :image="notification.img ? getImageUrl(notification.img.path) : undefined"
                          :variant="notification.img ? undefined : 'tonal' "
                        >
                          <span v-if="!notification.img">{{ avatarText(notification.name) }}</span>
                        </VAvatar>
                      </VBadge>
                      <div>
                        <h6
                          class="font-weight-semibold text-sm"
                          style="position: relative; top: 6px;"
                        >
                          <span class="d-block">{{ notification.name }}</span>
                        </h6>
                        <small class="text-xs">{{ notification.role }}</small>
                      </div>
                    </div>
                  </td>
                  <td class="text-sm text-primary">
                    {{ notification.title }}
                  </td>
                  <td class="text-sm">
                    {{ notification.subtitle }}
                  </td>
                  <td class="text-sm">
                    {{ notification.time }}
                  </td>
                  <td class="text-sm">
                    {{ notification.read_at ? formatDate(notification.read_at) : '---' }}
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
import { computed } from 'vue'
import { useNotificationStore } from "@/store/notifications"
import moment from 'moment'
import { useRouter } from 'vue-router'

const router = useRouter()
const notificationStore = useNotificationStore()

const formatDate = date => moment(date).format('MM/DD/YYYY')

const notifyClick = async notification => {
  if (notification.url) {
    const url = notification.url.startsWith('/') ? notification.url : `/${notification.url}`

    router.push(url)
  }
}

const notifications = computed(() => {
  return notificationStore.getNotifications
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

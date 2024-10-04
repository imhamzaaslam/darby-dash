<template>
  <IconBtn id="notification-btn">
    <VBadge
      v-bind="props.badgeProps"
      :model-value="props.notifications.some(n => !n.read_at)"
      color="primary"
      dot
      offset-x="2"
      offset-y="3"
      class="animated-badge"
    >
      <VIcon
        size="24"
        icon="tabler-bell"
      />
    </VBadge>

    <VMenu
      activator="parent"
      width="380px"
      :location="props.location"
      offset="12px"
      :close-on-content-click="false"
    >
      <VCard class="d-flex flex-column">
        <!-- 👉 Header -->
        <VCardItem class="notification-section">
          <VCardTitle class="text-h6">
            Notifications
          </VCardTitle>

          <template #append>
            <VChip
              v-show="props.notifications.some(n => !n.read_at)"
              size="small"
              color="primary"
              class="me-2"
            >
              {{ totalUnseenNotifications }} New
            </VChip>
            <IconBtn
              v-show="props.notifications.length"
              size="34"
              @click="markAllReadOrUnread"
            >
              <VIcon
                size="20"
                color="high-emphasis"
                :icon="!isAllMarkRead ? 'tabler-mail' : 'tabler-mail-opened' "
              />

              <VTooltip
                activator="parent"
                location="start"
              >
                {{ !isAllMarkRead ? 'Mark all as unread' : 'Mark all as read' }}
              </VTooltip>
            </IconBtn>
          </template>
        </VCardItem>

        <VDivider />

        <!-- 👉 Notifications list -->
        <PerfectScrollbar
          :options="{ wheelPropagation: false }"
          style="max-block-size: 23.75rem;"
        >
          <VList class="notification-list rounded-0 py-0">
            <template
              v-for="(notification, index) in props.notifications"
              :key="notification.title"
            >
              <VDivider v-if="index > 0" />
              <VListItem
                link
                lines="one"
                min-height="66px"
                class="list-item-hover-class"
                @click="notifyClick(notification)"
              >
                <!-- Slot: Prepend -->
                <!-- Handles Avatar: Image, Icon, Text -->
                <div class="d-flex align-start gap-3">
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
                    <p class="text-sm font-weight-medium mb-1">
                      {{ notification.title }}
                    </p>
                    <p
                      class="text-body-2 mb-2"
                      style=" letter-spacing: 0.4px !important; line-height: 18px;"
                    >
                      {{ notification.subtitle }}
                    </p>
                    <p
                      class="text-sm text-disabled mb-0"
                      style=" letter-spacing: 0.4px !important; line-height: 18px;"
                    >
                      {{ notification.time }}
                    </p>
                  </div>
                  <VSpacer />

                  <div class="d-flex flex-column align-end">
                    <VIcon
                      size="10"
                      icon="tabler-circle-filled"
                      :color="!notification.read_at ? 'primary' : '#a8aaae'"
                      :class="`${notification.read_at ? 'visible-in-hover' : ''}`"
                      class="mb-2"
                      @click.stop="toggleReadUnread(notification.read_at, notification.id)"
                    />

                    <VIcon
                      size="20"
                      icon="tabler-x"
                      class="visible-in-hover"
                      @click="$emit('remove', notification.id)"
                    />
                  </div>
                </div>
              </VListItem>
            </template>

            <VListItem
              v-show="!props.notifications.length"
              class="text-center text-medium-emphasis"
              style="block-size: 56px;"
            >
              <VListItemTitle>No Notification Found!</VListItemTitle>
            </VListItem>
          </VList>
        </PerfectScrollbar>

        <VDivider />

        <!-- 👉 Footer -->
        <VCardText
          v-show="props.notifications.length"
          class="pa-4"
        >
          <VBtn
            block
            size="small"
            :to="{ path: '/view/received/notifications' }"
          >
            View All Notifications
          </VBtn>
        </VCardText>
      </VCard>
    </VMenu>
  </IconBtn>
</template>

<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { useRouter } from 'vue-router'

const props = defineProps({
  notifications: {
    type: Array,
    required: true,
  },
  badgeProps: {
    type: Object,
    required: false,
    default: undefined,
  },
  location: {
    type: null,
    required: false,
    default: 'bottom end',
  },
})

const emit = defineEmits([
  'read',
  'unread',
  'remove',
  'click:notification',
])

const router = useRouter()

const isAllMarkRead = computed(() => props.notifications.some(item => item.read_at === null))

const notifyClick = async notification => {
  emit('click:notification', notification)
  if (notification.url) {
    router.push(notification.url)
  }
}

const markAllReadOrUnread = () => {
  const allNotificationsIds = props.notifications.map(item => item.id)
  if (!isAllMarkRead.value)
    emit('unread', allNotificationsIds)
  else
    emit('read', allNotificationsIds)
}

const totalUnseenNotifications = computed(() => {
  return props.notifications.filter(item => item.read_at === null).length
})

const toggleReadUnread = (isSeen, Id) => {
  if (isSeen)
    emit('unread', [Id])
  else
    emit('read', [Id])
}

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

/* const getUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}${path}`
} */
</script>

<style lang="scss">
.notification-section {
  padding-block: 0.75rem;
  padding-inline: 1rem;
}

.list-item-hover-class {
  .visible-in-hover {
    display: none;
  }

  &:hover {
    .visible-in-hover {
      display: block;
    }
  }
}

.notification-list.v-list {
  .v-list-item {
    border-radius: 0 !important;
    margin: 0 !important;
    padding-block: 0.75rem !important;
  }
}

// Badge Style Override for Notification Badge
.notification-badge {
  .v-badge__badge {
    /* stylelint-disable-next-line liberty/use-logical-spec */
    min-width: 18px;
    padding: 0;
    block-size: 18px;
  }
}
</style>

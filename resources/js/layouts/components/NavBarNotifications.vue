<script setup>
import { computed, onMounted, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useNotificationStore } from "@/store/notifications"

onMounted(async () => {
  await fetchNotifications()
})

const toast = useToast()
const notificationStore = useNotificationStore()

const fetchNotifications = async () => {
  try {
    await notificationStore.getAll()
  } catch (error) {
    toast.error('Error fetching notifications:', error)
  }
}

const removeNotification = notificationId => {
  notifications.value.forEach((item, index) => {
    if (notificationId === item.id)
      notifications.value.splice(index, 1)
    notificationStore.delete(notificationId)
  })
}

const markRead = notificationId => {
  notifications.value.forEach(item => {
    notificationId.forEach(id => {
      if (id === item.id)
        notificationStore.markAsRead({ ids: notificationId })
    })
  })
}

const markUnRead = notificationId => {
  notifications.value.forEach(item => {
    notificationId.forEach(id => {
      if (id === item.id)
        notificationStore.markAsUnread({ ids: notificationId })
    })
  })
}

const handleNotificationClick = notification => {
  if (!notification.read_at)
    markRead([notification.id])
}

const notifications = computed(() => {
  return notificationStore.getNotifications
})
</script>

<template>
  <Notifications
    :notifications="notifications"
    @remove="removeNotification"
    @read="markRead"
    @unread="markUnRead"
    @click:notification="handleNotificationClick"
  />
</template>

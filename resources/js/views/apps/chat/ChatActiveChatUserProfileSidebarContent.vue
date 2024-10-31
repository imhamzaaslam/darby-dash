<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { useChat } from './useChat'
import { useChatStore } from '@/store/chats'

const emit = defineEmits(['close'])

const store = useChatStore()
const { resolveAvatarBadgeVariant } = useChat()

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}
</script>

<template>
  <template v-if="store.activeChat">
    <!-- Close Button -->
    <div
      class="pt-6 px-6"
      :class="$vuetify.locale.isRtl ? 'text-left' : 'text-right'"
    >
      <IconBtn @click="$emit('close')">
        <VIcon
          icon="tabler-x"
          class="text-medium-emphasis"
        />
      </IconBtn>
    </div>

    <!-- User Avatar + Name + Role -->
    <div class="text-center px-6">
      <VBadge
        location="bottom right"
        offset-x="7"
        offset-y="4"
        bordered
        :color="resolveAvatarBadgeVariant(store.getActiveChat?.contact?.is_online)"
        class="chat-user-profile-badge mb-5"
      >
        <VAvatar
          size="84"
          :variant="!store.getActiveChat?.contact?.info?.avatar ? 'tonal' : undefined"
          :color="!store.getActiveChat?.contact?.info?.avatar ? 'primary' : undefined"
        >
          <VImg
            v-if="store.getActiveChat?.contact?.info?.avatar"
            :src="getImageUrl(store.getActiveChat?.contact?.info?.avatar?.path)"
          />
          <span
            v-else
            class="text-3xl"
          >{{ store.getActiveChat?.contact?.name_first.charAt(0) + store.getActiveChat?.contact?.name_last.charAt(0) }}</span>
        </VAvatar>
      </VBadge>
      <h5 class="text-h5">
        {{ store.getActiveChat?.contact?.name_first + " " + store.getActiveChat?.contact?.name_last }}
      </h5>
      <p class="text-capitalize text-body-1 mb-0">
        {{ store.getActiveChat?.contact?.role }}
      </p>
    </div>

    <!-- User Data -->
    <PerfectScrollbar
      class="ps-chat-user-profile-sidebar-content text-medium-emphasis pb-6 px-6"
      :options="{ wheelPropagation: false }"
    >
      <!-- Personal Information -->
      <div class="mb-6 mt-6">
        <div class="text-sm text-disabled mb-1">
          PERSONAL INFORMATION
        </div>
        <div class="d-flex align-center text-high-emphasis pa-2">
          <VIcon
            class="me-2"
            color="primary"
            icon="tabler-mail"
            size="18"
          />
          <div class="text-sm">
            {{ store.getActiveChat?.contact?.email }}
          </div>
        </div>
        <div class="d-flex align-center text-high-emphasis pa-2">
          <VIcon
            class="me-2"
            color="primary"
            icon="tabler-phone"
            size="18"
          />
          <div class="text-sm">
            {{ store.getActiveChat?.contact?.info?.phone }}
          </div>
        </div>
        <div class="d-flex align-center text-high-emphasis pa-2">
          <VIcon
            class="me-2"
            icon="tabler-clock"
            color="primary"
            size="18"
          />
          <div class="text-sm">
            Mon - Fri 10AM - 8PM
          </div>
        </div>
      </div>

      <!-- Options -->
      <!--
        <div>
        <VBtn
        block
        color="error"
        append-icon="tabler-trash"
        class="mt-6"
        >
        Delete Contact
        </VBtn>
        </div> 
      -->
    </PerfectScrollbar>
  </template>
</template>

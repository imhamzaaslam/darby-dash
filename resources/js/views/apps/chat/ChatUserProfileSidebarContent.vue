<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { useChat } from './useChat'
import { useUserStore } from "@/store/users"

const emit = defineEmits(['close'])


// composables
const store = useUserStore()
const { resolveAvatarBadgeVariant } = useChat()

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}
</script>

<template>
  <template v-if="store.getUser">
    <!-- Close Button -->
    <div class="pt-2 me-2 text-end">
      <IconBtn @click="$emit('close')">
        <VIcon
          class="text-medium-emphasis"
          color="disabled"
          icon="tabler-x"
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
        :color="resolveAvatarBadgeVariant(store.getUser?.is_online)"
        class="chat-user-profile-badge mb-5"
      >
        <VAvatar
          size="84"
          :variant="!store.getUser?.info?.avatar ? 'tonal' : undefined"
          :color="!store.getUser?.info?.avatar ? 'primary' : undefined"
        >
          <VImg
            v-if="store.getUser?.info?.avatar"
            :src="getImageUrl(store.getUser?.info?.avatar?.path)"
          />
          <span
            v-else
            class="text-3xl"
          >{{ store.getUser?.name_first.charAt(0) + store.getUser?.name_last.charAt(0) }}</span>
        </VAvatar>
      </VBadge>
      <h5 class="text-h5">
        {{ store.getUser?.name_first + " " + store.getUser?.name_last }}
      </h5>
      <p class="text-capitalize text-body-1 mb-0">
        {{ store.getUser?.role }}
      </p>
    </div>

    <!-- User Data -->
    <PerfectScrollbar
      class="ps-chat-user-profile-sidebar-content pb-5 px-6"
      :options="{ wheelPropagation: false }"
    >
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
            {{ store.getUser?.email }}
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
            {{ store.getUser?.info?.phone }}
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
      <!-- About -->
      <div class="my-6 text-medium-emphasis">
        <!-- Logout Button -->
        <!--
          <VBtn
          color="primary"
          class="mt-12"
          block
          append-icon="tabler-logout"
          >
          Logout
          </VBtn> 
        -->
      </div>
    </PerfectScrollbar>
  </template>
</template>

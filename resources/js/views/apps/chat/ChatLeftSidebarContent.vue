<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { useChat } from './useChat'
import ChatContact from '@/views/apps/chat/ChatContact.vue'
import { useChatStore } from '@/store/chats'
import { useUserStore } from "@/store/users"

const props = defineProps({
  search: {
    type: String,
    required: true,
  },
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits([
  'openChatOfContact',
  'showUserProfile',
  'close',
  'update:search',
])

const { resolveAvatarBadgeVariant } = useChat()
const search = useVModel(props, 'search', emit)
const store = useChatStore()
const userStore = useUserStore()

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}


const userDetails = computed(() => {
  return userStore.getUser
})
</script>

<template>
  <!-- 👉 Chat list header -->
  <div
    v-if="userDetails"
    class="chat-list-header"
  >
    <VBadge
      dot
      location="bottom right"
      offset-x="3"
      offset-y="3"
      color="success"
      bordered
    >
      <VAvatar
        size="40"
        class="cursor-pointer"
        :color="!userDetails?.info?.avatar?.path ? 'primary': undefined"
        @click="$emit('showUserProfile')"
      >
        <template v-if="userDetails?.info?.avatar?.path">
          <VImg :src="getImageUrl(userDetails?.info?.avatar.path)" />
        </template>
        <template v-else>
          <span>
            {{ userDetails?.name_first.charAt(0) + userDetails?.name_last.charAt(0) }}
          </span>
        </template>
      </VAvatar>
    </VBadge>

    <AppTextField
      v-model="search"
      placeholder="Search..."
      prepend-inner-icon="tabler-search"
      class="ms-4 me-1 chat-list-search"
    />

    <IconBtn @click.prevent>
      <VIcon icon="tabler-dots-vertical" />
      <VMenu 
        activator="parent" 
        class="p-0"
      >
        <VList class="p-0">
          <VListItem
            value="add-member"
            class="p-0"
          >
            Add Member
          </VListItem>
          <!--
            <VListItem
            value="add-group"
            class="p-0"
            >
            Add Group
            </VListItem> 
          -->
        </VList>
      </VMenu>
    </IconBtn>

    <IconBtn
      v-if="$vuetify.display.smAndDown"
      @click="$emit('close')"
    >
      <VIcon
        icon="tabler-x"
        class="text-medium-emphasis"
      />
    </IconBtn>
  </div>
  <VDivider />

  <PerfectScrollbar
    tag="ul"
    class="d-flex flex-column gap-y-1 chat-contacts-list px-3 py-2 list-none"
    :options="{ wheelPropagation: false }"
  >
    <li class="list-none">
      <h5 class="chat-contact-header text-primary text-h5">
        Chats
      </h5>
    </li>

    <ChatContact
      v-for="contact in store.getChatsContacts"
      :key="`chat-${contact.id}`"
      :user="contact"
      is-chat-contact
      @click="$emit('openChatOfContact', contact.uuid)"
    />

    <span
      v-show="!store.getChatsContacts?.length"
      class="no-chat-items-text text-disabled"
    >No chats found</span>
    <li class="list-none pt-2">
      <h5 class="chat-contact-header text-primary text-h5">
        Contacts
      </h5>
    </li>

    <ChatContact
      v-for="contact in store.getContacts"
      :key="`chat-${contact.id}`"
      :user="contact"
      @click="$emit('openChatOfContact', contact.uuid)"
    />

    <span
      v-show="!store.getContacts?.length"
      class="no-chat-items-text text-disabled"
    >No contacts found</span>
  </PerfectScrollbar>
</template>

<style lang="scss">
.chat-contacts-list {
  --chat-content-spacing-x: 16px;

  padding-block-end: 0.75rem;

  .chat-contact-header {
    margin-block: 0.5rem 0.25rem;
  }

  .chat-contact-header,
  .no-chat-items-text {
    margin-inline: var(--chat-content-spacing-x);
  }
}

.chat-list-search {
  .v-field--focused {
    box-shadow: none !important;
  }
}
</style>

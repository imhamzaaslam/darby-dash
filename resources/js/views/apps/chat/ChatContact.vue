<script setup>
import { useChat } from '@/views/apps/chat/useChat'
import { useChatStore } from '@/store/chats'

const props = defineProps({
  isChatContact: {
    type: Boolean,
    required: false,
    default: false,
  },
  user: {
    type: null,
    required: true,
  },
})

const store = useChatStore()
const { resolveAvatarBadgeVariant } = useChat()

const isChatContactActive = computed(() => {
  const isActive = store.getActiveChat?.user_id === props.user.id
  if (!props.isChatContact)
    return !store.getActiveChat?.messages && isActive
  
  return isActive
})

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}
</script>

<template>
  <li
    :key="store.chatsContacts?.length"
    class="chat-contact cursor-pointer d-flex align-center"
    :class="{ 'chat-contact-active': isChatContactActive }"
  >
    <VBadge
      dot
      location="bottom right"
      offset-x="3"
      offset-y="0"
      :color="resolveAvatarBadgeVariant(props.user.is_online)"
      bordered
      :model-value="props.isChatContact"
    >
      <VAvatar
        size="40"
        :variant="!props.user.info?.avatar ? 'tonal' : undefined"
        :color="!props.user.info?.avatar ? isChatContactActive ? 'background' : 'primary' : undefined"
      >
        <VImg
          v-if="props.user?.info?.avatar?.path"
          :src="getImageUrl(props.user?.info?.avatar.path)"
          alt="John Doe"
        />
        <span v-else>{{ props.user?.name_first.charAt(0) + props.user?.name_last.charAt(0) }}</span>
      </VAvatar>
    </VBadge>
    <div class="flex-grow-1 ms-4 overflow-hidden">
      <p class="text-base text-high-emphasis mb-0">
        {{ props.user?.name_first + " " +props.user?.name_last }}
      </p>
      <p class="mb-0 text-truncate text-body-2">
        {{ props.isChatContact && 'chat' in props.user ? props.user.chat.lastMessage.message : props.user.about }}
      </p>
    </div>
    <div
      v-if="props.isChatContact && 'chat' in props.user"
      class="d-flex flex-column align-self-start"
    >
      <div class="text-body-2 text-disabled whitespace-no-wrap">
        {{ formatDateToMonthShort(props.user.chat.lastMessage.time) }}
      </div>
      <VBadge
        v-if="props.user.chat.unseenMsgs"
        color="error"
        inline
        :content="props.user.chat.unseenMsgs"
        class="ms-auto"
      />
    </div>
  </li>
</template>

<style lang="scss">
@use "@core-scss/template/mixins" as templateMixins;
@use "@styles/variables/vuetify.scss";
@use "@core-scss/base/mixins";
@use "vuetify/lib/styles/tools/states" as vuetifyStates;

.chat-contact {
  border-radius: vuetify.$border-radius-root;
  padding-block: 8px;
  padding-inline: 12px;

  @include mixins.before-pseudo;
  @include vuetifyStates.states($active: false);

  &.chat-contact-active {
    @include templateMixins.custom-elevation(var(--v-theme-primary), "sm");

    background: rgb(var(--v-theme-primary));
    color: #fff;

    --v-theme-on-background: #fff;
  }

  .v-badge--bordered .v-badge__badge::after {
    color: #fff;
  }
}
</style>

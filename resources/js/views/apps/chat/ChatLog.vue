<script setup>
import { useChatStore } from '@/store/chats'
import { useUserStore } from "@/store/users"

const store = useChatStore()
const userStore = useUserStore()

const contact = computed(() => ({
  id: store.getActiveChat?.contact?.id,
  avatar: store.getActiveChat?.contact?.info?.avatar?.path,
  fName: store.getActiveChat?.contact?.name_first,
  lName: store.getActiveChat?.contact?.name_last,
}))

const resolveFeedbackIcon = feedback => {
  if (feedback.isSeen)
    return {
      icon: 'tabler-checks',
      color: 'info',
    }
  else if (feedback.isDelivered)
    return {
      icon: 'tabler-checks',
      color: undefined,
    }
  else
    return {
      icon: 'tabler-check',
      color: undefined,
    }
}

const msgGroups = computed(() => {
  let messages = []
  const _msgGroups = []
  if (store.getActiveChat?.messages && store.getActiveChat.messages.length > 0) {
    messages = store.getActiveChat?.messages
    let msgSenderId = messages[0].senderId
    let msgGroup = {
      senderId: msgSenderId,
      messages: [],
    }
    messages.forEach((msg, index) => {
      if (msgSenderId === msg.senderId) {
        msgGroup.messages.push({
          id: msg.id,
          message: msg.message,
          time: msg.created_at,
          feedback: msg.feedback,
        })
      } else {
        msgSenderId = msg.senderId
        _msgGroups.push(msgGroup)
        msgGroup = {
          senderId: msg.senderId,
          messages: [{
            id: msg.id,
            message: msg.message,
            time: msg.created_at,
            feedback: msg.feedback,
          }],
        }
      }
      if (index === messages.length - 1)
        _msgGroups.push(msgGroup)
    })
  }

  return _msgGroups
})

const editDialogVisible = ref(false)
const currentMessage = ref(null)
const editedMessage = ref('')

const editMessage = msgData => {
  currentMessage.value = msgData.id
  editedMessage.value = msgData.message
  editDialogVisible.value = true
}

const saveEditedMessage = async () => {
  if (editedMessage.value.trim()) {
    try {
      const payload = {
        chatUuid: store.getActiveChat?.uuid,
        chatId: currentMessage.value,
        message: editedMessage.value,
      }

      console.log('Payload', payload)

      await store.updateMsg(payload)
      editDialogVisible.value = false
    } catch (error) {
      console.error('Error editing message:', error)
    }
  }
}

const deleteMessage = async msgData => {
  try {
    const payload = {
      chatUuid: store.getActiveChat?.uuid,
      messageId: msgData.id,
    }

    store.deleteMsg(payload)
  } catch (error) {
    console.error('Error deleting message:', error)
  }
}

const userDetails = computed(() => {
  return userStore.getUser
})

const loadStatus = computed(() => {
  return store.getLoadStatus
})

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}
</script>

<template>
  <div class="chat-log pa-6">
    <div
      v-for="(msgGrp, index) in msgGroups"
      :key="msgGrp.senderId + String(index)"
      class="chat-group d-flex align-start"
      :class="[{
        'flex-row-reverse': msgGrp.senderId !== contact.id,
        'mb-6': msgGroups.length - 1 !== index,
      }]"
    >
      <div
        class="chat-avatar"
        :class="msgGrp.senderId !== contact.id ? 'ms-4' : 'me-4'"
      >
        <VAvatar
          :variant="!contact.avatar || !userDetails?.info?.avatar ? 'tonal' : undefined"
          :color="!contact.avatar || !userDetails?.info?.avatar ? 'primary' : undefined"
          size="32"
        >
          <VImg
            v-if="msgGrp.senderId === contact.id ? contact.avatar : userDetails?.info?.avatar"
            :src="msgGrp.senderId === contact.id ? getImageUrl(contact.avatar) : getImageUrl(userDetails?.info?.avatar?.path)"
          />
          <span v-else>
            {{ msgGrp.senderId === contact.id ? contact?.fName.charAt(0) + contact?.lName.charAt(0) : userDetails?.name_first.charAt(0) + userDetails?.name_last.charAt(0) }}
          </span>
        </VAvatar>
      </div>
      <div
        class="chat-body d-inline-flex flex-column"
        :class="msgGrp.senderId !== contact.id ? 'align-end' : 'align-start'"
      >
        <div
          v-for="(msgData, msgIndex) in msgGrp.messages"
          :key="msgData.time"
          class="chat-content py-2 px-4 elevation-2 d-flex align-items-center justify-content-between"
          style="background-color: rgb(var(--v-theme-surface));"
          :class="[
            msgGrp.senderId === contact.id ? 'chat-left' : 'bg-primary text-white chat-right',
            msgGrp.messages.length - 1 !== msgIndex ? 'mb-2' : 'mb-1'
          ]"
        >
          <!-- Inline Message and Icon Button -->
          <p class="mb-0 text-base flex-grow-1 me-2">
            {{ msgData.message }}
          </p>
  
          <!-- Icon Button for Menu -->
          <VMenu
            v-if="msgGrp.senderId == userDetails?.id "
            offset-y
          >
            <template #activator="{ props }">
              <VIcon
                v-bind="props"
                icon="tabler-dots-vertical"
                size="15"
                color="grey"
                class="cursor-pointer"
                style="position: relative; left: 12px;"
              />
            </template>
            <VList class="text-sm">
              <VListItem
                value="edit"
                @click="editMessage(msgData)"
              >
                Edit
              </VListItem>
              <VListItem
                value="delete"
                @click="deleteMessage(msgData)"
              >
                Delete
              </VListItem>
            </VList>
          </VMenu>
        </div>

        <div :class="{ 'text-right': msgGrp.senderId !== contact.id }">
          <VIcon
            v-if="msgGrp.senderId !== contact.id"
            size="16"
            :color="resolveFeedbackIcon(msgGrp.messages[msgGrp.messages.length - 1].feedback).color"
          >
            {{ resolveFeedbackIcon(msgGrp.messages[msgGrp.messages.length - 1].feedback).icon }}
          </VIcon>
          <span class="text-sm ms-2 text-disabled">{{ formatDate(msgGrp.messages[msgGrp.messages.length - 1].time, { hour: 'numeric', minute: 'numeric' }) }}</span>
        </div>
      </div>
    </div>
    <VDialog
      v-model="editDialogVisible"
      :width="$vuetify.display.smAndDown ? 'auto' : 600"
    >
      <!-- Dialog close btn -->
      <DialogCloseBtn @click="editDialogVisible=false" />

      <VCard
        title="Edit Message"
        class="pricing-dialog"
      >
        <div class="pa-5">
          <AppTextarea
            v-model="editedMessage"
            label="Message"
            rows="3"
            autofocus
            multiline
          />
          <div class="mt-4 d-flex justify-end">
            <VBtn
              color="primary"
              :disabled="loadStatus === 1"
              @click="saveEditedMessage"
            >
              <VProgressCircular
                v-if="loadStatus === 1"
                indeterminate
                size="16"
                color="white"
              />
              <span v-if="loadStatus === 1">Processing...</span>
              <span v-else>Save</span>
            </VBtn>
            <VBtn
              color="secondary"
              class="ms-2"
              @click="editDialogVisible = false"
            >
              Cancel
            </VBtn>
          </div>
        </div>
      </VCard>
    </VDialog>
  </div>
</template>

<style lang=scss>
.chat-log {
  .chat-body {
    max-inline-size: calc(100% - 6.75rem);

    .chat-content {
      border-end-end-radius: 6px;
      border-end-start-radius: 6px;

      p {
        overflow-wrap: anywhere;
      }

      &.chat-left {
        border-start-end-radius: 6px;
      }

      &.chat-right {
        border-start-start-radius: 6px;
      }
    }
  }
}
</style>

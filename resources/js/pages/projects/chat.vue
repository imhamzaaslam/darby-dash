<template>
  <Loader v-if="loadStatus === 1" />
  <VRow>
    <VCol
      cols="12"
      md="7"
      class="d-flex"
    >
      <div class="d-flex justify-center align-center mb-5">
        <VAvatar
          :size="30"
          class="me-1"
          :image="sketch"
        />
        <h3 class="text-primary">
          {{ project?.title }}
        </h3>
      </div>
    </VCol>
  </VRow>
  <VLayout class="chat-app-layout">
    <!-- 👉 user profile sidebar -->
    <VNavigationDrawer
      v-model="isUserProfileSidebarOpen"
      temporary
      touchless
      absolute
      class="user-profile-sidebar"
      location="start"
      width="370"
    >
      <ChatUserProfileSidebarContent @close="isUserProfileSidebarOpen = false" />
    </VNavigationDrawer>

    <!-- 👉 Active Chat sidebar -->
    <VNavigationDrawer
      v-model="isActiveChatUserProfileSidebarOpen"
      width="374"
      absolute
      temporary
      location="end"
      touchless
      class="active-chat-user-profile-sidebar"
    >
      <ChatActiveChatUserProfileSidebarContent @close="isActiveChatUserProfileSidebarOpen = false" />
    </VNavigationDrawer>

    <!-- 👉 Left sidebar   -->
    <VNavigationDrawer
      v-model="isLeftSidebarOpen"
      absolute
      touchless
      location="start"
      width="370"
      :temporary="$vuetify.display.smAndDown"
      class="chat-list-sidebar"
      :permanent="$vuetify.display.mdAndUp"
    >
      <ChatLeftSidebarContent
        v-model:isDrawerOpen="isLeftSidebarOpen"
        v-model:search="q"
        @open-chat-of-contact="openChatOfContact"
        @show-user-profile="isUserProfileSidebarOpen = true"
        @close="isLeftSidebarOpen = false"
      />
    </VNavigationDrawer>

    <!-- 👉 Chat content -->
    <VMain class="chat-content-container">
      <!-- 👉 Right content: Active Chat -->
      <div
        v-if="store.getActiveChat"
        class="d-flex flex-column h-100"
      >
        <!-- 👉 Active chat header -->
        <div class="active-chat-header d-flex align-center text-medium-emphasis bg-surface">
          <!-- Sidebar toggler -->
          <IconBtn
            class="d-md-none me-3"
            @click="isLeftSidebarOpen = true"
          >
            <VIcon icon="tabler-menu-2" />
          </IconBtn>

          <!-- avatar -->
          <div
            class="d-flex align-center cursor-pointer"
            @click="isActiveChatUserProfileSidebarOpen = true"
          >
            <VBadge
              dot
              location="bottom right"
              offset-x="3"
              offset-y="0"
              :color="resolveAvatarBadgeVariant(store.getActiveChat?.contact?.is_online)"
              bordered
            >
              <VAvatar
                size="40"
                :variant="!store.getActiveChat?.contact?.info?.avatar?.path ? 'tonal' : undefined"
                :color="!store.getActiveChat?.contact?.info?.avatar?.path ? 'primary' : undefined"
                class="cursor-pointer"
              >
                <VImg
                  v-if="store.getActiveChat?.contact?.info?.avatar"
                  :src="getImageUrl(store.getActiveChat?.contact?.info?.avatar?.path)"
                  :alt="store.getActiveChat?.contact?.name_first"
                />
                <span v-else>{{ store.getActiveChat?.contact?.name_first?.charAt(0) + store.getActiveChat?.contact?.name_last?.charAt(0) }}</span>
              </VAvatar>
            </VBadge>

            <div class="flex-grow-1 ms-4 overflow-hidden">
              <div class="text-h6 mb-0 font-weight-regular">
                {{ store.getActiveChat?.contact?.name_first+ " " + store.getActiveChat?.contact?.name_last }}
              </div>
              <p class="text-truncate mb-0 text-body-2">
                {{ store.getActiveChat?.contact?.role }}
              </p>
            </div>
          </div>

          <VSpacer />

          <!-- Header right content -->
          <!--
            <div class="d-sm-flex align-center d-none text-medium-emphasis">
            <AppTextField
            v-model="search"
            placeholder="Search..."
            prepend-inner-icon="tabler-search"
            class="ms-4 me-1 chat-list-search"
            style="min-width: 200px;width: 100%;flex-grow: 1;"
            />
            </div> 
          -->
        </div>

        <VDivider />

        <!-- Chat log -->
        <PerfectScrollbar
          ref="chatLogPS"
          tag="ul"
          :options="{ wheelPropagation: false }"
          class="flex-grow-1"
        >
          <ChatLog />
        </PerfectScrollbar>

        <!-- Message form -->
        <VForm
          class="chat-log-message-form mb-5 mx-5"
          @submit.prevent="sendMessage(store.getActiveChat?.contact?.uuid, store.getActiveChat?.id)"
        >
          <VTextField
            :key="store.getActiveChat?.user_id"
            v-model="msg"
            variant="solo"
            density="default"
            class="chat-message-input"
            placeholder="Type your message..."
            autofocus
          >
            <template #append-inner>
              <div class="d-flex gap-1">
                <!-- File Attachment Icon -->
                <!--
                  <IconBtn @click="triggerFileInput">
                  <VIcon
                  icon="tabler-paperclip"
                  size="22"
                  />
                  </IconBtn> 
                -->

                <!-- File Preview -->
                <div
                  v-if="selectedFiles.length"
                  class="file-preview"
                >
                  <div
                    v-for="(file, index) in selectedFiles"
                    :key="index"
                    class="d-flex align-center"
                  >
                    <VIcon
                      icon="tabler-file"
                      class="me-1"
                    />
                    <span>{{ truncateDescription(file.name, 10) }}</span>
                    <IconBtn
                      class="ms-2 text-danger"
                      @click="removeFile(index)"
                    >
                      <VIcon
                        icon="tabler-x"
                        size="18"
                      />
                    </IconBtn>
                  </div>
                </div>
                <VBtn
                  :disabled="loadStatus === 1" 
                  @click="sendMessage(store.getActiveChat?.contact?.uuid, store.getActiveChat?.id)"
                >
                  <template #append>
                    <VIcon
                      icon="tabler-send"
                      color="#fff"
                    />
                  </template>
                  <VProgressCircular
                    v-if="loadStatus === 1"
                    indeterminate
                    size="16"
                    color="white"
                  />
                  <span v-if="loadStatus === 1">Sending...</span>
                  <span v-else>Send</span>
                </VBtn>
              </div>
            </template>
          </VTextField>

          <input
            ref="fileInput"
            type="file"
            accept=".jpeg,.png,.jpg,.gif,.pdf,.sql,.docx,.xls,.xlsx,.docx"
            hidden
            multiple
            @change="onFileSelected"
          >
        </VForm>
      </div>

      <!-- 👉 Start conversation -->
      <div
        v-else
        class="d-flex h-100 align-center justify-center flex-column"
      >
        <VAvatar
          size="98"
          variant="tonal"
          color="primary"
          class="mb-4"
        >
          <VIcon
            size="50"
            class="rounded-0"
            icon="tabler-message-2"
          />
        </VAvatar>

        <VBtn
          v-if="$vuetify.display.smAndDown"
          rounded="xl"
          @click="startConversation"
        >
          Start Conversation
        </VBtn>

        <p
          v-else
          style="max-inline-size: 40ch; text-wrap: balance;"
          class="text-center text-disabled"
        >
          Start connecting with the people by selecting one of the chat or contact on left
        </p>
      </div>
    </VMain>
  </VLayout>
</template>

<script setup>
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import Loader from "@/components/Loader.vue"
import {
  useDisplay,
  useTheme,
} from 'vuetify'
import { themes } from '@/plugins/vuetify/theme'
import sketch from '@images/icons/project-icons/sketch.png'
import { debounce, truncate } from 'lodash'
import ChatActiveChatUserProfileSidebarContent from '@/views/apps/chat/ChatActiveChatUserProfileSidebarContent.vue'
import ChatLeftSidebarContent from '@/views/apps/chat/ChatLeftSidebarContent.vue'
import ChatLog from '@/views/apps/chat/ChatLog.vue'
import ChatUserProfileSidebarContent from '@/views/apps/chat/ChatUserProfileSidebarContent.vue'
import { useChat } from '@/views/apps/chat/useChat'
import { useChatStore } from '@/store/chats'
import { useProjectStore } from "@/store/projects"
import { useRoute } from 'vue-router'

definePage({ meta: { layoutWrapperClasses: 'layout-content-height-fixed' } })
useHead({ title: `${layoutConfig.app.title} | Chat` })

// composables
const vuetifyDisplays = useDisplay()
const store = useChatStore()
const projectStore = useProjectStore()
const $route = useRoute()
const { isLeftSidebarOpen } = useResponsiveLeftSidebar(vuetifyDisplays.smAndDown)
const { resolveAvatarBadgeVariant } = useChat()

// Perfect scrollbar
const chatLogPS = ref()
const fileInput = ref(null)
const selectedFiles = ref([])

const scrollToBottomInChatLog = () => {
  const scrollEl = chatLogPS.value.$el || chatLogPS.value

  scrollEl.scrollTop = scrollEl.scrollHeight
}

onBeforeMount(async () => {
  await fetchProject()
})

const fetchProject = async () => {
  const inboxId = $route.query.inbox
  if (inboxId) {
    openChatOfContact(inboxId) 
  } else {
    callFirstContact() 
  }
  await projectStore.show(projectUuid)
}

const callFirstContact = () => {
  const inbox = store.getContacts
  const firstContact = inbox?.[0]
  
  if (firstContact) {
    openChatOfContact(firstContact.uuid)
  } else {
    console.log('No contacts available in the inbox')
  }
}

// Search query
const q = ref('')
const projectUuid = $route.params.id

watch(
  q,
  debounce(val => store.getProjectInbox(projectUuid, val), 300),
  { immediate: true },
)

// Open Sidebar in smAndDown when "start conversation" is clicked
const startConversation = () => {
  if (vuetifyDisplays.mdAndUp.value)
    return
  isLeftSidebarOpen.value = true
}

// Chat message
const msg = ref('')

const sendMessage = async (contactId, chatId) => {
  if (!msg.value && selectedFiles.value.length === 0) return 

  const payload = {
    projectId: projectUuid,
    userId: contactId,
    chatId: chatId,
    message: msg.value,
    files: selectedFiles.value,
  }

  await store.sendMsg(payload)

  msg.value = ''
  selectedFiles.value = []

  nextTick(() => {
    scrollToBottomInChatLog()
  })
}


const triggerFileInput = () => {
  fileInput.value.click()
}

const onFileSelected = event => {
  selectedFiles.value = Array.from(event.target.files)
}
  
const removeFile = index => {
  selectedFiles.value.splice(index, 1)
}

const truncateDescription = (description, length) => {
  return description.length > length
    ? description.slice(0, length) + '...'
    : description
}

const openChatOfContact = async userId => {
  const payload = {
    projectId: $route.params.id,
    userId: userId,
  }

  await store.getChat(payload)

  // Reset message input
  msg.value = ''

  // Set unseenMsgs to 0
  const contact = store.getChatsContacts.find(c => c.contact.uuid === userId)
  if (contact)
    contact.unseenMsgs = 0

  // if smAndDown =>  Close Chat & Contacts left sidebar
  if (vuetifyDisplays.smAndDown.value)
    isLeftSidebarOpen.value = false

  // Scroll to bottom
  nextTick(() => {
    scrollToBottomInChatLog()
  })
}

// User profile sidebar
const isUserProfileSidebarOpen = ref(false)

// Active chat user profile sidebar
const isActiveChatUserProfileSidebarOpen = ref(false)

// file input
const refInputEl = ref()
const { name } = useTheme()

const chatContentContainerBg = computed(() => {
  let color = 'transparent'
  if (themes)
    color = themes?.[name.value].colors?.background

  return color
})

const project = computed(() =>{
  return projectStore.getProject
})

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

const loadStatus = computed(() => {
  return store.getLoadStatus
})
</script>

<style lang="scss">
@use "@styles/variables/vuetify.scss";
@use "@core-scss/base/mixins.scss";
@use "@layouts/styles/mixins" as layoutsMixins;

// Variables
$chat-app-header-height: 76px;

// Placeholders
%chat-header {
  display: flex;
  align-items: center;
  min-block-size: $chat-app-header-height;
  padding-inline: 1.5rem;
}

.chat-start-conversation-btn {
  cursor: default;
}

.chat-app-layout {
  height: 500px!important;
  border-radius: vuetify.$card-border-radius;

  @include mixins.elevation(vuetify.$card-elevation);

  $sel-chat-app-layout: &;

  @at-root {
    .skin--bordered {
      @include mixins.bordered-skin($sel-chat-app-layout);
    }
  }

  .active-chat-user-profile-sidebar,
  .user-profile-sidebar {
    .v-navigation-drawer__content {
      display: flex;
      flex-direction: column;
    }
  }

  .chat-list-header,
  .active-chat-header {
    @extend %chat-header;
  }

  .chat-list-sidebar {
    .v-navigation-drawer__content {
      display: flex;
      flex-direction: column;
    }
  }
}

.chat-content-container {
  background-color: v-bind(chatContentContainerBg);

  // Adjust the padding so text field height stays 48px
  .chat-message-input {
    .v-field__input {
      font-size: 0.9375rem !important;
      line-height: 1.375rem !important;
      padding-block: 0.6rem 0.5rem;
    }

    .v-field__append-inner {
      align-items: center;
      padding-block-start: 0;
    }

    .v-field--appended {
      padding-inline-end: 8px;
    }
  }
}

.chat-user-profile-badge {
  .v-badge__badge {
    min-width: 12px !important;
    height: 0.75rem;
  }
}
.file-preview {
  display: flex;
  align-items: center;
  padding: 4px 8px;
  background-color: #f0f2f5;
  border-radius: 4px;
  font-size: 12px;
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>

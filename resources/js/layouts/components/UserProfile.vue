<template>
  <VBadge
    dot
    location="bottom right"
    offset-x="3"
    offset-y="3"
    bordered
    color="success"
  >
    <VAvatar
      class="cursor-pointer"
      color="primary"
      variant="tonal"
    >
      <template v-if="userDetails?.info?.avatar?.path">
        <VImg :src="getImageUrl(userDetails?.info?.avatar.path)" />
      </template>
      <template v-else>
        <span>
          {{ userDetails?.name_first.charAt(0) + userDetails?.name_last.charAt(0) }}
        </span>
      </template>

      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="230"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar
                    color="primary"
                    variant="tonal"
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
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ userDetails?.name_first + ' ' + userDetails?.name_last }}
            </VListItemTitle>
            <VListItemSubtitle><small style="position: relative;top:-6px;">{{ userDetails?.role }}</small></VListItemSubtitle>
            <VListItemSubtitle>{{ userDetails?.company }}</VListItemSubtitle>
          </VListItem>

          <VDivider class="my-2" />

          <!-- 👉 Profile -->
          <VListItem to="/account-setting">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="tabler-user"
                size="22"
              />
            </template>

            <VListItemTitle>Profile</VListItemTitle>
          </VListItem>

          <!-- 👉 Logout -->
          <VListItem @click="logout">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="tabler-logout"
                size="22"
              />
            </template>

            <VListItemTitle>Logout</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>

<script setup>
import avatar1 from '@images/avatars/avatar-1.png'
import { useAuthStore } from "@/store/auth"
import { useUserStore } from "@/store/users"

onMounted(() => {
  getUser()
})

const userStore = useUserStore()

const authStore = useAuthStore()
const router = useRouter()

async function logout() {
  let token = authStore.token
  let res = await authStore.logout(token)
  if(res.success) {
    router.push({ name: 'login' })
  } else {
    alert(res.data.error)
  }
}

const getUser = async () => {
  let user = JSON.parse(localStorage.getItem('user'))
  const uuid = user.user?.uuid

  await userStore.show(uuid)
}

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}


const userDetails = computed(() => {
  return userStore.getUser
})
</script>

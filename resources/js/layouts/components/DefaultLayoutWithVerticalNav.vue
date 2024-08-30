<script setup>
import navItems from '@/navigation/vertical'
import { themeConfig } from '@themeConfig'

import NavbarThemeSwitcher from '@/layouts/components/NavbarThemeSwitcher.vue'
import NavBarNotifications from '@/layouts/components/NavBarNotifications.vue'
import UserProfile from '@/layouts/components/UserProfile.vue'
import NavBarI18n from '@core/components/I18n.vue'
import { useRoute } from 'vue-router'
import { useProjectStore } from "@/store/projects"
import { useAuthStore } from "@/store/auth"

// @layouts plugin
import { VerticalNavLayout } from '@layouts'

// SECTION: Loading Indicator
const isFallbackStateActive = ref(false)
const refLoadingIndicator = ref(null)

const $route = useRoute()

const projectStore = useProjectStore()
const authStore = useAuthStore()
const projectId = ref(null)
const projectType = ref(null)

watchEffect(() => {
  projectId.value = $route.params.id
})

watch([
  isFallbackStateActive,
  refLoadingIndicator,
], () => {

  if (isFallbackStateActive.value && refLoadingIndicator.value)
    refLoadingIndicator.value.fallbackHandle()
  if (!isFallbackStateActive.value && refLoadingIndicator.value)
    refLoadingIndicator.value.resolveHandle()
}, { immediate: true })

const showNavigation = computed(() => {
  return $route.params.id !== undefined
})
// !SECTION

const project = computed(() =>{
  return projectStore.getProject
})
</script>

<template>
  <VerticalNavLayout :nav-items="navItems">
    <!-- 👉 navbar -->
    <template #navbar="{ toggleVerticalOverlayNavActive }">
      <div class="d-flex h-100 align-center">
        <IconBtn
          id="vertical-nav-toggle-btn"
          class="ms-n3 d-lg-none"
          @click="toggleVerticalOverlayNavActive(true)"
        >
          <VIcon
            size="26"
            icon="tabler-menu-2"
          />
        </IconBtn>

        <NavbarThemeSwitcher />
        <div
          v-if="showNavigation"
          class="d-flex align-center"
        >
          <RouterLink :to="`/projects/web-designs/${projectId}`">
            <span
              class="text-h6 ms-3 me-5 inner-badge-text"
              :class="{ 'text-primary': $route.path === `/projects/web-designs/${projectId}` }"
            >Overview</span>
          </RouterLink>
          <RouterLink :to="`/projects/${projectId}/tasks/add`">
            <span
              class="text-h6 me-8 inner-badge-text"
              :class="{ 'text-primary': $route.path === `/projects/${projectId}/tasks/add` }"
            >
              Tasks
              <!--
                <VBadge
                class="new-badge"
                color="error"
                content="390"
                >
                <span class="inner-badge-text">Tasks</span>
                </VBadge>
              -->
            </span>
          </RouterLink>
          <RouterLink
            :to="`/projects/${projectId}/milestones`"
            class="text-h6 me-8 inner-badge-text"
            :class="{ 'text-primary': $route.path === `/projects/${projectId}/milestones` }"
          >
            Milestones
          </RouterLink>
          <RouterLink :to="`/projects/${projectId}/calendar`">
            <span
              class="text-h6 me-8 inner-badge-text"
              :class="{ 'text-primary': $route.path === `/projects/${projectId}/calendar` }"
            >Calendar</span>
          </RouterLink>
          <RouterLink :to="`/projects/${projectId}/files`">
            <span
              class="text-h6 me-8 inner-badge-text"
              :class="{ 'text-primary': $route.path === `/projects/${projectId}/files` }"
            >Files</span>
          </RouterLink>
          <RouterLink :to="`/projects/${projectId}/chat`">
            <span
              class="text-h6 me-5 inner-badge-text"
              :class="{ 'text-primary': $route.path === `/projects/${projectId}/chat` }"
            >
              Inbox
              <!--
                <VBadge
                class="new-badge"
                color="error"
                content="14"
                >
                <span class="inner-badge-text">Inbox</span>
                </VBadge>
              -->
            </span>
          </RouterLink>
          <RouterLink :to="`/projects/${projectId}/team`">
            <span
              class="text-h6 me-8 inner-badge-text"
              :class="{ 'text-primary': $route.path === `/projects/${projectId}/team` }"
            >Your Team</span>
          </RouterLink>
          <RouterLink :to="`/projects/${projectId}/payments`">
            <span
              class="text-h6 me-8 inner-badge-text"
              :class="{ 'text-primary': $route.path === `/projects/${projectId}/payments` }"
            >Payments</span>
          </RouterLink>
        </div>
        <VSpacer />

        <span
          v-if="showNavigation"
          class="text-h6 font-weight-bold me-4"
        >
          <span v-if="authStore.isAdmin || authStore.isManager">
            <RouterLink :to="`/projects/${projectId}/bucks`">
              Darby Bucks <span class="text-primary">${{ project?.bucks_share_amount }}</span>
            </RouterLink>
          </span>
          <span v-else>
            <RouterLink :to="`/projects/${projectId}/bucks?tab=manage-bucks`">
              Earned Bucks <span class="text-primary">${{ project?.bucks_earnings }}</span>
            </RouterLink>
          </span>
        </span>
        <NavBarNotifications class="me-2" />
        <UserProfile />
      </div>
    </template>

    <AppLoadingIndicator ref="refLoadingIndicator" />

    <!-- 👉 Pages -->
    <RouterView v-slot="{ Component }">
      <Suspense
        :timeout="0"
        @fallback="isFallbackStateActive = true"
        @resolve="isFallbackStateActive = false"
      >
        <Component :is="Component" />
      </Suspense>
    </RouterView>

    <!-- 👉 Customizer -->
    <!-- <TheCustomizer /> -->
  </VerticalNavLayout>
</template>

<style lang="scss" scoped>
.inner-badge-text{
    font-size: .85rem!important;
}
</style>

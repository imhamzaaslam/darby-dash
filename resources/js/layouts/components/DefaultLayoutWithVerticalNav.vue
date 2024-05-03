<script setup>
import navItems from '@/navigation/vertical'
import { themeConfig } from '@themeConfig'

import NavbarThemeSwitcher from '@/layouts/components/NavbarThemeSwitcher.vue'
import NavBarNotifications from '@/layouts/components/NavBarNotifications.vue'
import UserProfile from '@/layouts/components/UserProfile.vue'
import NavBarI18n from '@core/components/I18n.vue'
import { useRoute } from 'vue-router'

// @layouts plugin
import { VerticalNavLayout } from '@layouts'

// SECTION: Loading Indicator
const isFallbackStateActive = ref(false)
const refLoadingIndicator = ref(null)

const $route = useRoute()

const projectId = ref(null)
const projectType = ref(null)

const fetchProjectParams = () => {
  projectId.value = $route.params.id
  projectType.value = $route.params.project
}

onMounted(fetchProjectParams)

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
  fetchProjectParams()

  return $route.params.id !== undefined
})
// !SECTION
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
          <a
            href="#"
            class="text-h6 ms-3 me-5 text-primary"
          >Overview</a>
          <a
            href="#"
            class="text-h6 me-5"
          >
            <VBadge
              class="new-badge"
              color="error"
              content="14"
            >
              <span class="inner-badge-text">Inbox</span>
            </VBadge>
          </a>
          <RouterLink :to="`/projects/${projectType}/${projectId}/files`">
            <span
              class="text-h6 me-8 inner-badge-text"
            >Files</span>
          </RouterLink>
          <a
            href="#"
            class="text-h6 me-8"
          >
            <VBadge
              class="new-badge"
              color="error"
              content="390"
            >
              <span class="inner-badge-text">Tasks</span>
            </VBadge>
          </a>
          <a
            href="#"
            class="text-h6 me-8 inner-badge-text"
          >Project Scope</a>
          <a
            href="#"
            class="text-h6 me-8 inner-badge-text"
          >Milestones</a>
          <RouterLink :to="`/projects/${projectType}/${projectId}/calendar`">
            <span class="text-h6 me-8 inner-badge-text">Calendar</span>
          </RouterLink>
          <a
            href="#"
            class="text-h6 me-8 inner-badge-text"
          >Your Team</a>
        </div>

        <VSpacer />

        <NavBarI18n
          v-if="themeConfig.app.i18n.enable && themeConfig.app.i18n.langConfig?.length"
          :languages="themeConfig.app.i18n.langConfig"
        />
        <span class="text-h6 font-weight-bold me-4">Darby Bucks <span class="text-primary">$200</span></span>
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
    font-size: 0.85rem!important;
}
</style>

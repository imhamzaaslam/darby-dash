<script setup>
import navItems from '@/navigation/vertical'
import { themeConfig } from '@themeConfig'
import { layoutConfig } from '@layouts'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'

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

onMounted(async () => {
  await authStore.tenantInfo()
})

const generalSetting = computed(() => {
  return authStore.getGeneralSetting
})

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
  const isManageTemplatesRoute = $route.name === 'manage-templates'
  const isManageCompanyRoute = $route.name === 'manage-company-settings'

  return !(isManageTemplatesRoute || isManageCompanyRoute) && $route.params.id !== undefined
})

// !SECTION

const project = computed(() =>{
  return projectStore.getProject
})
</script>

<template>
  <VerticalNavLayout :nav-items="navItems">
    <!-- 👉 navbar -->
    <template #logoNavbar="{ toggleVerticalOverlayNavActive }">
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
        <RouterLink
          to="/"
          class="app-logo app-title-wrapper"
        >
          <VNodeRenderer :nodes="layoutConfig.app.logo" />
        </RouterLink>
        <NavbarThemeSwitcher />
        <NavBarNotifications class="me-2" />
        <UserProfile />
      </div>
    </template>

    <template #navbar>
      <div class="d-flex h-100 align-center">
        <!-- Dropdown for router links on mobile view -->

        <IconBtn
          v-if="showNavigation"
          class="ms-n1 d-lg-none"
          @click.prevent
        >
          <VIcon
            size="26"
            icon="tabler-dots-vertical"
          />
          <VMenu
            activator="parent"
            class="d-lg-none"
            offset-y
          >
            <VList>
              <VListItem
                v-for="link in [
                  { text: 'Overview', route: `/projects/web-designs/${projectId}` },
                  { text: 'Tasks', route: `/projects/${projectId}/tasks/add` },
                  { text: 'Milestones', route: `/projects/${projectId}/milestones` },
                  { text: 'Calendar', route: `/projects/${projectId}/calendar` },
                  { text: 'Files', route: `/projects/${projectId}/files` },
                  { text: 'Inbox', route: `/projects/${projectId}/chat` },
                  { text: 'Your Team', route: `/projects/${projectId}/team` },
                  { text: 'Marketplace', route: `/projects/${projectId}/marketplace` },
                  /* { text: 'Payments', route: `/projects/${projectId}/payments` } */
                ]"
                :key="link.text"
              >
                <RouterLink :to="link.route">
                  <VListItemTitle>{{ link.text }}</VListItemTitle>
                </RouterLink>
              </VListItem>
              <!-- Darby Bucks/Earned Bucks for mobile view -->
              <VListItem v-if="project?.bucks_share">
                <RouterLink :to="authStore.isAdmin || authStore.isManager ? `/projects/${projectId}/bucks` : `/projects/${projectId}/bucks?tab=manage-bucks`">
                  <VListItemTitle>
                    <span v-if="authStore.isAdmin || authStore.isManager">
                      {{ generalSetting?.bucks_label || 'Darby Bucks' }}
                      <span class="text-primary">${{ project?.bucks_share_amount }}</span>
                    </span>
                    <span v-else>
                      Earned {{ generalSetting?.bucks_label || 'Darby Bucks' }}
                      <span class="text-primary">${{ project?.bucks_earnings }}</span>
                    </span>
                  </VListItemTitle>
                </RouterLink>
              </VListItem>
            </VList>
          </VMenu>
        </IconBtn>
        <div
          v-if="showNavigation"
          class="d-none d-lg-flex align-center"
        >
          <RouterLink :to="`/projects/web-designs/${projectId}`">
            <span
              class="text-h6 inner-badge-text"
              :class="{ 'text-primary': $route.path === `/projects/web-designs/${projectId}` }"
            >Overview</span>
          </RouterLink>
          <RouterLink :to="`/projects/${projectId}/tasks/add`">
            <span
              class="text-h6 inner-badge-text"
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
            class="text-h6 inner-badge-text"
            :class="{ 'text-primary': $route.path === `/projects/${projectId}/milestones` }"
          >
            Milestones
          </RouterLink>
          <RouterLink :to="`/projects/${projectId}/calendar`">
            <span
              class="text-h6 inner-badge-text"
              :class="{ 'text-primary': $route.path === `/projects/${projectId}/calendar` }"
            >Calendar</span>
          </RouterLink>
          <RouterLink :to="`/projects/${projectId}/files`">
            <span
              class="text-h6 inner-badge-text"
              :class="{ 'text-primary': $route.path === `/projects/${projectId}/files` }"
            >Files</span>
          </RouterLink>
          <RouterLink :to="`/projects/${projectId}/chat`">
            <span
              class="text-h6 inner-badge-text"
              :class="{ 'text-primary': $route.path === `/projects/${projectId}/chat` }"
            >
              <VBadge
                v-if="project?.total_unseen_messages"
                class="new-badge"
                color="error"
                :content="project?.total_unseen_messages"
              >
                <span class="inner-badge-text">Inbox</span>
              </VBadge>
              <span v-else>Inbox</span>
            </span>
          </RouterLink>
          <RouterLink :to="`/projects/${projectId}/team`">
            <span
              class="text-h6 inner-badge-text"
              :class="{ 'text-primary': $route.path === `/projects/${projectId}/team` }"
            >Your Team</span>
          </RouterLink>
          <RouterLink :to="`/projects/${projectId}/marketplace`">
            <span
              class="text-h6 inner-badge-text"
              :class="{ 'text-primary': $route.path === `/projects/${projectId}/marketplace` }"
            >Marketplace</span>
          </RouterLink> 
          <!--
            <RouterLink :to="`/projects/${projectId}/payments`">
            <span
            class="text-h6 me-8 inner-badge-text"
            :class="{ 'text-primary': $route.path === `/projects/${projectId}/payments` }"
            >Payments</span>
            </RouterLink> 
          -->
        </div>
        <VSpacer />

        <span
          v-if="showNavigation && project?.bucks_share"
          class="text-h6 font-weight-bold me-4 d-none d-lg-flex"
        >
          <span v-if="authStore.isAdmin || authStore.isManager">
            <RouterLink :to="`/projects/${projectId}/bucks`">
              {{ generalSetting?.bucks_label || 'Darby Bucks' }} <span class="text-primary">${{ project?.bucks_share_amount }}</span>
            </RouterLink>
          </span>
          <span v-else>
            <RouterLink :to="`/projects/${projectId}/bucks?tab=manage-bucks`">
              Earned {{ generalSetting?.bucks_label || 'Darby Bucks' }} <span class="text-primary">${{ project?.bucks_earnings }}</span>
            </RouterLink>
          </span>
        </span>
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
    font-size: 1.1rem!important;
    padding: 0 15px;
}
.app-title-wrapper {
    margin-inline-end: auto;
  }
</style>

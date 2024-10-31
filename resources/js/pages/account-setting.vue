<template>
  <div>
    <VTabs
      v-model="activeTab"
      class="v-tabs-pill"
    >
      <VTab
        v-for="item in tabs"
        :key="item.icon"
        :value="item.tab"
        @click="setActiveTab(item.tab)"
      >
        <VIcon
          size="20"
          start
          :icon="item.icon"
        />
        {{ item.title }}
      </VTab>
    </VTabs>

    <VWindow
      v-model="activeTab"
      class="mt-6 disable-tab-transition"
      :touch="false"
    >
      <!-- Account -->
      <VWindowItem value="account">
        <AccountSettingsAccount />
      </VWindowItem>

      <!-- Security -->
      <VWindowItem value="security">
        <AccountSettingsSecurity />
      </VWindowItem>

      <!-- Billing -->
      <VWindowItem
        v-if="!authStore.isSuperAdmin"
        value="billing-plans"
      >
        <AccountSettingsBillingAndPlans />
      </VWindowItem>

      <!-- Notification -->
      <VWindowItem
        v-if="!authStore.isSuperAdmin"
        value="notifications"
      >
        <AccountSettingsNotification />
      </VWindowItem>
    </VWindow>
  </div>
</template>

<script setup="js">
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import AccountSettingsAccount from '@/views/pages/account-settings/AccountSettingsAccount.vue'
import AccountSettingsBillingAndPlans from '@/views/pages/account-settings/AccountSettingsBillingAndPlans.vue'
import AccountSettingsNotification from '@/views/pages/account-settings/AccountSettingsNotification.vue'
import AccountSettingsSecurity from '@/views/pages/account-settings/AccountSettingsSecurity.vue'
import { useAuthStore } from "@/store/auth"
import { watch, ref, onBeforeMount } from 'vue'
import { useRouter } from 'vue-router'

useHead({ title: `${layoutConfig.app.title} | Account` })

const authStore = useAuthStore()
const activeTab = ref('account')
const router = useRouter()

// Tabs
const tabs = [
  { title: 'Account', icon: 'tabler-users', tab: 'account' },
  { title: 'Security', icon: 'tabler-lock', tab: 'security' },
  !authStore.isSuperAdmin && { title: 'Billing & Plans', icon: 'tabler-file-text', tab: 'billing-plans' },
  !authStore.isSuperAdmin && { title: 'Notifications', icon: 'tabler-bell', tab: 'notifications' },
].filter(Boolean) // filter out any `false` values

// Function to set the active tab
function setActiveTab(tab) {
  activeTab.value = tab
}

// Initialize active tab based on URL query and available tabs
onBeforeMount(() => {
  const searchParams = new URLSearchParams(window.location.search)
  const requestedTab = searchParams.get('tab')
  
  if (requestedTab && tabs.some(tab => tab.tab === requestedTab)) {
    setActiveTab(requestedTab)
  }
})

// Watch activeTab and update URL
watch(activeTab, newActiveTab => {
  router.push({ query: { tab: newActiveTab } })
  useHead({ title: `${layoutConfig.app.title} | ${tabs.find(tab => tab.tab === newActiveTab).title}` })
})

// Define page settings
definePage({ meta: { navActiveLink: 'account-setting' } })
</script>

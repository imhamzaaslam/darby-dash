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
      <VWindowItem value="billing-plans">
        <AccountSettingsBillingAndPlans />
      </VWindowItem>

      <!-- Notification -->
      <VWindowItem value="notifications">
        <AccountSettingsNotification />
      </VWindowItem>

      <!-- Danger Zone -->
      <!--
        <VWindowItem value="danger-zone">
        <AccountSettingsDangerZone />
        </VWindowItem>
      -->
    </VWindow>
  </div>
</template>

<script setup="js">
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import AccountSettingsAccount from '@/views/pages/account-settings/AccountSettingsAccount.vue'
import AccountSettingsBillingAndPlans from '@/views/pages/account-settings/AccountSettingsBillingAndPlans.vue'
import AccountSettingsDangerZone from '@/views/pages/account-settings/AccountSettingsDangerZone.vue'
import AccountSettingsNotification from '@/views/pages/account-settings/AccountSettingsNotification.vue'
import AccountSettingsSecurity from '@/views/pages/account-settings/AccountSettingsSecurity.vue'
import { watch, ref } from 'vue'

import { useRouter } from 'vue-router'

useHead({ title: `${layoutConfig.app.title} | Account` })
onBeforeMount(() => {
  const searchParams = new URLSearchParams(window.location.search)
  const activeTab = searchParams.get('tab')
  if (activeTab && ['account', 'security', 'billing-plans', 'notifications'].includes(activeTab)) {
    setActiveTab(activeTab)
  }
})

const activeTab = ref('account')
const router = useRouter()

// tabs
const tabs = [
  {
    title: 'Account',
    icon: 'tabler-users',
    tab: 'account',
  },
  {
    title: 'Security',
    icon: 'tabler-lock',
    tab: 'security',
  },
  {
    title: 'Billing & Plans',
    icon: 'tabler-file-text',
    tab: 'billing-plans',
  },
  {
    title: 'Notifications',
    icon: 'tabler-bell',
    tab: 'notifications',
  },

  // {
  //   title: 'Danger Zone',
  //   icon: 'tabler-alert-triangle',
  //   tab: 'danger-zone',
  // },
]

function setActiveTab(tab) {
  activeTab.value = tab
}

// use watch to change the active tab and then pass to the route as query param
watch(activeTab, newActiveTab => {
  router.push({
    query: {
      tab: newActiveTab,
    },
  })

  useHead({ title: `${layoutConfig.app.title} | ${tabs.find(tab => tab.tab === newActiveTab).title}` })
})

definePage({ meta: { navActiveLink: 'account-setting' } })
</script>

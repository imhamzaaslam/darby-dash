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
      <VWindowItem value="bucks-overview">
        <BucksOverview />
      </VWindowItem>

      <!-- Security -->
      <VWindowItem value="manage-bucks">
        <MangageBucks />
      </VWindowItem>
    </VWindow>
  </div>
</template>

<script setup="js">
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import BucksOverview from '@/views/pages/bucks-settings/BucksOverview.vue'
import MangageBucks from '@/views/pages/bucks-settings/ManageBucks.vue'
import { watch, ref } from 'vue'

import { useRouter } from 'vue-router'

useHead({ title: `${layoutConfig.app.title} | Bucks` })
onBeforeMount(() => {
  const searchParams = new URLSearchParams(window.location.search)
  const activeTab = searchParams.get('tab')
  if (activeTab && ['bucks-overview', 'manage-bucks',].includes(activeTab)) {
    setActiveTab(activeTab)
  }
})

const activeTab = ref('bucks-overview')
const router = useRouter()

// tabs
const tabs = [
  {
    title: 'Bucks Overview',
    icon: 'tabler-dashboard',
    tab: 'bucks-overview',
  },
  {
    title: 'Manage Tasks',
    icon: 'tabler-coin',
    tab: 'manage-bucks',
  },
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

definePage({ meta: { navActiveLink: 'bucks-overview' } })
</script>

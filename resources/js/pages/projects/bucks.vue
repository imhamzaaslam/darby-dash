<template>
  <VRow>
    <VCol cols="12">
      <div class="d-flex justify-start align-center">
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
  <div>
    <VTabs
      v-if="authStore.isAdmin || authStore.isManager"
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
      <VWindowItem
        v-if="authStore.isAdmin || authStore.isManager"
        value="bucks-overview"
      >
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
import { useAuthStore } from "@/store/auth"
import { useProjectStore } from "@/store/projects"
import { watch, ref } from 'vue'
import sketch from '@images/icons/project-icons/sketch.png'

import { useRouter, useRoute } from 'vue-router'

useHead({ title: `${layoutConfig.app.title} | Bucks` })
onBeforeMount(async () => {
  const searchParams = new URLSearchParams(window.location.search)
  const activeTab = searchParams.get('tab')
  if (activeTab && ['bucks-overview', 'manage-bucks'].includes(activeTab)) {
    setActiveTab(activeTab)
  }
  await fetchProject()
})

const authStore = useAuthStore()
const projectStore = useProjectStore()
const activeTab = ref('bucks-overview')
const router = useRouter()
const route = useRoute()
const projectUuid = route.params.id

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

const fetchProject = async () => {
  try {
    await projectStore.show(projectUuid)
  } catch (error) {
    toast.error('Error fetching project:', error)
  }
}

function setActiveTab(tab) {
  activeTab.value = tab
}

const project = computed(() =>{
  return projectStore.getProject
})

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

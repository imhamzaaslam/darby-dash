<template>
  <h3>Project Dashboards</h3>
  <VRow class="mt-3">
    <VCol
      v-for="(data, index) in dashboards"
      :key="index"
      cols="12"
      md="4"
      sm="4"
    >
      <div>
        <RouterLink :to="{ path: data.url, query: { type: data.type } }">
          <VCard
            class="logistics-card-statistics cursor-pointer"
            :style="data.isHover ? { 'border-block-end': `2px solid rgba(var(--v-theme-${data.color}))` } : { 'border-block-end': `2px solid rgba(var(--v-theme-${data.color}), var(--v-disabled-opacity))` }"
            @mouseenter="data.isHover = true"
            @mouseleave="data.isHover = false"
          >
            <VCardText>
              <div class="d-flex align-center gap-x-4 mb-2">
                <VAvatar
                  variant="tonal"
                  :color="data.color"
                  rounded
                >
                  <VIcon
                    :icon="data.icon"
                    size="28"
                  />
                </VAvatar>
                <h5 class="text-h6 text-center">
                  {{ data.title }}
                </h5>
              </div>
              <VRow>
                <VCol
                  cols="12"
                  class="text-center"
                >
                  <small class="font-weight-medium text-high-emphasis text-sm text-truncate">Projects:<strong> 10 | </strong></small>
                  <small class="font-weight-medium text-high-emphasis text-sm text-truncate">Tasks:<strong> 6 | </strong></small>
                  <small class="font-weight-medium text-high-emphasis text-sm text-truncate">Team:<strong> 3 | </strong></small>
                  <small class="font-weight-medium text-high-emphasis text-sm text-truncate">File:<strong> 10 </strong></small>
                </VCol>
              </VRow>
            </VCardText>
          </VCard>
        </RouterLink>
      </div>
    </VCol>
  </VRow>
  <!--
    <VBtn
    color="primary"
    class="mt-3"
    size="small"
    @click="checkApi"
    >
    Check API
    </VBtn>
  -->
</template>

<script setup>
import { useProjectStore } from '../store/projects'

const projectStore = useProjectStore()

// async function checkApi() {
//   let res = await projectStore.getAll()
//   console.log(res)
// }

const dashboards = ref([
  {
    icon: 'tabler-world',
    color: 'primary',
    title: 'Website Design Project',
    url: '/projects/web-designs',
    type: 1,
    isHover: false,
  },
  {
    icon: 'tabler-military-rank',
    color: 'primary',
    title: 'SEO Program',
    url: '/projects/web-designs',
    type: 2,
    isHover: false,
  },
  {
    icon: 'tabler-brand-google',
    color: 'primary',
    title: 'Google Ads Program',
    url: '/projects/web-designs',
    type: 3,
    isHover: false,
  },
])
</script>

<style lang="scss" scoped>
@use "@core-scss/base/mixins" as mixins;

.logistics-card-statistics:hover {
  @include mixins.elevation(12);

  transition: all 0.1s ease-out;
}
</style>

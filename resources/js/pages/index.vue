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
              <h5 class="text-h5 font-weight-medium text-center">
                {{ data.title }}
              </h5>
            </div>
            <div class="text-center">
              <RouterLink :to="data.url">
                <VBtn
                  size="small"
                  rounded="pill"
                  color="primary"
                >
                  Access Projects
                </VBtn>
              </RouterLink>
            </div>
          </VCardText>
        </VCard>
      </div>
    </VCol>
  </VRow>
  <VBtn
    color="primary"
    class="mt-3"
    size="small"
    @click="checkApi"
  >
    Check API
  </VBtn>
</template>

<script setup>
import { useProjectStore } from '../store/projects'

const projectStore = useProjectStore()
async function checkApi() {
  let res = await projectStore.getAll()
  console.log(res)
}

const dashboards = ref([
  {
    icon: 'tabler-world',
    color: 'primary',
    title: 'Website Design Project',
    url: '/projects/web-designs',
    isHover: false,
  },
  {
    icon: 'tabler-military-rank',
    color: 'primary',
    title: 'SEO Program',
    url: '/projects/seo-programs',
    isHover: false,
  },
  {
    icon: 'tabler-brand-google',
    color: 'primary',
    title: 'Google Ads Program',
    url: '/projects/google-ads-programs',
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

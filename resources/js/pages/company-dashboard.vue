<template>
  <div>
    <div class="d-flex align-center">
      <VAvatar
        icon="tabler-align-box-bottom-center"
        size="36"
        class="me-2"
        color="primary"
        variant="tonal"
      />
      <h3 class="text-primary">
        Project Dashboards
      </h3>
    </div>
  </div>
  <VRow class="mt-3">
    <VCol
      cols="12"
      md="4"
      sm="12"
    >
      <div>
        <RouterLink :to="{ path: '/projects/web-designs', query: { type: projectType.id } }">
          <VCard class="logistics-card-statistics cursor-pointer">
            <VCardText>
              <div class="d-flex align-center gap-x-4 mb-2">
                <VAvatar
                  variant="tonal"
                  color="primary"
                  rounded
                >
                  <VIcon
                    :icon="projectType.icon"
                    size="28"
                  />
                </VAvatar>
                <h5 class="text-h6 text-center">
                  {{ projectType.name }}
                </h5>
              </div>
              <VRow>
                <VCol
                  cols="12"
                  class="text-center"
                >
                  <small class="font-weight-medium text-high-emphasis text-sm text-truncate">Projects: <strong> {{ projectType.total_projects }} | </strong></small>
                  <small class="font-weight-medium text-high-emphasis text-sm text-truncate">Tasks: <strong> {{ projectType.total_tasks }} | </strong></small>
                  <small class="font-weight-medium text-high-emphasis text-sm text-truncate">Members: <strong> {{ projectType.total_members }} | </strong></small>
                  <small class="font-weight-medium text-high-emphasis text-sm text-truncate">File: <strong> {{ projectType.total_files }} </strong></small>
                </VCol>
              </VRow>
            </VCardText>
          </VCard>
        </RouterLink>
      </div>
    </VCol>
  </VRow>
</template>
  
<script setup>
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import { useProjectTypeStore } from '@/store/project_types'
  
useHead({ title: `${layoutConfig.app.title} | Project Dashboards` })
onBeforeMount(async () => {
  await fetchProjectTypes()
})
  
const projectTypeStore = useProjectTypeStore()
  
const fetchProjectTypes = async () => {
  try {
    await projectTypeStore.getAll()
  } catch (error) {
    toast.error('Failed to get project types:', error.message || error)
  }
}
  
const projectType = computed(() => {
  return projectTypeStore.getProjectTypesWithAttributes
})
</script>
  
  <style lang="scss" scoped>
  @use "@core-scss/base/mixins" as mixins;
  
  .logistics-card-statistics:hover {
    @include mixins.elevation(12);
  
    transition: all 0.1s ease-out;
  }
  .logistics-card-statistics {
    border-block-end: 2px solid rgba(var(--v-theme-primary));
  }
  .logistics-card-statistics:hover {
    border-block-end: 2px solid rgba(var(--v-theme-primary));
  }
  </style>
  
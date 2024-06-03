<template>
  <div>
    <!-- Toggle -->
    <VRow>
      <VCol
        cols="12"
        md="7"
        class="d-flex"
      >
        <VBtnToggle
          v-model="viewType"
          class="d-toggle"
          rounded="0"
        >
          <VIcon
            icon="tabler-list"
            class="me-1"
            :class="{ 'bg-primary': viewType === 'list' }"
            @click="viewType = 'list'"
          />
          <VIcon
            icon="tabler-layout-grid"
            :class="{ 'bg-primary': viewType === 'grid' }"
            @click="viewType = 'grid'"
          />
        </VBtnToggle>
      </VCol>
      <VCol
        cols="12"
        md="5"
      >
        <div class="d-flex justify-end mb-5">
          <VBtn
            prepend-icon="tabler-plus"
            @click="() => $router.push(`/projects/${projectUuid}/tasks/add`)"
          >
            Add New
          </VBtn>
        </div>
      </VCol>
    </VRow>

    <!-- Skeleton Loader -->
    <div v-if="isMembersLoading == 1">
      <VRow
        v-if="viewType === 'list'"
        class="mb-4"
      >
        <TeamListSkeleton />
      </VRow>

      <!-- Grid View Skeleton Loader -->
      <VRow v-else>
        <TeamGridSkeleton />
      </VRow>
    </div>

    <div v-else>
      <VRow
        v-if="viewType === 'list'"
        class="mb-4"
      >
        <VCol
          v-for="(data, index) in projectProgress.lists"
          :key="index"
          cols="12"
        > 
          <VCard class="d-flex ps-4 py-1">
            <VCol cols="3">
              <div class="d-flex align-center">
                <div class="d-flex flex-column ms-3">
                  <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ data.name }}</span>
                </div>
              </div>
            </VCol>
            <VCol cols="3">
              <span>
                {{ data.totalTasks > 0 ? 'Tasks' : 'Task' }} ({{ data.totalTasks }})
              </span>
            </VCol>
            <VCol cols="3">
              <VChip
                variant="outlined"
                :color="data.status === 'done' ? 'success' : 'warning'"
                size="small"
              >
                <span>{{ data.progress }}%</span>
              </VChip>
            </VCol>
            <VCol cols="3">
              <VChip
                variant="outlined"
                :color="data.status === 'done' ? 'success' : 'warning'"
                size="small"
              >
                <span>{{ capitalizeFirstLetter(data.status) }}</span>
              </VChip>
            </VCol>
          </VCard>
        </VCol>
      </VRow>

      <!-- Grid View -->
      <VRow v-else>
        <VCol
          v-for="(data, index) in projectProgress.lists"
          :key="index"
          cols="12"
          md="4"
        >
          <VCard>
            <div class="image-container">
              <VImg :src="Page2" />
            </div>
            <VCardText class="position-relative">
              <!-- User Avatar -->
              <VAvatar
                size="75"
                class="avatar-center"
                color="primary"
              >
                <span>{{ avatarText('Lists') }}</span>
              </VAvatar>
    
              <VRow class="mt-5">
                <VCol cols="8">
                  <div class="d-flex align-center mt-1 ms-1">
                    <div class="d-flex flex-column ms-3">
                      <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ data.name }}</span>
                      <span class="text-sm text-truncate mb-0">
                        {{ data.totalTasks }} {{ data.totalTasks > 0 ? 'Tasks' : 'Task' }}
                      </span>
                      <VChip
                        variant="outlined"
                        :color="data.status === 'done' ? 'success' : 'warning'"
                        size="small"
                        class="mt-3 position-absolute user_role"
                      >
                        <span>{{ capitalizeFirstLetter(data.status) }}</span>
                      </VChip>
                    </div>
                  </div>
                </VCol>
              </VRow>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>
    </div>
  </div>
</template>

<script setup lang="js">
import TeamListSkeleton from '@/pages/projects/_partials/team-list-skeleton.vue'
import TeamGridSkeleton from '@/pages/projects/_partials/team-grid-skeleton.vue'
import avatar1 from '@images/avatars/avatar-1.png'
import Page2 from '../../../images/pages/2.png'
import girlWithLaptop from '@images/illustrations/PM.png'
import { useProjectStore } from "@/store/projects"
import { useRoute } from 'vue-router'

const projectStore = useProjectStore()
const $route = useRoute()
const projectUuid = $route.params.id
const viewType = ref('list')

onBeforeMount(async () => {
  await getProjectProgress()
})

const getProjectProgress = async () => {
  await projectStore.getProgress(projectUuid)
}

const capitalizeFirstLetter = string => {
  return string.charAt(0).toUpperCase() + string.slice(1)
}

const projectProgress = computed(() => {
  return projectStore.getProjectProgress
})
</script>

<style scoped>
.d-toggle{
  border: unset !important;
  padding: 0 !important;
  align-items: unset !important;
  block-size: unset !important;
}

.avatar-center {
  position: absolute;
  border: 3px solid rgb(var(--v-theme-surface));
  inset-block-start: -2rem;
  inset-inline-start: 1rem;
}

.image-container {
  position: relative;
}

.dots-icon {
  position: absolute;
  top: 8px;
  right: 8px;
}

.user_role{
  right: 10px;
}
</style>

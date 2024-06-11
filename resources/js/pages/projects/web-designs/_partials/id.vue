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
  <VRow>
    <VCol cols="12">
      <VProgressLinear
        v-model="projectProgress.overallProgress"
        color="success"
        height="20"
      >
        <template #default="{ value }">
          <span :class="Math.ceil(value) < 50 ? 'text-primary' : 'text-white'">
            {{ Math.ceil(value) }}%
          </span>
        </template>
      </VProgressLinear>
    </VCol>
  </VRow>
  <VRow>
    <VCol cols="9">
      <div class="horizontal-scroll">
        <VRow style="flex-wrap: nowrap;">
          <VCol
            v-for="(data, index) in projectProgress.lists"
            :key="index"
            cols="3"
            style="flex: 0 0 auto; max-width: 33.333333%;"
          >
            <VCard
              class="logistics-card-statistics cursor-pointer p-0"
              @click="() => $router.push(`/projects/${projectUuid}/tasks/add`)"
            >
              <VCardText style="padding: 20px !important;">
                <div class="mb-2 text-center">
                  <h5 class="text-h6 mb-3 font-weight-medium">
                    {{ data.name }}
                  </h5>
                  <VAvatar
                    v-if="data.status == 'completed'"
                    color="success"
                    size="x-large"
                  >
                    <VIcon
                      icon="tabler-check"
                      size="22"
                    />
                  </VAvatar>
                  <VAvatar
                    v-else-if="data.status == 'inprogress'"
                    color="warning"
                    size="x-large"
                  >
                    <span class="text-sm">{{ data.progress }}%</span>
                  </VAvatar>
                  <VAvatar
                    v-else-if="data.status == 'pending'"
                    color="info"
                    size="x-large"
                  >
                    <VIcon
                      icon="tabler-clock"
                      size="22"
                    />
                  </VAvatar>
                </div>
                <div class="text-center text-h6 font-weight-medium">
                  <small v-if="data.status == 'completed'">
                    Completed
                  </small>
                  <small v-else-if="data.status == 'inprogress'">
                    Inprogress
                  </small>
                  <small v-else>
                    Pending
                  </small>
                </div>
              </VCardText>
            </VCard>
          </VCol>
          <VCol
            v-if="projectProgress?.lists?.length < 3 ? true : false"
            :cols="projectProgress?.lists?.length == 1 ? 6 : 3"
            :style="`flex: 0 0 auto; max-width: ${projectProgress?.lists?.length == 1 ? '65%' : '33.333333%'};`"
          >
            <VCard
              class="logistics-card-statistics cursor-pointer p-0"
              outlined
            >
              <VCardText class="text-center py-5 px-3">
                <div class="mb-6">
                  <h5
                    class="text-h5 mb-4 font-weight-medium"
                    style="color: #616161;"
                  >
                    Other List Appear Here
                  </h5>
                  <span v-html="otherListImg" />
                </div>
              </VCardText>
            </VCard>
          </VCol>
        </VRow>
      </div>
    </VCol>

    <VCol cols="3">
      <VCard class="logistics-card-statistics cursor-pointer p-1">
        <VCardText>
          <div class="text-body-1 text-center">
            Project Launch Date
          </div>
          <div class="mb-2 mt-2 text-center">
            <h5 class="text-h3 mb-3 font-weight-medium">
              {{ projectProgress.launchingDays }} Days
            </h5>
          </div>
          <div class="text-center text-h6 mb-2 font-weight-medium">
            <small>
              {{ projectProgress.launchingDate }}
            </small>
          </div>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
  <VRow>
    <VCol cols="6">
      <VCard>
        <VCardItem title="Inbox (14)">
          <template #append>
            <MoreBtn />
          </template>
        </VCardItem>
        <VCardText>
          <VList class="card-list">
            <VListItem
              v-for="instructor in [
                { active: 'success', name: 'Jordan Stevenson', profession: 'Business Intelligence', totalCourses: 3, avatar: avatar1 },
                { active: 'warning', name: 'Bentlee Emblin', profession: 'Digital Marketing', totalCourses: 2, avatar: avatar1 },
                { active: 'warning', name: 'Benedetto Rossiter', profession: 'UI/UX Design', totalCourses: 1, avatar: avatar1 },
                { active: 'secondary', name: 'Beverlie Krabbe', profession: 'Vue', totalCourses: 8, avatar: avatar1 },
              ]"
              :key="instructor.name"
            >
              <template #prepend>
                <VBadge
                  dot
                  bordered
                  :color="instructor.active"
                  location="bottom end"
                >
                  <VAvatar
                    size="34"
                    :image="instructor.avatar"
                  />
                </VBadge>
              </template>
              <VListItemTitle class="font-weight-medium">
                {{ instructor.name }}
              </VListItemTitle>
              <VListItemSubtitle class="text-disabled">
                {{ instructor.profession }}
              </VListItemSubtitle>

              <template #append>
                <span class="p-0">
                  <VBadge
                    color="error"
                    :content="instructor.totalCourses"
                  />
                </span>
              </template>
            </VListItem>
            <VBtn
              size="small"
              rounded="pill"
              color="primary"
            >
              Write Message
            </VBtn>
          </VList>
        </VCardText>
      </VCard>
    </VCol>
    <VCol cols="6">
      <VCard
        class="pb-5"
        style="height: 388px;"
      >
        <VCardItem :title="`Your Tasks (${projectProgress.totalTasks})`">
          <template #append>
            <MoreBtn />
          </template>
        </VCardItem>
        <VCardText style="overflow-y: auto; max-height: calc(100% - 56px);">
          <VList class="card-list">
            <VListItem
              v-for="(data, index) in projectProgress.lists"
              :key="index"
            >
              <template #prepend>
                <VProgressCircular
                  v-model="data.progress"
                  size="54"
                  class="me-4"
                  :color="getColor(data.status)"
                >
                  <span class="text-body-1 text-high-emphasis font-weight-medium">
                    {{ data.progress }}%
                  </span>
                </VProgressCircular>
              </template>
              <VListItemTitle class="font-weight-medium mb-1">
                {{ data.name }}
              </VListItemTitle>

              <VListItemSubtitle>{{ data.totalTasks }} Tasks</VListItemSubtitle>
              <template #append>
                <VBtn
                  variant="tonal"
                  color="default"
                  class="rounded-sm"
                  size="30"
                  @click="() => $router.push(`/projects/${projectUuid}/tasks/add`)"
                >
                  <VIcon
                    icon="tabler-chevron-right"
                    class="flip-in-rtl"
                  />
                </VBtn>
              </template>
            </VListItem>

            <!-- Section for fewer than 3 tasks -->
            <VListItem
              v-if="projectProgress?.lists?.length < 3"
              class="d-flex justify-center align-center"
            >
              <VCard
                outlined
                class="pa-3 d-flex align-center"
              >
                <VCardText class="d-flex align-center">
                  <VIcon
                    size="40"
                    color="info"
                    icon="tabler-info-circle"
                    class="me-4"
                  />
                  <div>
                    <h5
                      class="text-h5 font-weight-medium mb-2"
                      style="color: #616161;"
                    >
                      Other List Appear Here
                    </h5>
                    <p class="mb-0">
                      Add more lists to see them here!
                    </p>
                  </div>
                </VCardText>
              </VCard>
            </VListItem>
          </VList>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
  <VRow>
    <VCol cols="6">
      <VCard>
        <VCardItem title="Upcoming Event">
          <template #append>
            <MoreBtn />
          </template>
        </VCardItem>
        <VCardText>
          <div>
            <div class="d-flex justify-space-between mx-12 my-8 flex-wrap">
              <div
                v-for="{ icon, title, value, color } in [{ icon: 'tabler-clock', value: '32 Minutes', title: 'Status Call', color: 'primary' }, { icon: 'tabler-check', value: '17 Mar, 2024', title: 'Approve Design', color: 'success' }]"
                :key="title"
                class="mx-7"
              >
                <div class="text-center">
                  <VIcon
                    :icon="icon"
                    size="60"
                    :color="color"
                  />
                </div>
                <div>
                  <h6 class="text-h5 text-high-emphasis">
                    {{ title }}
                  </h6>
                  <div class="text-sm text-center mt-1">
                    {{ value }}
                  </div>
                </div>
              </div>
            </div>
            <div class="text-center mb-1">
              <VBtn
                size="small"
                rounded="pill"
                color="primary"
              >
                Join the Event
              </VBtn>
            </div>
          </div>
        </VCardText>
      </VCard>
    </VCol>
    <VCol cols="6">
      <VCard>
        <VCardItem>
          <VCardTitle>Recent Activity</VCardTitle>

          <template #append>
            <div>
              <MoreBtn
                :menu-list="[
                  { title: 'Share timeline', value: 'Share timeline' },
                  { title: 'Suggest edits', value: 'Suggest edits' },
                  { title: 'Report bug', value: 'Report bug' },
                ]"
              />
            </div>
          </template>
        </VCardItem>

        <VCardText class="activity-card">
          <VTimeline
            density="compact"
            align="start"
            truncate-line="both"
            class="v-timeline-density-compact"
          >
            <VTimelineItem
              dot-color="warning"
              size="x-small"
            >
              <div class="d-flex justify-space-between align-center flex-wrap">
                <span class="app-timeline-title">
                  Client Meeting
                </span>
                <span class="app-timeline-meta">Today</span>
              </div>
              <p class="app-timeline-text mb-2">
                Project meeting with john @10:15am
              </p>
            </VTimelineItem>

            <VTimelineItem
              dot-color="primary"
              size="x-small"
            >
              <div class="d-flex justify-space-between align-center flex-wrap">
                <span class="app-timeline-title">
                  Create a new project for client
                </span>
                <span class="app-timeline-meta">2 Day Ago</span>
              </div>

              <p class="app-timeline-text mb-1">
                Add files to new design folder
              </p>
            </VTimelineItem>

            <VTimelineItem
              dot-color="primary"
              size="x-small"
            >
              <div class="d-flex justify-space-between align-center flex-wrap">
                <span class="app-timeline-title">
                  Shared 2 New Project Files
                </span>
                <span class="app-timeline-meta">6 Day Ago</span>
              </div>
              <p class="app-timeline-text mb-0">
                Sent by Mollie Dixon
              </p>
            </VTimelineItem>

            <VTimelineItem
              dot-color="primary"
              size="x-small"
            >
              <div class="d-flex justify-space-between align-center flex-wrap">
                <span class="app-timeline-title">
                  Project status updated
                </span>
                <span class="app-timeline-meta">10 Day Ago</span>
              </div>
              <p class="app-timeline-text mb-1">
                WooCommerce iOS App Completed
              </p>
            </VTimelineItem>
          </VTimeline>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
  <VRow>
    <VCol cols="6">
      <VCard>
        <VCardItem title="Meet with PM">
          <template #append>
            <MoreBtn />
          </template>
        </VCardItem>
        <VCardText>
          <VRow>
            <VCol cols="6">
              <div class="d-flex justify-center  align-start pb-0 px-3 pt-3 mb-3 bg-light-primary rounded">
                <VImg
                  :src="girlWithLaptop"
                  width="145"
                  height="140"
                />
              </div>
            </VCol>
            <VCol cols="6">
              <div>
                <div class="d-flex justify-space-between my-2 flex-wrap">
                  <div class="d-flex gap-x-3 align-center">
                    <div>
                      <h6 class="text-h5 text-high-emphasis">
                        Jennifer Lawrence
                      </h6>
                      <div class="text-sm text-high-emphasis">
                        Web Dev PM
                      </div>
                    </div>
                  </div>
                </div>
                <div class="my-2">
                  <VBtn
                    size="small"
                    rounded="pill"
                    color="primary"
                  >
                    Schedule Meeting
                  </VBtn>
                </div>
              </div>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>
    </VCol>
    <VCol cols="6">
      <VCard>
        <VCardItem title="Maching Services For Your Project">
          <template #append>
            <MoreBtn />
          </template>
        </VCardItem>
        <VCardText class="my-8 mx-12">
          <VRow>
            <VCol col="12">
              <div class="d-flex">
                <VBtn block>
                  SEO
                </VBtn>
              </div>
            </VCol>
          </VRow>
          <VRow>
            <VCol col="12">
              <div class="d-flex">
                <VBtn block>
                  Web Hosting
                </VBtn>
              </div>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<script setup lang="js">
import avatar1 from '@images/avatars/avatar-1.png'
import girlWithLaptop from '@images/illustrations/PM.png'
import otherListImg from '@images/darby/other_list.svg?raw'
import { useProjectStore } from "@/store/projects"
import { useRoute } from 'vue-router'
import sketch from '@images/icons/project-icons/sketch.png'

const projectStore = useProjectStore()
const $route = useRoute()
const projectUuid = $route.params.id

onBeforeMount(async () => {
  await getProjectProgress()
})

const getProjectProgress = async () => {
  await fetchProject()
  await projectStore.getProgress(projectUuid)
}

const fetchProject = async () => {
  try {
    await projectStore.show(projectUuid)
    isLoading.value = true
  } catch (error) {
    toast.error('Error fetching project:', error)
  }
  finally {
    isLoading.value = false
  }
}

const getColor = progress => {
  if (progress == 'completed') {
    return 'success'
  } else if (progress == 'inprogress') {
    return 'warning'
  } else {
    return 'info'
  }
}

const projectProgress = computed(() => {
  return projectStore.getProjectProgress
})

const project = computed(() =>{
  return projectStore.getProject
})
</script>

<style lang="scss" scoped>
@use "@core-scss/base/mixins" as mixins;
.logistics-card-statistics:hover {
  @include mixins.elevation(12);

  transition: all 0.1s ease-out;
}
.v-list{
    overflow: unset!important;
}
.activity-card {
    max-height: 208px;
    overflow-y: auto;
    margin-bottom: 25px;
}
.logistics-card-statistics{
  border-block-end: 3px solid rgba(var(--v-theme-success), var(--v-disabled-opacity));
}
.logistics-card-statistics:hover {
  border-bottom: 3px solid rgba(var(--v-theme-success));
}
.horizontal-scroll {
  overflow-x: auto;
  overflow-y: hidden;
  white-space: nowrap;
}

.horizontal-scroll::-webkit-scrollbar {
  height: 10px; /* Set height of the scrollbar */
}

.horizontal-scroll::-webkit-scrollbar-track {
  background: #f1f1f1; /* Set track color */
}

.horizontal-scroll::-webkit-scrollbar-thumb {
  background: #888; /* Set thumb color */
  border-radius: 5px; /* Set thumb border radius */
}

.horizontal-scroll::-webkit-scrollbar-thumb:hover {
  background: #555; /* Set thumb color on hover */
}

.v-col {
  margin-bottom: 10px; /* Add margin bottom to create gap from scrollbar */
}
</style>

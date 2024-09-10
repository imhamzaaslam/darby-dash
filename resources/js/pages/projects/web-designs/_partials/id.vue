<template>
  <Loader v-if="loadStatus === 1" />
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
              @click="() => $router.push({ path: `/projects/${projectUuid}/tasks/add`, query: { expanded: index, type: data.uuid } })"
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
                    color="primary"
                    size="x-large"
                  >
                    <span class="text-sm">{{ data.progress }}%</span>
                  </VAvatar>
                  <VAvatar
                    v-else-if="data.status == 'pending'"
                    color="secondary"
                    size="x-large"
                  >
                    <VIcon
                      icon="tabler-clock"
                      size="22"
                    />
                  </VAvatar>
                </div>
                <div :class="`text-center text-h6 font-weight-medium text-${getColor(data.status)}`">
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
                    color="secondary"
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
      <VCard
        style="height: 163px;"
        class="logistics-card-statistics cursor-pointer p-1"
      >
        <VCardText>
          <div class="text-body-1 text-center d-flex align-center mb-4">
            <span class="text-primary font-weight-bold me-1">Start Date:</span>{{ formatDate(project?.created_at) }}
          </div>
          <div class="text-body-1 text-center d-flex align-center mb-4">
            <span class="text-primary font-weight-bold me-1">Estimated Hours:</span>{{ project?.total_estimated_hours }}
          </div>
          <div class="text-body-1 text-center d-flex align-center mb-4">
            <span class="text-primary font-weight-bold me-1">Due Date:</span>{{ formatDate(projectProgress.launchingDate) }} <small class="ms-1 font-weight-bold">({{ projectProgress.launchingDays }} {{ projectProgress.launchingDays > 1 ? 'days' : 'day' }})</small>
          </div>
          <!-- <div class="text-body-1 text-center d-flex align-center justify-center">
            Project Launch Date
            <VIcon
              class="tabler-info-circle ms-1"
              color="primary"
            />
            <VTooltip
              activator="parent"
              location="top"
            >
              <span class="text-xs">Based on pending & inprogress tasks</span>
            </VTooltip>
          </div>
          <div class="mb-2 mt-2 text-center">
            <h5 class="text-h3 mb-3 font-weight-medium">
              {{ projectProgress.launchingTime }}
            </h5>
          </div>
          <div class="text-center text-h6 mb-2 font-weight-medium">
            <small>
              {{ projectProgress.launchingDate }}
            </small>
          </div> -->
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
                { active: 'primary', name: 'Bentlee Emblin', profession: 'Digital Marketing', totalCourses: 2, avatar: avatar1 },
                { active: 'primary', name: 'Benedetto Rossiter', profession: 'UI/UX Design', totalCourses: 1, avatar: avatar1 },
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
                    color="primary"
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
              <VListItemTitle
                class="font-weight-medium mb-1 cursor-pointer text-primary"
                @click="() => $router.push({ path: `/projects/${projectUuid}/tasks/add`, query: { expanded: index, type: data.uuid } })"
              >
                {{ data.name }}
              </VListItemTitle>

              <VListItemSubtitle>{{ data.totalTasks }} Tasks</VListItemSubtitle>
              <template #append>
                <VBtn
                  variant="tonal"
                  color="primary"
                  class="rounded-sm"
                  size="30"
                  @click="() => $router.push({ path: `/projects/${projectUuid}/tasks/add`, query: { expanded: index, type: data.uuid } })"
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
                    color="primary"
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
      <VCard style="height: 320px;">
        <VCardItem>
          <VCardTitle>Upcoming Event</VCardTitle>

          <template #append>
            <div>
              <MoreBtn />
            </div>
          </template>
        </VCardItem>

        <VCardText class="activity-card">
          <VTimeline
            v-if="project?.upcoming_events.length > 0"
            density="compact"
            align="start"
            truncate-line="both"
            class="v-timeline-density-compact"
          >
            <VTimelineItem
              v-for="(event, index) in project?.upcoming_events"
              :key="index"
              dot-color="primary"
              size="x-small"
            >
              <div class="d-flex justify-space-between align-center flex-wrap">
                <span class="app-timeline-title">
                  {{ event.name }}
                </span>
                <span class="app-timeline-meta">{{ getDaysLeft(event.start_date) }} Days Left</span>
              </div>
              <p class="app-timeline-text mb-2">
                {{ formatDate(event.start_date) }}
              </p>
            </VTimelineItem>
          </VTimeline>
          <div
            v-else
            class="text-center py-10"
          >
            <p class="text-body-2 text-high-emphasis">
              No upcoming events for this project.
            </p>
            <RouterLink :to="`/projects/${projectUuid}/calendar`">
              <VBtn
                size="small"
                rounded="pill"
                color="primary"
              >
                Add Event
              </VBtn>
            </RouterLink>
          </div>
          <div
            v-if="project?.upcoming_events.length < 2"
            class="text-center py-10"
          >
            <RouterLink :to="`/projects/${projectUuid}/calendar`">
              <VBtn
                size="small"
                rounded="pill"
                color="primary"
              >
                Add More Event
              </VBtn>
            </RouterLink>
          </div>
        </VCardText>
      </VCard>
    </VCol>
    <!-- <VCol cols="6">
      <VCard style="height: 320px;">
        <VCardItem title="Upcoming Event">
          <template #append>
            <MoreBtn />
          </template>
        </VCardItem>
        <VCardText>
          <div>
            <div
              v-if="project?.upcoming_events.length > 0"
              class="d-flex mx-12 my-8 flex-wrap"
              :class="project?.upcoming_events.length > 1 ? 'justify-space-between' : 'justify-center'"
            >
              <div
                v-for="(event, index) in project?.upcoming_events"
                :key="index"
                class="mx-7"
              >
                <div class="text-center">
                  <VIcon
                    icon="tabler-clock"
                    size="55"
                    color="primary"
                  />
                </div>
                <div>
                  <h6 class="text-h5 text-high-emphasis">
                    {{ event.name }}
                  </h6>
                  <div class="text-sm text-center mt-1">
                    {{ formatDate(event.start_date) }}
                  </div>
                </div>
              </div>
            </div>
            <div
              v-else
              class="text-center py-10"
            >
              <p class="text-body-2 text-high-emphasis">
                No upcoming events for this project.
              </p>
              <RouterLink :to="`/projects/${projectUuid}/calendar`">
                <VBtn
                  size="small"
                  rounded="pill"
                  color="primary"
                >
                  Add Event
                </VBtn>
              </RouterLink>
            </div>
          </div>
        </VCardText>
      </VCard>
    </VCol> -->
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
      <VCard style="height: 295px;">
        <!-- // check if project_manager is not null/ -->
        <template v-if="project?.project_manager">
          <VCardItem title="Meet with PM">
            <template #append>
              <MoreBtn />
            </template>
          </VCardItem>
          <VCardText>
            <VRow>
              <VCol cols="6">
                <div
                  v-if="project?.project_manager?.info?.avatar"
                  class="d-flex justify-center  align-start pb-0 px-3 pt-3 mb-3"
                >
                  <VImg
                    :src="getImageUrl(project?.project_manager?.info?.avatar.path)"
                    width="145"
                    height="140"
                  />
                </div>
                <div
                  v-else
                  class="d-flex justify-center align-center pb-0 px-3 bg-light-primary rounded"
                  style="height: 140px;"
                >
                  <h6 class="text-h1 text-primary">
                    {{ avatarText(project?.project_manager?.name_first + ' . ' + project?.project_manager?.name_last) }}
                  </h6>
                </div>
              </VCol>

              <VCol cols="6">
                <div>
                  <div class="d-flex justify-space-between my-2 flex-wrap">
                    <div class="d-flex gap-x-3 align-center">
                      <div>
                        <h6 class="text-h5 text-high-emphasis">
                          {{ project?.project_manager?.name_first + ' ' + project?.project_manager?.name_last }}
                        </h6>
                        <div class="text-sm text-high-emphasis">
                          {{ project?.project_manager?.email }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="my-5">
                    <RouterLink :to="`/projects/${projectUuid}/calendar`">
                      <VBtn
                        size="small"
                        rounded="pill"
                        color="primary"
                      >
                        Schedule Meeting
                      </VBtn>
                    </RouterLink>
                  </div>
                </div>
              </VCol>
            </VRow>
          </VCardText>
        </template>
        <!-- // else show the below -->
        <template v-else>
          <VCardItem title="No Project Manager Assigned">
            <template #append>
              <MoreBtn />
            </template>
          </VCardItem>
          <VCardText>
            <div class="text-center py-10">
              <p class="text-body-2 text-high-emphasis">
                Currently, there is no project manager assigned to this project.
              </p>
              <RouterLink to="/projects/web-designs">
                <VBtn
                  size="small"
                  rounded="pill"
                  color="primary"
                >
                  Assign Project Manager
                </VBtn>
              </RouterLink>
            </div>
          </VCardText>
        </template>
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
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import moment from 'moment'
import Loader from "@/components/Loader.vue"
import avatar1 from '@images/avatars/avatar-1.png'
import girlWithLaptop from '@images/illustrations/PM.png'
import otherListImg from '@images/darby/other_list.svg?raw'
import { useProjectStore } from "@/store/projects"
import { useRoute } from 'vue-router'
import sketch from '@images/icons/project-icons/sketch.png'

onBeforeMount(async () => {
  await getProjectProgress()
})

const projectStore = useProjectStore()
const $route = useRoute()
const projectUuid = $route.params.id

const getProjectProgress = async () => {
  await fetchProject()
  await projectStore.getProgress(projectUuid)
}

const fetchProject = async () => {
  try {
    await projectStore.show(projectUuid)
  } catch (error) {
    toast.error('Error fetching project:', error)
  }
}

const getColor = progress => {
  if (progress == 'completed') {
    return 'success'
  } else if (progress == 'inprogress') {
    return 'primary'
  } else {
    return 'secondary'
  }
}

const formatDate = date => moment(date).format('MM/DD/YYYY')

const getDaysLeft = date => {
  const today = moment()
  const eventDate = moment(date)
  
  return eventDate.diff(today, 'days')
}

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

const projectProgress = computed(() => {
  return projectStore.getProjectProgress
})

const project = computed(() =>{
  return projectStore.getProject
})

const loadStatus = computed(() => {
  return projectStore.getLoadStatus
})

watch(project, () => {
  useHead({ title: `${layoutConfig.app.title} | ${project?.value?.title} - Overview` })
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
  border-block-end: 3px solid rgba(var(--v-theme-primary), var(--v-disabled-opacity));
}
.logistics-card-statistics:hover {
  border-bottom: 3px solid rgba(var(--v-theme-primary));
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

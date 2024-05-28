<template>
  <VRow>
    <VCol cols="12">
      <VProgressLinear
        v-model="skill"
        color="success"
        height="20"
      >
        <template #default="{ value }">
          <span class="text-white">{{ Math.ceil(value) }}%</span>
        </template>
      </VProgressLinear>
    </VCol>
  </VRow>
  <VRow>
    <VCol cols="9">
      <VRow>
        <VCol
          v-for="(data, index) in phaseData"
          :key="index"
          cols="3"
        >
          <VCard
            class="logistics-card-statistics cursor-pointer p-0"
            :style="data.isHover ? { 'border-block-end': `3px solid rgba(var(--v-theme-${data.color}))` } : { 'border-block-end': `3px solid rgba(var(--v-theme-${data.color}), var(--v-disabled-opacity))` }"
            @mouseenter="data.isHover = true"
            @mouseleave="data.isHover = false"
          >
            <VCardText style="padding:10px!important;">
              <div class="text-body-1 text-center">
                {{ data.phase }}
              </div>
              <div class="mb-2 text-center">
                <h5 class="text-h6 mb-3 font-weight-medium">
                  {{ data.title }}
                </h5>
                <VAvatar
                  v-if="data.status == 'DONE'"
                  color="success"
                  size="x-large"
                >
                  <VIcon
                    icon="tabler-check"
                    size="22"
                  />
                </VAvatar>
                <VAvatar
                  v-else-if="data.status == 'Inprogress'"
                  color="warning"
                  size="x-large"
                >
                  <span class="text-sm">67%</span>
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
                <small v-if="data.status == 'DONE'">
                  -
                </small>
                <small v-else-if="data.status == 'Inprogress'">
                  Inprogress
                </small>
                <small v-else>
                  June 5, 2024
                </small>
              </div>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>
    </VCol>
    <VCol cols="3">
      <VCard class="logistics-card-statistics cursor-pointer p-1">
        <VCardText>
          <div class="text-body-1 text-center">
            Project Launch Date
          </div>
          <div class="mb-2 mt-2 text-center">
            <h5 class="text-h3 mb-3 font-weight-medium">
              170 Days
            </h5>
          </div>
          <div class="text-center text-h6 mb-2 font-weight-medium">
            <small>
              Friday June 24, 2024
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
      <VCard>
        <VCardItem title="Your Tasks (390)">
          <template #append>
            <MoreBtn />
          </template>
        </VCardItem>
        <VCardText>
          <VList class="card-list">
            <VListItem
              v-for="assignment in assignmentData"
              :key="assignment.title"
            >
              <template #prepend>
                <VProgressCircular
                  v-model="assignment.progress"
                  :size="54"
                  class="me-4"
                  :color="assignment.color"
                >
                  <span class="text-body-1 text-high-emphasis font-weight-medium">
                    {{ assignment.progress }}%
                  </span>
                </VProgressCircular>
              </template>
              <VListItemTitle class="font-weight-medium mb-3">
                {{ assignment.title }}
              </VListItemTitle>

              <VListItemSubtitle>{{ assignment.tasks }} Tasks</VListItemSubtitle>
              <template #append>
                <VBtn
                  variant="tonal"
                  color="default"
                  class="rounded-sm"
                  size="30"
                >
                  <VIcon
                    icon="tabler-chevron-right"
                    class="flip-in-rtl"
                  />
                </VBtn>
              </template>
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

const skill = ref(72)

const phaseData = [
  { color: "success", title: 'Wireframe', phase: 'Phase 1', status: "DONE", isHover: false },
  { color: "success", title: 'Design', phase: 'Phase 2', status: "DONE", isHover: false },
  { color: "warning", title: 'Development', phase: 'Phase 3', status: "Inprogress", isHover: false },
  { color: "info", title: 'Q&A', phase: 'Phase 4', status: "pending", isHover: false },
]

const assignmentData = [
  { title: 'Wireframe', tasks: 120, progress: 100, color: 'success' },
  { title: 'Design', tasks: 32, progress: 100, color: 'success' },
  { title: 'Development', tasks: 182, progress: 67, color: 'warning' },
  { title: 'Q&A', tasks: 56, progress: 0, color: 'info' },
]
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
</style>

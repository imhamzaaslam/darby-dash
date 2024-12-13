<template>
  <Loader v-if="loadStatus === 1" />
  <VRow>
    <VCol
      cols="12"
      md="6"
    >
      <div class="d-flex justify-between align-center">
        <div class="d-flex align-center">
          <VAvatar
            icon="tabler-cube"
            size="36"
            class="me-2"
            color="primary"
            variant="tonal"
          />
          <h3 class="text-primary">
            {{ project?.title }}
            <span class="d-block text-xs text-secondary">{{ project?.project_type }}</span>
          </h3>
        </div>
      </div>
    </VCol>
    <VCol
      cols="12"
      md="6"
    >
      <div class="d-flex justify-end align-center">
        <VCheckbox
          v-if="projectProgress.overallProgress === 100 && authStore.isAdmin"
          v-model="isCompleted"
          label="Mark as Completed"
          class="ms-2 me-2"
          :true-value="1"
          :false-value="0"
          @change="handleProjectCompleteSwitchChange(project?.title)"
        />
        <VBtn
          v-if="projectProgress.overallProgress === 100 && showAwardBucksBtn && authStore.isAdmin"
          size="x-small"
          color="primary"
          rounded="pill"
          @click="openAwardBucksDialogue"
        >
          <VIcon
            start
            icon="tabler-award-filled"
          />
          Award Bucks to PM
        </VBtn>
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
    <VCol
      v-for="(data, index) in projectProgress?.lists?.slice(0, 3)"
      :key="index"
      cols="12"
      md="3"
      sm="12"
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
      v-for="index in 3 - projectProgress?.lists?.length"
      v-if="projectProgress?.lists?.length < 3 ? true : false"
      :key="index"
      cols="12"
      md="3"
      sm="12"
    >
      <VCard
        style="height: 163px;"
        class="logistics-card-statistics cursor-pointer p-0"
        outlined
        @click.stop="activeListIndex === index ? null : (activeListIndex = index)"
      >
        <VCardText
          v-if="activeListIndex !== index"
          class="text-center py-5 px-3"
        >
          <h5 class="text-h6 mb-4 font-weight-medium">
            Add New List
          </h5>
          <VIcon
            icon="tabler-circle-plus"
            size="55"
            color="primary"
          />
        </VCardText>

        <!-- Text Field with Save/Cancel Buttons -->
        <VCardText v-else>
          <VTextField
            v-model="newListName"
            label="List Name"
            variant="outlined"
            autofocus
            @keydown.enter="saveList"
          />
          <div class="d-flex justify-end mt-4">
            <VBtn
              color="primary"
              class="me-2"
              size="x-small"
              :disabled="getListLoadStatus === 1"
              @click.stop="saveList"
            >
              <span v-if="getListLoadStatus === 1">
                <VProgressCircular
                  :size="16"
                  width="3"
                  indeterminate
                />
                Loading...
              </span>
              <span v-else>
                Save
              </span>
            </VBtn>
            <VBtn
              color="secondary"
              size="x-small"
              outlined
              @click.stop="resetListPlaceholderForm"
            >
              Cancel
            </VBtn>
          </div>
        </VCardText>
      </VCard>
    </VCol>
    <VCol
      cols="12"
      md="3"
      sm="12"
      class="pb-0"
    >
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
            <span class="text-primary font-weight-bold me-1">Due Date:</span>{{ projectProgress.launchingDate == 'Today' ? '' : formatDate(projectProgress.launchingDate) }} <small class="ms-1 font-weight-bold">({{ projectProgress.launchingDays }} {{ projectProgress.launchingDays > 1 ? 'days' : 'day' }})</small>
          </div>
        </VCardText>
      </VCard>
    </VCol>
  </VRow> 
  <VRow>
    <VCol
      cols="12"
      md="6"
    >
      <VCard style="height: 388px; position: relative;">
        <VCardItem :title="'Inbox (' + (project.project_members ? project.project_members?.filter(member => member.id != userDetails.id).length : 0) + ')'">
          <template #append>
            <div>
              <MoreBtn
                :menu-list="[
                  { title: 'View All', value: 'view-all', to: { name: 'chat', params: { id: projectUuid } } },
                ]"
              />
            </div>
          </template>
        </VCardItem>
        <VCardText
          class="inbox-card"
          style="max-height: 250px!important;"
        >
          <VList class="card-list">
            <VListItem
              v-for="inbox in project.project_members?.filter(member => member.id != userDetails.id)"
              :key="inbox.id"
            >
              <template #prepend>
                <VBadge
                  dot
                  bordered
                  :color="inbox.is_online ? 'success' : 'warning'"
                  location="bottom end"
                >
                  <RouterLink :to="`/projects/${projectUuid}/chat?inbox=${inbox.uuid}`">
                    <VAvatar
                      v-if="inbox?.info"
                      color="primary"
                      :image="inbox?.info?.avatar ? getImageUrl(inbox?.info?.avatar.path) : undefined"
                      :variant="inbox?.info?.avatar ? undefined : 'tonal'"
                      size="38"
                    >
                      <span v-if="!inbox?.info?.avatar">{{ avatarText(inbox.name_first + ' ' + inbox.name_last) }}</span>
                    </VAvatar>
                  </RouterLink>
                </VBadge>
              </template>
              <VListItemTitle class="font-weight-medium">
                <RouterLink :to="`/projects/${projectUuid}/chat?inbox=${inbox.uuid}`">
                  {{ inbox.name_first + ' ' + inbox.name_last }}
                </RouterLink>
              </VListItemTitle>
              <VListItemSubtitle class="text-body-2 text-secondary">
                {{ inbox.role }}
              </VListItemSubtitle>

              <template #append>
                <span class="p-0">
                  <RouterLink :to="`/projects/${projectUuid}/chat?inbox=${inbox.uuid}`">
                    <VBadge
                      color="primary"
                      :content="inbox.unseen_messages"
                    />
                  </RouterLink>
                </span>
              </template>
            </VListItem>
          </VList>
        </VCardText>
        <div style="position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%); width: 100%; text-align: center;">
          <VBtn
            size="small"
            rounded="pill"
            color="primary"
            :to="`/projects/${projectUuid}/chat`"
          >
            Write Message
          </VBtn>
        </div>
      </VCard>
    </VCol>
    <VCol
      cols="12"
      md="6"
    >
      <VCard
        class="pb-5"
        style="height: 388px;"
      >
        <VCardItem :title="`Upcoming Tasks (${upcomingTasks ? upcomingTasks.length : 0})`">
          <template #append>
            <MoreBtn
              :menu-list="[
                { title: 'View All', value: 'view-all', to: { name: 'add-project-tasks', params: { id: projectUuid } } },
              ]"
            />
          </template>
        </VCardItem>
        <VCardText style="overflow-y: auto; max-height: calc(100% - 56px);">
          <div
            v-if="upcomingTasks ? upcomingTasks.length == 0 : 0"
            class="text-center py-10"
          >
            <p class="text-body-2 text-high-emphasis">
              No upcoming task found.
            </p>
            <RouterLink :to="`/projects/${projectUuid}/tasks/add`">
              <VBtn
                size="small"
                rounded="pill"
                color="primary"
              >
                Add Task
              </VBtn>
            </RouterLink>
          </div>
          <VList
            v-else
            class="card-list upcoming-task-list"
          >
            <VListItem
              v-for="(task, index) in upcomingTasks"
              :key="index"
              class="upcoming-task-item"
            >
              <VListItemTitle
                class="font-weight-medium me-4 mb-1 text-truncate"
                style="max-width: 300px;"
              >
                <VAvatar
                  icon="tabler-playstation-circle"
                  size="20"
                  class="me-1"
                  color="primary"
                  variant="tonal"
                />
                {{ task.name }}
              </VListItemTitle>
              <VListItemSubtitle class="me-4">
                <div class="d-flex align-center gap-2">
                  <span 
                    class="text-body-2 upcoming-task-list-name text-secondary"
                    @click="() => $router.push({ path: `/projects/${projectUuid}/tasks/add`, query: { type: task.list_uuid } })"
                  >
                    <VIcon
                      size="18"
                      icon="tabler-list"
                      color="primary"
                    />
                    {{ task.list_name }}
                  </span>
                  <span
                    v-if="task.est_time"
                    class="text-body-2 text-secondary"
                  >
                    <VIcon
                      size="18"
                      icon="tabler-alarm"
                      color="primary"
                    />
                    {{ task.est_time }}
                  </span>
                  <!-- Assignees -->
                  <div class="d-flex align-center v-avatar-group demo-avatar-group">
                    <div
                      v-for="(user, index) in task.assignees.slice(0, 2)"
                      :key="index"
                      class="me-1"
                    >
                      <VAvatar :size="30">
                        <VAvatar
                          size="25"
                          class="text-white bg-primary"
                          variant="tonal"
                        >
                          <small>{{ avatarText(user.name_first + ' ' + user.name_last) }}</small>
                        </VAvatar>
                        <VTooltip
                          activator="parent"
                          location="top"
                        >
                          <span>{{ user.name_first + ' ' + user.name_last }}</span>
                        </VTooltip>
                      </VAvatar>
                    </div>
                    <VAvatar
                      v-if="task.assignees.length > 2"
                      :size="30"
                      class="me-1"
                    >
                      <VAvatar
                        size="25"
                        class="text-white bg-primary"
                        variant="tonal"
                      >
                        <small>+{{ task.assignees.length - 2 }}</small>
                      </VAvatar>
                    </VAvatar>
                  </div>
                </div>
              </VListItemSubtitle>
              <template #append>
                <div class="d-flex align-center gap-x-4">
                  <VChip
                    v-if="task.due_date"
                    label
                    size="small"
                    :color="isDueDateOverdue(task.due_date) ? 'error' : isDueDateToday(task.due_date) ? 'warning' : 'primary'"
                    class="text-body-2"
                  >
                    <small v-if="isDueDateToday(task.due_date)">Due Today</small>
                    <small v-else-if="isDueDateOverdue(task.due_date)">Overdue on {{ formatDate(task.due_date) }}</small>
                    <span v-else>
                      {{ formatDate(task.due_date) }}
                    </span>
                  </VChip>
                  <VChip
                    v-else
                    label
                    size="small"
                    :color="task.status.color"
                    class="text-body-2"
                    :class="task.status.color"
                  >
                    <span>
                      {{ task.status.name }}
                    </span>
                  </VChip>
                </div>
              </template>
            </VListItem>
          </VList>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
  <VRow>
    <VCol
      cols="12"
      md="6"
    >
      <VCard style="height: 320px;">
        <VCardItem>
          <VCardTitle>Upcoming Event</VCardTitle>

          <template #append>
            <div>
              <MoreBtn
                :menu-list="[
                  { title: 'View All', value: 'view-all', to: { name: 'calendar', params: { id: projectUuid } } },
                ]"
              />
            </div>
          </template>
        </VCardItem>

        <VCardText>
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
            v-if="project?.upcoming_events.length < 2 && project?.upcoming_events.length > 0"
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
    <!--
      <VCol cols="6">
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
      </VCol>
    -->
    <VCol
      cols="12"
      md="6"
    >
      <VCard style="height: 320px;">
        <VCardItem>
          <VCardTitle>Recent Activity</VCardTitle>

          <template #append>
            <div>
              <MoreBtn
                :menu-list="[
                  { title: 'View All', value: 'view-all', to: { name: 'view-project-activities', params: { id: projectUuid } } },
                ]"
              />
            </div>
          </template>
        </VCardItem>
        <VCardText class="activity-card">
          <template v-if="getActivities.length">
            <VTimeline
              v-for="(activity, index) in getActivities"
              :key="index"
              side="end"
              density="compact"
              align="start"
              truncate-line="both"
              class="v-timeline-density-compact"
            >
              <VTimelineItem
                dot-color="primary"
                size="x-small"
              >
                <div class="d-flex justify-space-between align-center flex-wrap">
                  <span class="app-timeline-title">
                    {{ truncateDescription(activity.title, 45) }}
                  </span>
                  <span class="app-timeline-meta">{{ activity.time }}</span>
                </div>
                <p class="text-body-2 text-high-emphasis  mb-2">
                  {{ truncateDescription(activity.subtitle, 55) }}
                </p>
              </VTimelineItem>
            </VTimeline>
          </template>
  
          <template v-else>
            <div class="text-center py-10">
              <p class="text-body-2 text-high-emphasis">
                No activities created yet.
              </p>
            </div>
          </template>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
  <VRow>
    <VCol
      cols="12"
      md="6"
    >
      <VCard style="height: 100%; min-height: 295px;">
        <!-- Changed height to 100% for better responsiveness -->
        <template v-if="project?.project_manager">
          <VCardItem title="Your Project Manager">
            <template #append>
              <MoreBtn
                :menu-list="[
                  { title: 'View Team', value: 'view-all', to: { name: 'team', params: { id: projectUuid } } },
                ]"
              />
            </template>
          </VCardItem>
          <VCardText>
            <VRow>
              <VCol
                cols="12"
                md="4"
                class="d-flex justify-center align-start pb-0 pt-2 mb-3"
              >
                <div v-if="project?.project_manager?.info?.avatar">
                  <VAvatar
                    :size="140"
                    class="elevation-1"
                  >
                    <VImg
                      :src="getImageUrl(project?.project_manager?.info?.avatar.path)"
                      alt="Project Manager Avatar"
                    />
                  </VAvatar>
                </div>
                <div v-else>
                  <VAvatar
                    :size="140"
                    color="light-primary"
                    class="elevation-1"
                  >
                    <span class="text-h1 text-primary">
                      {{ avatarText(project?.project_manager?.name_first + ' . ' + project?.project_manager?.name_last) }}
                    </span>
                  </VAvatar>
                </div>
              </VCol>
              <VCol
                cols="12"
                md="8"
              >
                <div>
                  <div
                    class="d-flex my-2 flex-wrap"
                    :class="justifyPMInfo"
                  >
                    <div class="d-flex align-center">
                      <div>
                        <h6 class="text-h5 text-high-emphasis d-inline-flex align-items-center">
                          {{ project?.project_manager?.name_first + ' ' + project?.project_manager?.name_last }}
                          <VBadge
                            dot
                            :color="project?.project_manager?.is_online ? 'success' : 'warning'"
                            class="ms-3 mt-2"
                          />
                        </h6>
                        <div class="text-sm text-high-emphasis">
                          <VIcon
                            class="me-1 text-primary"
                            icon="tabler-phone"
                          />
                          {{ project?.project_manager?.info?.phone }}
                        </div>
                        <div class="text-sm text-high-emphasis mt-1">
                          <VIcon
                            class="me-1 text-primary"
                            icon="tabler-calendar"
                          />
                          <RouterLink
                            class="text-high-emphasis"
                            :to="`/projects/${projectUuid}/calendar`"
                          >
                            <span class="text-decoration-underline">Schedule A Meeting</span>
                          </RouterLink>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="my-5 d-flex gap-3 flex-wrap">
                    <!-- Use flex-wrap for better mobile handling -->
                    <RouterLink :to="`/projects/${projectUuid}/chat`">
                      <VBtn
                        size="small"
                        rounded="pill"
                        color="primary"
                      >
                        Start A Chat
                      </VBtn>
                    </RouterLink>

                    <RouterLink :to="`/projects/${projectUuid}/chat`">
                      <VBtn
                        size="small"
                        rounded="pill"
                        color="primary"
                      >
                        Send A Message
                      </VBtn>
                    </RouterLink>
                  </div>
                </div>
              </VCol>
            </VRow>
          </VCardText>
        </template>
        <template v-else>
          <VCardItem title="No Project Manager Assigned">
            <template #append>
              <MoreBtn
                :menu-list="[
                  { title: 'View Team', value: 'view-all', to: { name: 'team', params: { id: projectUuid } } },
                ]"
              />
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
    <VCol
      cols="12"
      md="6"
    >
      <VCard style="height:294px!important">
        <VCardItem title="Matching Services For Your Project">
          <template #append>
            <MoreBtn
              :menu-list="[
                { title: 'View All', value: 'view-all', to: { name: 'services-setting'} },
              ]"
            />
          </template>
        </VCardItem>
        <VCardText>
          <template v-if="getServices.length">
            <VSheet
              class="mx-auto"
              max-width="800"
            >
              <VSlideGroup
                v-model="model"
                mandatory
                show-arrows
              >
                <VSlideGroupItem
                  v-for="(service, index) in getServices"
                  :key="index"
                >
                  <VSheet
                    class="mx-2 custom-border"
                    height="180"
                    width="250"
                  >
                    <div class="d-flex flex-column px-3 py-2 fill-height">
                      <span class="font-weight-bold text-h5 text-primary mb-2">{{ service.title }}</span>
                      <p 
                        class="text-body-2 text-high-emphasis text-wrap"
                        style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;margin-bottom: 10px;"
                        v-html="service.description"
                      />
                      <div class="d-flex flex-grow-1" />
                      <div class="d-flex justify-start">
                        <VBtn
                          size="small"
                          variant="elevated"
                          rounded="pill"
                          color="primary"
                          :to="{ name: 'marketplace-service-detail', params: { id: service.uuid } }"
                        >
                          Learn More
                        </VBtn>
                      </div>
                    </div>
                  </VSheet>
                </VSlideGroupItem>
              </VSlideGroup>
            </VSheet>
          </template>
          <template v-else>
            <!-- Fallback UI when no services are found -->
            <div
              class="d-flex flex-column align-center justify-center"
              style="height: 180px; text-align: center;"
            >
              <p class="font-weight-bold text-body-1 mb-2">
                No services available
              </p>
              <VBtn
                v-if="authStore.isAdmin"
                size="small"
                rounded="pill"
                color="primary"
                :to="{ name: 'services-setting' }"
              >
                Add Services
              </VBtn>
            </div>
          </template>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
  <!-- Award Bucks to PM Dialogue -->
  <VDialog
    v-model="isAwardBucksDialogue"
    :width="$vuetify.display.smAndDown ? 'auto' : 600"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="closeAwardBucksDialogue" />

    <VCard
      title="Award Bucks to PM"
      class="pricing-dialog"
    >
      <VForm
        ref="saveAwardedBucksForm"
        @submit.prevent="submitAwardedBucks"
      >
        <VCardText>
          <VAlert
            v-if="!project.is_bucks_share_assigned_to_pm"
            type="error"
            class="text-sm mb-5"
            dismissible
          >
            Please assign bucks share to the Project Manager.
            <RouterLink
              class="text-decoration-underline ms-1 me-1 text-white"
              :to="`/projects/${projectUuid}/bucks`"
            >
              Go to Bucks Management
            </RouterLink>
          </VAlert>
          <div class="d-flex align-items-center">
            <VCheckbox
              v-model="awardedBucks"
              :disabled="!project.is_bucks_share_assigned_to_pm"
              label="Award to PM"
              class="ps-0 me-1"
              :true-value="1"
              :false-value="0"
            />
            <small
              v-if="project?.pm_bucks"
              class="mt-2 text-primary font-weight-bold"
            >(${{ project?.pm_bucks }})</small>
          </div>
          <AppTextarea
            v-model="comment"
            label="Comment"
            rows="3"
            autofocus
            multiline
          />
        </VCardText>
        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            @click="closeAwardBucksDialogue"
          >
            Cancel
          </VBtn>
          <VBtn
            :disabled="loadStatus === 1 || !project.is_bucks_share_assigned_to_pm"
            type="submit"
            @click="saveAwardedBucksForm?.validate()"
          >
            <VProgressCircular
              v-if="loadStatus === 1"
              indeterminate
              size="16"
              color="white"
            />
            <span v-if="loadStatus === 1">Processing...</span>
            <span v-else>Award</span>
          </VBtn>
        </VCardText>
      </vform>
    </VCard>
  </VDialog>
</template>

<script setup lang="js">
import { layoutConfig } from '@layouts'
import { computed, onBeforeMount, onMounted, onUnmounted, ref } from 'vue'
import Swal from 'sweetalert2'
import confetti from 'canvas-confetti'
import { useHead } from '@unhead/vue'
import moment from 'moment'
import Loader from "@/components/Loader.vue"
import { useProjectStore } from "@/store/projects"
import { useRoute } from 'vue-router'
import { useProjectBucksStore } from "@/store/project_bucks"
import { useAuthStore } from "@/store/auth"
import { useUserStore } from "@/store/users"
import { useUserSettingStore } from "@/store/user_settings"
import { useProjectListStore } from "@/store/project_lists"
import { useToast } from "vue-toastification"
import { VSheet } from 'vuetify/lib/components/index.mjs'

onBeforeMount(async () => {
  await getProjectProgress()
})

const projectStore = useProjectStore()
const projectBucksStore = useProjectBucksStore()
const authStore = useAuthStore()
const userStore = useUserStore()
const userSettingStore = useUserSettingStore()
const projectListStore = useProjectListStore()
const $route = useRoute()
const toast = useToast()

const projectUuid = $route.params.id
const isCompleted = ref(0)
const model = ref(null)
const showAwardBucksBtn = ref(0)
const saveAwardedBucksForm = ref()
const isAwardBucksDialogue = ref(false)
const awardedBucks = ref(0)
const comment = ref(null)
const justifyPMInfo = ref('justify-space-between')
const activeListIndex = ref(null)
const newListName = ref('')

const isDueDateToday = dueDate => {
  if (!dueDate) return false

  const today = new Date()
  const dueDateObj = new Date(dueDate)

  today.setHours(0, 0, 0, 0)
  dueDateObj.setHours(0, 0, 0, 0)

  return dueDateObj.getTime() === today.getTime()
}

const isDueDateOverdue = dueDate => {
  if (!dueDate) return false

  const today = new Date()
  const dueDateObj = new Date(dueDate)

  today.setHours(0, 0, 0, 0)
  dueDateObj.setHours(0, 0, 0, 0)

  return dueDateObj.getTime() < today.getTime()
}

const isMobile = () => {
  return window.innerWidth <= 768 || window.innerWidth <= 926
}

const handleResize = () => {
  justifyPMInfo.value = isMobile() ? 'justify-center' : 'justify-space-between'
}

onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})

const getProjectProgress = async () => {
  await fetchProject()
  isCompleted.value = project.value.is_completed
  awardedBucks.value = project.value.is_pm_bucks_awarded
  comment.value = project.value.comments
  if(isCompleted.value)
  {
    showAwardBucksBtn.value = true
  }
  await projectStore.getProgress(projectUuid)
  await fetchMembers()
  await fetchServices()
  await fetchActivities()
}

const fetchProject = async () => {
  try {
    await projectStore.show(projectUuid)
  } catch (error) {
    toast.error('Error fetching project:', error)
  }
}

const fetchServices = async () => {
  try {
    //await userSettingStore.getServicesByType(project?.value?.project_type_uuid)
    await userSettingStore.getServicesWithoutPagination()
  } catch (error) {
    toast.error('Error fetching services:', error)
  }
}

const fetchActivities = async () => {
  try {
    await projectStore.getActivities(projectUuid)
  } catch (error) {
    toast.error('Error fetching activities:', error)
  }
}


const fetchMembers = async () => {
  try {
    await userStore.getByProjects(1, 10, null, null, null, projectUuid)
  } catch (error) {
    toast.error('Error fetching services:', error)
  }
}

const showConfetti = () => {
  confetti({
    particleCount: 1000,
    spread: 360,
  })
}

const handleProjectCompleteSwitchChange = async projectName => {
  const originalState = isCompleted.value
  const isComplete = originalState === 1

  const title = isComplete
    ? `Confirm Completion of the ${projectName} Project`
    : `Confirm Incompletion of the ${projectName} Project`

  const actionText = `<small>You are about to mark the project as ${isComplete ? 'completed' : 'incomplete'}. This action cannot be undone. Do you wish to continue?</small>`

  try {
    const { isConfirmed } = await Swal.fire({
      title,
      html: actionText,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "rgba(var(--v-theme-primary))",
      cancelButtonColor: "#808390",
      confirmButtonText: isComplete ? "Yes, mark as completed" : "Yes, mark as incomplete",
      cancelButtonText: "Cancel",
      didOpen: () => {
        document.querySelector('.swal2-confirm').blur()

        const titleElem = document.querySelector('.swal2-title')
        if (titleElem) titleElem.style.fontSize = '18px'
        const textElem = document.querySelector('.swal2-html-container')
        if (textElem) textElem.style.marginTop = '8px'
      },
    })

    if (isConfirmed) {
      await projectStore.complete({ uuid: projectUuid, is_completed: isCompleted.value })
      showAwardBucksBtn.value = isComplete
      if(isComplete)
      {
        showConfetti()
        fetchActivities()
      }
    } else {
      isCompleted.value = originalState ? 0 : 1
    }
  } catch (error) {
    isCompleted.value = originalState ? 0 : 1
    toast.error('Error updating project:', error)
  }
}

const closeAwardBucksDialogue = () => {
  isAwardBucksDialogue.value = false
  comment.value = null
}

const openAwardBucksDialogue =() => {
  isAwardBucksDialogue.value = true
}

const resetListPlaceholderForm = () => {
  activeListIndex.value = null
  newListName.value = ''
}

const saveList = async () => {
  try {
    
    const name = newListName.value.trim()
    if (name === '') {
      toast.error('List name cannot be empty.')

      return
    }

    const addListDetails = {
      name: name,
      project_uuid: projectUuid,
    }

    await projectListStore.create(addListDetails)
    resetListPlaceholderForm()
    toast.success('List added successfully', { timeout: 1000 })
    await projectStore.getProgress(projectUuid)
  } catch (error) {
    console.error('Error adding list:', error)
    toast.error('Failed to add list:', error)
  }
}

const submitAwardedBucks = async () => {
  saveAwardedBucksForm.value?.validate().then(async ({ valid: isValid }) => {
    if (isValid) {
      try {
        const payload = {
          uuid: projectUuid,
          is_pm_bucks_awarded: awardedBucks.value,
          comments: comment.value || null,
        }

        await projectStore.complete(payload)

        if (getErrors.value) {
          toast.error('Something went wrong. Please try again later')
        } else {
          toast.success('Bucks awarded successfully')
          closeAwardBucksDialogue()
          isCompleted.value = project.value.is_completed
          awardedBucks.value = project.value.is_pm_bucks_awarded
          comment.value = project.value.comments
          await fetchActivities()
        }
      } catch (error) {
        toast.error(`Error awarding bucks to pm: ${error.message || error}`)
      }
    }
  })
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

const truncateDescription = (description, length) => {
  return description.length > length
    ? description.slice(0, length) + '...'
    : description
}

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

const projectProgress = computed(() => {
  return projectStore.getProjectProgress
})

const upcomingTasks = computed(() => {
  return projectStore.getProjectUpcomingTasks
})

const project = computed(() =>{
  return projectStore.getProject
})

const projectBucks = computed(() =>{
  return projectBucksStore.getBucksDetails
})

const loadStatus = computed(() => {
  return projectStore.getLoadStatus
})

const getErrors = computed(() => {
  return projectStore.getErrors
})

const getServices = computed(() => {
  return userSettingStore.getProjectServicesWithoutPagination
})

const getUsersByProjects = computed(() => {
  return userStore.getUsersByProjects
})

const getActivities = computed(() => {
  return projectStore.getProjectActivities.slice(0, 14)
})

const userDetails = computed(() => {
  return userStore.getUser
})

const getListLoadStatus = computed(() => {
  return projectListStore.getLoadStatus
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
.inbox-card {
    max-height: 280px;
    overflow-y: auto;
    margin-bottom: 25px;
}
.logistics-card-statistics{
  border-block-end: 3px solid rgba(var(--v-theme-primary), var(--v-disabled-opacity));
}
.logistics-card-statistics:hover {
  border-bottom: 3px solid rgba(var(--v-theme-primary));
}
.custom-border {
  border: 1px solid rgba(var(--v-theme-primary));
  border-radius: 4px;
  background-color: rgb(var(--v-theme-grey-50));
}
.custom-border-list{
    box-shadow: 0 3px 12px rgba(var(--v-shadow-key-umbra-color), var(--v-shadow-md-opacity));
    border-block-end: 3px solid rgba(var(--v-theme-primary), var(--v-pressed-opacity));
    border-radius: 6px;
    background-color: rgb(var(--v-theme-surface));
    cursor: pointer;
  }
  .custom-border-list:hover{
    border-bottom: 3px solid rgba(var(--v-theme-primary));
  }
.horizontal-scroll {
  overflow-x: auto;
  overflow-y: hidden;
  white-space: nowrap;
}
.upcoming-task-item{
  border-bottom: 1px solid rgba(var(--v-theme-background));
  margin-bottom: 5px;
  margin-top: 5px;
}

.upcoming-task-list-name:hover{
  color: rgba(var(--v-theme-primary)) !important;
  cursor: pointer;
}

.upcoming-task-item:last-child {
  border-bottom: none;
}

.horizontal-scroll::-webkit-scrollbar {
  height: 10px; /* Set height of the scrollbars */
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

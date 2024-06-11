<template>
  <Loader v-if="getUserLoadStatus == 1" />
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
    <VRow class="mt-5 mb-1">
      <VCol
        cols="12"
        class="pt-0 ps-4"
      >
        <h3>
          Manage Task & Events
        </h3>
      </VCol>
    </VRow>
    <VCard>
      <VLayout style="z-index: 0;">
        <VMain>
          <VCard flat>
            <FullCalendar
              ref="refCalendar"
              :options="calendarOptions"
            />
          </VCard>
        </VMain>
      </VLayout>
    </VCard>
    <CalendarEventHandler
      v-model:isDrawerOpen="isEventHandlerSidebarActive"
      :event="event"
      :get-project-guests="getProjectGuests"
      :get-load-status="getLoadStatus"
      :project-uuid="route.params.id"
      @add-event="addEvent"
      @update-event="updateEvent"
      @remove-event="removeEvent"
      @refresh-calendar="setCalendarEvents"
    />
  </div>
</template>

<script setup>
import FullCalendar from '@fullcalendar/vue3'
import { blankEvent, useCalendar } from '@/views/apps/calendar/useCalendar'

// Components
import CalendarEventHandler from '@/views/apps/calendar/CalendarEventHandler.vue'
import { useProjectTaskStore } from "@/store/project_tasks"
import { useCalendarEventStore } from "@/store/calendar_events"
import { useProjectStore } from "@/store/projects"
import { useUserStore } from "@/store/users"
import Loader from "@/components/Loader.vue"
import { useRoute } from 'vue-router'
import sketch from '@images/icons/project-icons/sketch.png'
import { ref, watch, onBeforeMount, computed } from 'vue'

// 👉 Store
const projectTaskStore = useProjectTaskStore()
const userStore = useUserStore()
const projectStore = useProjectStore()
const calendarEventStore = useCalendarEventStore()
const route = useRoute()

// 👉 Event
const event = ref(structuredClone(blankEvent))
const isEventHandlerSidebarActive = ref(false)

watch(isEventHandlerSidebarActive, val => {
  if (!val) event.value = structuredClone(blankEvent)
})

const { isLeftSidebarOpen } = useResponsiveLeftSidebar()

// 👉 useCalendar
const {
  refCalendar,
  calendarOptions,
  addEvent,
  updateEvent,
  removeEvent,
} = useCalendar(event, isEventHandlerSidebarActive, isLeftSidebarOpen, route.params.id)

// use before mounted function and set events option in calendarOptions
onBeforeMount(async () => {
  await fetchProject()
  setCalendarEvents()
})

const fetchProjectTasks = async () => {
  try {
    const projectUuid = route.params.id

    await projectTaskStore.getAll(projectUuid)
  } catch (error) {
    console.error('Error fetching project tasks', error)
  }
}

const fetchCalendarEvents = async () => {
  try {
    const projectUuid = route.params.id

    await calendarEventStore.getAll(projectUuid)
  } catch (error) {
    console.error('Error fetching calendar events', error)
  }
}

const fetchProjectGuests = async () => {
  try {
    const projectUuid = route.params.id

    await userStore.getByProjects(projectUuid)
  } catch (error) {
    console.error('Error fetching guests', error)
  }
}

const fetchProject = async () => {
  try {
    await projectStore.show(route.params.id)
    isLoading.value = true
  } catch (error) {
    toast.error('Error fetching project:', error)
  }
  finally {
    isLoading.value = false
  }
}

const setCalendarEvents = async () => {
  await fetchCalendarEvents()
  await fetchProjectTasks()
  await fetchProjectGuests()

  const projectTasks = projectTaskStore.getProjectAllTasks
  const calendarEvents = calendarEventStore.getCalendarEvents

  const combinedEvents = [
    ...projectTasks.map(task => ({
      id: task.id,
      uuid: task.uuid,
      title: task.name,
      start: task.due_date ? task.due_date : task.created_at,
      extendedProps: {
        description: task.description,
        guests: task.guests || [],
      },
    })),
    ...calendarEvents.map(event => ({
      id: event.id,
      uuid: event.uuid,
      title: event.name,
      start: event.start_date,
      url: event.url ? event.url : '',
      end: event.end_date ? event.end_date : event.start_date,
      extendedProps: {
        description: event.description,
        guests: event.guests || [],
      },
    })),
  ]

  calendarOptions.events = combinedEvents

  // Trigger a re-render
  if (refCalendar.value) {
    const calendarApi = refCalendar.value.getApi()

    calendarApi.removeAllEvents()
    calendarApi.addEventSource(combinedEvents)
  }
}

const getProjectGuests = computed(() => {
  return userStore.getUsersByProjects.map(member => ({
    id: member.id,
    name: `${member.name_first} ${member.name_last} (${member.role})`,
  }))
})

const getLoadStatus = computed(() => {
  return calendarEventStore.getLoadStatus
})

const project = computed(() => {
  return projectStore.getProject
})

const getUserLoadStatus = computed(() => {
  return userStore.getLoadStatus
})
</script>

  <style lang="scss">
  @use "@core-scss/template/libs/full-calendar";

  .calendar-filters {
    max-height: 374px;
    overflow-y: scroll;
    margin-bottom: 20px;
  }

  .calendars-checkbox {
    .v-label {
      color: rgba(var(--v-theme-on-surface), var(--v-high-emphasis-opacity));
      opacity: var(--v-high-emphasis-opacity);
    }
  }

  .calendar-add-event-drawer {
    &.v-navigation-drawer:not(.v-navigation-drawer--temporary) {
      border-end-start-radius: 0.375rem;
      border-start-start-radius: 0.375rem;
    }
  }

  .calendar-date-picker {
    display: none;

    +.flatpickr-input {
      +.flatpickr-calendar.inline {
        border: none;
        box-shadow: none;

        .flatpickr-months {
          border-block-end: none;
        }
      }
    }

    &~.flatpickr-calendar .flatpickr-weekdays {
      margin-block: 0 4px;
    }
  }
  </style>

  <style lang="scss" scoped>
  .v-layout {
    overflow: visible !important;

    .v-card {
      overflow: visible;
    }
  }
  </style>

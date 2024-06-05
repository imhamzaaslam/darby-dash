<template>
  <div>
    <VRow class="mt-0 mb-3">
      <VCol 
        cols="12"
        class="pt-0 ps-4"
      >
        <h3>Manage Task & Events</h3>
      </VCol>
    </VRow>
    <VCard>
      <!-- `z-index: 0` Allows overlapping vertical nav on calendar -->
      <VLayout style="z-index: 0;">
        <!-- 👉 Navigation drawer -->
        <VMain>
          <VCard flat>
            <FullCalendar
              ref="refCalendar"
              :options="calendarOptions"
              :events="staticEvents"
            />
          </VCard>
        </VMain>
      </VLayout>
    </VCard>
    <CalendarEventHandler
      v-model:isDrawerOpen="isEventHandlerSidebarActive"
      :event="event"
      @add-event="addEvent"
      @update-event="updateEvent"
      @remove-event="removeEvent"
    />
  </div>
</template>

<script setup>
import FullCalendar from '@fullcalendar/vue3'
import {
  blankEvent,
  useCalendar,
} from '@/views/apps/calendar/useCalendar'
import { useCalendarStore } from '@/views/apps/calendar/useCalendarStore'

// Components
import CalendarEventHandler from '@/views/apps/calendar/CalendarEventHandler.vue'
import { useProjectTaskStore } from "@/store/project_tasks"
import { useRoute } from 'vue-router'
import { set } from '@vueuse/core'

// 👉 Store
const store = useCalendarStore()
const projectTaskStore = useProjectTaskStore()
const route = useRoute()

// 👉 Event
const event = ref(structuredClone(blankEvent))
const isEventHandlerSidebarActive = ref(false)

watch(isEventHandlerSidebarActive, val => {
  if (!val)
    event.value = structuredClone(blankEvent)
})

const { isLeftSidebarOpen } = useResponsiveLeftSidebar()

// 👉 useCalendar
const { refCalendar, calendarOptions, addEvent, updateEvent, removeEvent, jumpToDate, refetchEvents } = useCalendar(event, isEventHandlerSidebarActive, isLeftSidebarOpen)

// put pending, in_progress, done in selectedTaskType
const allTaskSelected = ref(true)
const selectedTaskType = ref(['Todo', 'In_progress', 'Done'])

// use before mounted functiona nd set events option in calendarOptions
onBeforeMount(async () => {
  await fetchProjectTasks()
  setCalendarEvents(projectTaskStore.getProjectAllTasks)
})

const fetchProjectTasks = async () => {
  try {
    const projectUuid = route.params.id

    await projectTaskStore.getAll(projectUuid)
  } catch (error) {
    console.error('Error fetching project tasks', error)
  }
}

const setCalendarEvents = projectTasks => {
  calendarOptions.events = projectTasks.map(task => ({
    id: task.id,
    title: task.name,
    start: task.due_date ? task.due_date : task.created_at,
    extendedProps: {
      calendar: 'Business',
    },
  }))

  // Trigger a re-render
  if (refCalendar.value) {
    const calendarApi = refCalendar.value.getApi()

    calendarApi.removeAllEvents()
    calendarApi.addEventSource(calendarOptions.events)
  }
}

const updateCalendarEvents = () => {
  const selectedTaskTypes = toRaw(selectedTaskType.value)
  const projectTasks = projectTaskStore.getProjectAllTasks
  const filteredTasks = projectTasks.filter(task => selectedTaskTypes.includes(task.status))
  
  setCalendarEvents(filteredTasks)
}

const taskFilters = [
  {
    label: 'Pending',
    value: 'Todo',
    color: 'warning',
    selected: true,
  },
  {
    label: 'In Progress',
    value: 'In_progress',
    color: 'info',
    selected: true,
  },
  {
    label: 'Completed',
    value: 'Done',
    color: 'success',
    selected: true,
  },
]

// 👉 Toggle all tasks
const toggleAllTasks = () => {
  if (allTaskSelected.value) {
    selectedTaskType.value = taskFilters.map(i => i.value)
  } else {
    selectedTaskType.value = []
  }
}

// use watch to check the selectedTaskType and update the selected filters in taskFilters
watch(selectedTaskType, val => {
  taskFilters.forEach(filter => {
    filter.selected = val.includes(filter.value)
  })
  allTaskSelected.value = val.length === taskFilters.length

  updateCalendarEvents()
})

// 👉 Check all
const checkAll = computed({

  /*GET: Return boolean `true` => if length of options matches length of selected filters => Length matches when all events are selected
SET: If value is `true` => then add all available options in selected filters => Select All
Else if => all filters are selected (by checking length of both array) => Empty Selected array  => Deselect All
*/
  get: () => store.selectedCalendars.length === store.availableCalendars.length,
  set: val => {
    if (val)
      store.selectedCalendars = store.availableCalendars.map(i => i.label)
    else if (store.selectedCalendars.length === store.availableCalendars.length)
      store.selectedCalendars = []
  },
})
// !SECTION
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
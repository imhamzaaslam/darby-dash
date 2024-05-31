<template>
  <div>
    <VCard>
      <!-- `z-index: 0` Allows overlapping vertical nav on calendar -->
      <VLayout style="z-index: 0;">
        <!-- 👉 Navigation drawer -->
        <VNavigationDrawer
          v-model="isLeftSidebarOpen"
          width="292"
          absolute
          touchless
          location="start"
          class="calendar-add-event-drawer"
          :temporary="$vuetify.display.mdAndDown"
        >
          <div style="margin: 1.4rem;">
            <VBtn
              block
              prepend-icon="tabler-plus"
              @click="isEventHandlerSidebarActive = true"
            >
              Add event
            </VBtn>
          </div>

          <VDivider />

          <div class="d-flex align-center justify-center pa-2 mb-3">
            <AppDateTimePicker
              :model-value="new Date().toJSON().slice(0, 10)"
              :config="{ inline: true }"
              class="calendar-date-picker"
              @input="jumpToDate($event.target.value)"
            />
          </div>

          <VDivider />
          <div class="pa-7 calendar-filters">
            <!-- <p class="text-sm text-uppercase text-disabled mb-3">
              Events FILTER
            </p>

            <div class="d-flex flex-column calendars-checkbox">
              <VCheckbox
                v-model="checkAll"
                label="View all"
              />
              <VCheckbox
                v-for="calendar in store.availableCalendars"
                :key="calendar.label"
                v-model="store.selectedCalendars"
                :value="calendar.label"
                :color="calendar.color"
                :label="calendar.label"
              />
            </div> -->

            <p class="text-sm text-uppercase text-disabled mb-3">
              Tasks FILTER
            </p>

            <div class="d-flex flex-column calendars-checkbox">
              <VCheckbox
                v-model="allTaskSelected"
                label="View all"
                @change="toggleAllTasks"
              />
              <VCheckbox
                v-for="filter in taskFilters"
                :key="filter.label"
                v-model="selectedTaskType"
                :value="filter.value"
                :color="filter.color"
                :label="filter.label"
                :checked="filter.selected"
              />
            </div>
          </div>
          <VDivider />
        </VNavigationDrawer>

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
onBeforeMount(() => {
  fetchProjectTasks()
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
  console.log('setCalendarEvents', projectTasks)
  calendarOptions.events = projectTasks.map(task => ({
    id: task.id,
    title: task.name,
    start: task.created_at,
    end: task.due_date || task.created_at,
    extendedProps: {
      calendar: 'Business',
    },
  }))
}

const updateCalendarEvents = () => {
  // get selectedTaskType values as array
  const selectedTaskTypes = toRaw(selectedTaskType.value)

  console.log('selectedTaskTypes', selectedTaskTypes)

  const projectTasks = projectTaskStore.getProjectAllTasks

  console.log('projectTasks', projectTasks)

  const filteredTasks = projectTasks.filter(task => selectedTaskTypes.includes(task.status))

  console.log('filteredTasks', filteredTasks)

  setCalendarEvents(filteredTasks)
  refetchEvents()
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
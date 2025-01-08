import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import listPlugin from '@fullcalendar/list'
import timeGridPlugin from '@fullcalendar/timegrid'
import { useConfigStore } from '@core/stores/config'
import { useCalendarEventStore } from "@/store/calendar_events"

export const blankEvent = {
  title: '',
  start: '',
  end: '',
  url: '',
  extendedProps: {
    guests: [],
    description: null,
    uuid: '',
    isTask: false,
  },
}
export const useCalendar = (event, isEventHandlerSidebarActive, isLeftSidebarOpen, projectUuid) => {
  const configStore = useConfigStore()

  // 👉 Store
  const store = useCalendarEventStore()

  // 👉 Calendar template ref
  const refCalendar = ref()


  // ℹ️ Extract event data from event API
  const extractEventDataFromEventApi = eventApi => {
    const { id, title, start, end, url, extendedProps: { guests, description, uuid, isTask } } = eventApi

    return {
      id,
      uuid,
      title,
      start,
      end,
      url,
      extendedProps: {
        guests,
        description,
        uuid,
        isTask,
      },
    }
  }

  if (typeof process !== 'undefined' && process.server)
    store.getAll({ projectUuid })


  // 👉 Fetch events
  const fetchEvents = (info, successCallback) => {
    // If there's no info => Don't make useless API call
    if (!info)
      return
    store.getAll({ projectUuid })
      .then(r => {
        successCallback(r.map(e => ({
          ...e,

          // Convert string representation of date to Date object
          start: new Date(e.start_date),
          end: e.end_date ? new Date(e.end_date) : null,
          uuid: e.uuid,
          url: e.url,
          isTask: e.isTask,
        })))
      })
      .catch(e => {
        console.error('Error occurred while fetching calendar events', e)
      })
  }


  // 👉 Calendar API
  const calendarApi = ref(null)


  // 👉 Remove event in calendar [UI]
  const removeEventInCalendar = eventId => {
    const _event = calendarApi.value?.getEventById(eventId)
    if (_event)
      _event.remove()
  }


  // 👉 refetch events
  const refetchEvents = () => {
    calendarApi.value?.refetchEvents()
  }


  // 👉 Add event
  const addEvent = async (_event, callback) => {
    try {
      await store.create(_event)
      if (callback) callback()
    } catch (error) {
      console.error('Error creating event:', error)
    }
  }

  // 👉 Update event
  const updateEvent = async (_event, callback) => {
    try {
      await store.update(_event)
      if (callback) callback()
    } catch (error) {
      console.error('Error updating event:', error)
    }
  }

  // 👉 Remove event
  const removeEvent = event => {
    store.delete(event).then(() => {
      removeEventInCalendar(event.id)
    })
  }


  // 👉 Calendar options
  const calendarOptions = {
    plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin, listPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
      start: 'drawerToggler,prev,next title',
      end: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
    },

    // ❗ We need this to be true because when its false and event is allDay event and end date is same as start data then Full calendar will set end to null
    forceEventDuration: true,

    /*
        Enable dragging and resizing event
        Docs: https://fullcalendar.io/docs/editable
      */
    editable: true,

    /*
        Enable resizing event from start
        Docs: https://fullcalendar.io/docs/eventResizableFromStart
      */
    eventResizableFromStart: true,

    /*
        Automatically scroll the scroll-containers during event drag-and-drop and date selecting
        Docs: https://fullcalendar.io/docs/dragScroll
      */
    dragScroll: true,

    /*
        Max number of events within a given day
        Docs: https://fullcalendar.io/docs/dayMaxEvents
      */
    dayMaxEvents: 2,

    /*
        Determines if day names and week names are clickable
        Docs: https://fullcalendar.io/docs/navLinks
      */
    navLinks: true,
    eventClassNames({ event: calendarEvent }) {
      const colorName = calendarEvent._def.extendedProps.isTask ? 'error' : 'primary'
      
      return [
        // Background Color
        `bg-light-${colorName} text-${colorName}`,
      ]
    },
    eventClick({ event: clickedEvent, jsEvent }) {
      // * Only grab required field otherwise it goes in infinity loop
      // ! Always grab all fields rendered by form (even if it get `undefined`) otherwise due to Vue3/Composition API you might get: "object is not extensible"
      jsEvent.preventDefault()
      event.value = extractEventDataFromEventApi(clickedEvent)
      isEventHandlerSidebarActive.value = true
    },

    // customButtons
    dateClick(info) {
      event.value = { ...event.value, start: info.date }
      isEventHandlerSidebarActive.value = true
    },

    /*
          Handle event drop (Also include dragged event)
          Docs: https://fullcalendar.io/docs/eventDrop
          We can use `eventDragStop` but it doesn't return updated event so we have to use `eventDrop` which returns updated event
        */
    eventDrop({ event: droppedEvent }) {
      updateEvent(extractEventDataFromEventApi(droppedEvent))
    },

    /*
          Handle event resize
          Docs: https://fullcalendar.io/docs/eventResize
        */
    eventResize({ event: resizedEvent }) {
      if (resizedEvent.start && resizedEvent.end)
        updateEvent(extractEventDataFromEventApi(resizedEvent))
    },

    eventContent: arg => {
      // Truncate the title if it exceeds 16 characters
      const truncatedTitle = arg.event.title.length > 22 ? arg.event.title.substring(0, 22) + "..." : arg.event.title

      return { html: `<div class="fc-content">${truncatedTitle}</div>` }
    },

    customButtons: {
      drawerToggler: {
        text: 'calendarDrawerToggler',
        click() {
          isLeftSidebarOpen.value = true
        },
      },
    },
    firstDay: 1,
  }


  // 👉 onMounted
  onMounted(() => {
    calendarApi.value = refCalendar.value.getApi()
  })


  // 👉 Jump to date on sidebar(inline) calendar change
  const jumpToDate = currentDate => {
    calendarApi.value?.gotoDate(new Date(currentDate))
  }

  watch(() => configStore.isAppRTL, val => {
    calendarApi.value?.setOption('direction', val ? 'rtl' : 'ltr')
  }, { immediate: true })

  return {
    refCalendar,
    calendarOptions,
    refetchEvents,
    fetchEvents,
    addEvent,
    updateEvent,
    removeEvent,
    jumpToDate,
  }
}

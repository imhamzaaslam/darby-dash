import { defineStore } from 'pinia'
import CalendarEventService from '../services/CalendarEventService'

export const useCalendarEventStore = defineStore('calendar_events', {
  state: () => ({
    calendarEvents: [],
    calendarEvent: null,
    calendarEventsCount: 0,
    loadStatus: 0,
    error: null,
  }),
  persist: true,
  actions: {
    async getAll(projectId) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await CalendarEventService.getCalendarEvents(projectId)

        this.projectLists = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('getCalendarEvents error ', error)
      }
    },
    async create(calendarEvent) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await CalendarEventService.createCalendarEvent(calendarEvent)

        this.calendarEvent = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('createCalendarEvent error ', error)
      }
    },
    async update(calendarEvent) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await CalendarEventService.updateCalendarEvent(calendarEvent)

        this.calendarEvent = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('updateCalendarEvent error ', error)
      }
    },
    async delete(calendarEvent) {
      this.error = null
      this.loadStatus = 1
      try {
        const response = await CalendarEventService.deleteCalendarEvent(calendarEvent)

        this.calendarEvent = response.data.data
        this.loadStatus = 2
      } catch (error) {
        this.error = error
        this.loadStatus = 3
        console.error('deleteCalendarEvent error ', error)
      }
    },
  },
  getters: {
    getLoadStatus: state => state.loadStatus,
    getCalendarEvents: state => state.calendarEvents,
    getCalendarEvent: state => state.calendarEvent,
    getErrors: state => state.error,
  },
})

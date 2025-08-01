<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'
import { useToast } from "vue-toastification"
import moment from 'moment'
import Swal from 'sweetalert2'

// 👉 store
const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  projectUuid: {
    type: String,
    required: true,
  },
  getLoadStatus: Number,
  getProjectGuests: {
    type: Object,
    required: true,
  },
  event: {
    type: null,
    required: true,
  },
  setCalendarEvents: Function,
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'addEvent',
  'updateEvent',
  'removeEvent',
])

const formatDate = date => {
  return moment(date).format('YYYY-MM-DD HH:mm')
}

const toast = useToast()

const refForm = ref()

// 👉 Event
const event = ref(JSON.parse(JSON.stringify(props.event)))

console.log('This is event', event)

const resetEvent = () => {
  event.value = JSON.parse(JSON.stringify(props.event))
  nextTick(() => {
    refForm.value?.resetValidation()
  })
}

watch(() => props.isDrawerOpen, resetEvent)

const removeEvent = async () => {
  const confirmDelete = await Swal.fire({
    title: "Are you sure?",
    html: `<small>Do you want to remove event title <b>${event.value.title}</b>?</small>`,
    icon: "warning",
    showCancelButton: true,
    customClass: {
      confirmButton: 'v-btn custom-btn-style rounded-pill px-4',
      cancelButton: 'v-btn custom-secondary-btn rounded-pill px-4',
    },
    confirmButtonText: "Yes, delete it!",
    didOpen: () => {
      document.querySelector('.swal2-confirm').blur()
    },
  })

  if (confirmDelete.isConfirmed) {
    const eventDetails = ref({
      id: event.value.id,
      uuid: event.value.uuid,
      project_uuid: props.projectUuid,
    })

    emit('removeEvent', eventDetails.value)

    // Close drawer
    emit('update:isDrawerOpen', false)
    toast.success('Event deleted successfully', { timeout: 1000 })
  }
}

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

const handleSubmit = async () => {
  const { valid } = await refForm.value?.validate()

  if (valid) {
    const eventDetails = ref({
      name: event.value.title,
      uuid: event.value.uuid,
      project_uuid: props.projectUuid,
      description: event.value.extendedProps.description,
      start_date: event.value.start ? formatDate(event.value.start) : null,
      end_date: event.value.end ? formatDate(event.value.end) : null,
      url: event.value.url,
      guests_ids: event.value.extendedProps.guests,
    })

    const onComplete = () => {
      props.setCalendarEvents()
      toast.success('Event processed successfully', { timeout: 1000 })
    }

    try {
      if ('id' in event.value) {
        emit('updateEvent', eventDetails.value, onComplete)
      } else {
        emit('addEvent', eventDetails.value, onComplete)
      }
      emit('update:isDrawerOpen', false)
    } catch (error) {
      toast.error('An error occurred while updating the calendar.', { timeout: 1000 })
    }
  }
}


// 👉 Form
const onCancel = () => {

  // Close drawer
  emit('update:isDrawerOpen', false)
  nextTick(() => {
    refForm.value?.reset()
    resetEvent()
    refForm.value?.resetValidation()
  })
}

const startDateTimePickerConfig = computed(() => {
  /* if (event.value.end)
    config.maxDate = event.value.end */

  return {
    enableTime: true,
    dateFormat: `m/d/Y H:i`,
  }
})

const endDateTimePickerConfig = computed(() => {
  const config = {
    enableTime: true,
    dateFormat: `m/d/Y H:i`,
  }

  if (event.value.start)
    config.minDate = event.value.start

  return config
})

const dialogModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}
</script>

<template>
  <VNavigationDrawer
    temporary
    location="end"
    :model-value="props.isDrawerOpen"
    width="420"
    class="scrollable-content"
    @update:model-value="dialogModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      :title="event.id ? event.extendedProps.isTask ? 'Task Details' : 'Update Event' : 'Add Event'"
      @cancel="$emit('update:isDrawerOpen', false)"
    >
      <template
        v-if="!event.extendedProps.isTask"
        #beforeClose
      >
        <IconBtn
          v-show="event.id"
          color="error"
          @click="removeEvent"
        >
          <VIcon
            size="18"
            icon="tabler-trash"
          />
        </IconBtn>
      </template>
    </AppDrawerHeaderSection>

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <!-- SECTION Form -->
          <VForm
            ref="refForm"
            @submit.prevent="handleSubmit"
          >
            <VRow>
              <!-- 👉 Title -->
              <VCol cols="12">
                <AppTextField
                  v-model="event.title"
                  autofocus
                  label="Event Title *"
                  :disabled="event.extendedProps.isTask"
                  placeholder="Meeting with Jane"
                  :class="{ 'cursor-not-allowed': event.extendedProps.isTask }"
                  :rules="[requiredValidator]"
                />
              </VCol>

              <!-- 👉 Start date -->
              <VCol cols="12">
                <AppDateTimePicker
                  :key="JSON.stringify(startDateTimePickerConfig)"
                  v-model="event.start"
                  :rules="[requiredValidator]"
                  :disabled="event.extendedProps.isTask"
                  label="Start date"
                  placeholder="Select Date"
                  :config="startDateTimePickerConfig"
                />
              </VCol>

              <!-- 👉 End date -->
              <!--
                <VCol cols="12">
                <AppDateTimePicker
                :key="JSON.stringify(endDateTimePickerConfig)"
                v-model="event.end"
                label="Due date"
                placeholder="Select Date"
                :disabled="event.id && event.extendedProps.isTask"
                :config="endDateTimePickerConfig"
                />
                </VCol> 
              -->

              <!-- 👉 Event URL -->
              <VCol
                v-if="!event.extendedProps.isTask"
                cols="12"
              >
                <AppTextField
                  v-model="event.url"
                  label="Event URL"
                  placeholder="https://event.com/meeting"
                  :rules="[urlValidator]"
                  type="url"
                />
              </VCol>

              <!-- 👉 Guests -->
              <VCol
                v-if="!event.extendedProps.isTask"
                cols="12"
              >
                <AppAutocomplete
                  v-model="event.extendedProps.guests"
                  label="Members"
                  placeholder="Select Members"
                  :items="props.getProjectGuests"
                  :item-title="item => item.name"
                  :item-value="item => item.id"
                  chips
                  multiple
                  closable-chips
                  eager
                  clearable
                  clear-icon="tabler-x"
                >
                  <template #chip="{ props, item }">
                    <VChip
                      v-bind="props"
                      variant="elevated"
                      color="primary"
                    >
                      <VAvatar
                        color="white"
                        :image="item?.raw?.avatar ? getImageUrl(item?.raw?.avatar?.path) : undefined"
                        :variant="item?.raw?.avatar ? undefined : 'tonal'"
                        size="18"
                      >
                        <small
                          v-if="!item?.raw?.avatar"
                          class=""
                        >{{ avatarText(item?.raw?.shortName) }}</small>
                      </VAvatar>
                      <span class="ms-2">{{ item.raw.shortName }}</span>
                    </VChip>
                  </template>

                  <template #item="{ props, item }">
                    <VListItem v-bind="{ ...props, title: '' }">
                      <VAvatar
                        color="primary"
                        :image="item?.raw?.avatar ? getImageUrl(item?.raw?.avatar?.path) : undefined"
                        :variant="item?.raw?.avatar ? undefined : 'tonal'"
                        size="38"
                      >
                        <span v-if="!item?.raw?.avatar">{{ avatarText(item?.raw?.shortName) }}</span>
                      </VAvatar>
                      <span class="ms-2">{{ item.raw.shortName }}</span>
                    </VListItem>
                  </template>
                </AppAutocomplete>
              </VCol>

              <!-- 👉 Description -->
              <VCol
                v-if="!event.extendedProps.isTask"
                cols="12"
              >
                <AppTextarea
                  v-model="event.extendedProps.description"
                  label="Description"
                  placeholder="Meeting description"
                />
              </VCol>

              <!-- 👉 Form buttons -->
              <VCol
                v-if="!event.extendedProps.isTask"
                cols="12"
              >
                <VBtn
                  type="submit"
                  class="me-3 custom-btn-style"
                  :disabled="props.getLoadStatus === 1"
                >
                  <span v-if="props.getLoadStatus === 1">
                    <VProgressCircular
                      :size="16"
                      width="3"
                      indeterminate
                    />
                    Loading...
                  </span>
                  <span v-else>
                    {{ event.id ? 'Update' : 'Create' }} Event
                  </span>
                </VBtn>
                <VBtn
                  variant="outlined"
                  color="secondary"
                  class="custom-secondary-btn"
                  @click="onCancel"
                >
                  Cancel
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        <!-- !SECTION -->
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>


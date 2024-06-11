<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'
import { useToast } from "vue-toastification"
import moment from 'moment'

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
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'addEvent',
  'updateEvent',
  'removeEvent',
  'refreshCalendar',
])

const formatDate = date => {
  return moment(date).format('YYYY-MM-DD HH:mm')
}

const toast = useToast()

const refForm = ref()

// 👉 Event
const event = ref(JSON.parse(JSON.stringify(props.event)))

const resetEvent = () => {
  event.value = JSON.parse(JSON.stringify(props.event))
  nextTick(() => {
    refForm.value?.resetValidation()
  })
}

watch(() => props.isDrawerOpen, resetEvent)

const removeEvent = () => {
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

const handleSubmit = () => {
  refForm.value?.validate().then(async ({ valid }) => {
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

      // If id exist on id => Update event
      if ('id' in event.value){
        emit('updateEvent', eventDetails.value)
        toast.success('Event Updated successfully', { timeout: 1000 })
      }
      else{
        emit('addEvent', eventDetails.value)
        toast.success('Event added successfully', { timeout: 1000 })
      }
      emit('refreshCalendar')
      emit('update:isDrawerOpen', false)
    }
  })
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
  const config = {
    dateFormat: `m/d/Y H:i`,
  }

  if (event.value.end)
    config.maxDate = event.value.end

  return config
})

const endDateTimePickerConfig = computed(() => {
  const config = {
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
      :title="event.id ? 'Update Event' : 'Add Event'"
      @cancel="$emit('update:isDrawerOpen', false)"
    >
      <template #beforeClose>
        <IconBtn
          v-show="event.id"
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
                  label="Title"
                  placeholder="Meeting with Jane"
                  :rules="[requiredValidator]"
                />
              </VCol>

              <!-- 👉 Start date -->
              <VCol cols="12">
                <AppDateTimePicker
                  :key="JSON.stringify(startDateTimePickerConfig)"
                  v-model="event.start"
                  :rules="[requiredValidator]"
                  label="Start date"
                  placeholder="Select Date"
                  :config="startDateTimePickerConfig"
                />
              </VCol>

              <!-- 👉 End date -->
              <VCol cols="12">
                <AppDateTimePicker
                  :key="JSON.stringify(endDateTimePickerConfig)"
                  v-model="event.end"
                  label="Due date"
                  placeholder="Select Date"
                  :config="endDateTimePickerConfig"
                />
              </VCol>

              <!-- 👉 Event URL -->
              <VCol cols="12">
                <AppTextField
                  v-model="event.url"
                  label="Event URL"
                  placeholder="https://event.com/meeting"
                  :rules="[urlValidator]"
                  type="url"
                />
              </VCol>

              <!-- 👉 Guests -->
              <VCol cols="12">
                <AppSelect
                  v-model="event.extendedProps.guests"
                  label="Members"
                  placeholder="Select Members"
                  :items="props.getProjectGuests"
                  :item-title="item => item.name"
                  :item-value="item => item.id"
                  chips
                  multiple
                  eager
                />
              </VCol>

              <!-- 👉 Description -->
              <VCol cols="12">
                <AppTextarea
                  v-model="event.extendedProps.description"
                  label="Description"
                  placeholder="Meeting description"
                />
              </VCol>

              <!-- 👉 Form buttons -->
              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-3"
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
                    Submit
                  </span>
                </VBtn>
                <VBtn
                  variant="outlined"
                  color="secondary"
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

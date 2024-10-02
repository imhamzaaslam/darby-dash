<!-- eslint-disable camelcase -->
<script setup>
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useUserSettingStore } from "@/store/user_settings"

const toast = useToast()
const userSettingStore = useUserSettingStore()

const managementTypes = ref([])
const selectedManagementType = ref(null)

const getNotificationSettings = computed(() => userSettingStore.getNotificationSettings)

const fetchNotificationSettings = async () => {
  try {
    await userSettingStore.getAllNotifications()

    const apiResponse = getNotificationSettings.value

    if (apiResponse && Object.keys(apiResponse).length > 0) {
      managementTypes.value = Object.keys(apiResponse).map(managementType => ({
        title: capitalizeFirstLetter(managementType),
        notifications: apiResponse[managementType].map(notification => ({
          id: notification.id,
          type: notification.type,
          deliverableChannel: notification.deliverableChannel,
        })),
      }))

      selectedManagementType.value = managementTypes.value[0]
    } else {
      toast.error('No settings available.')
    }
  } catch (error) {
    toast.error(`Error fetching settings: ${error.message}`)
  }
}

const capitalizeFirstLetter = string => {
  return string.charAt(0).toUpperCase() + string.slice(1)
}

const updateNotificationChannel = notification => {
  const { id, deliverableChannel } = notification

  if (id && deliverableChannel !== undefined) {
    userSettingStore.updateNotificationSetting(id, { deliverable_channel: deliverableChannel })
      .then(() => {
        toast.success('Notification channel updated successfully.')
      })
      .catch(error => {
        toast.error(`Error updating notification channel: ${error.message}`)
      })
  } else {
    toast.error('Invalid notification data.')
  }
}

const selectManagement = managementTitle => {
  selectedManagementType.value = managementTypes.value.find(type => type.title === managementTitle) || managementTypes.value[0]
}

onBeforeMount(fetchNotificationSettings)
</script>

<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Notification Settings</VCardTitle>
      <p class="text-sm mt-2 mb-0">
        Manage your preferences for receiving notifications and alerts.
      </p>
    </VCardItem>

    <VCardText>
      <div class="d-flex flex-wrap gap-1 mb-4">
        <VChip
          v-for="management in managementTypes"
          :key="management.title"
          :class="{ 'v-chip--active': selectedManagementType?.title === management.title }"
          class="cursor-pointer"
          color="primary"
          @click="selectManagement(management.title)"
        >
          {{ management.title }} Alerts
        </VChip>
      </div>

      <VTable
        class="text-no-wrap mt-4"
        density="compact"
        :style="{ border: '1px solid #e0e0e0' }"
      >
        <thead>
          <tr>
            <th style="width: 65%;">
              Notification Type
            </th>
            <th style="width: 35%;">
              Deliverable Channel
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="notification in selectedManagementType?.notifications"
            :key="notification.type"
          >
            <td>{{ notification.type }}</td>
            <td>
              <div class="d-flex align-items-center">
                <VRadioGroup
                  v-model="notification.deliverableChannel"
                  inline
                  @change="updateNotificationChannel(notification)"
                >
                  <VRadio
                    label="Email"
                    value="mail"
                    class="me-2"
                    density="compact"
                  />
                  <VRadio
                    label="Push"
                    value="database"
                    class="me-2"
                    density="compact"
                  />
                  <VRadio
                    label="Both"
                    value="both"
                    class="me-2"
                    density="compact"
                  />
                  <VRadio
                    label="None"
                    value="null"
                    density="compact"
                  />
                </VRadioGroup>
              </div>
            </td>
          </tr>
        </tbody>
      </VTable>
    </VCardText>
    <VDivider />
  </VCard>
</template>

<style scoped>
.cursor-pointer {
  transition: background-color 0.3s;
}

.v-chip--active {
  background-color: #a12592;
  color: white !important;
}

.v-chip {
  transition: transform 0.2s;
}

.v-chip:hover {
  transform: scale(1.03);
}

.v-table {
  border-radius: 8px;
  overflow: hidden;
}
</style>

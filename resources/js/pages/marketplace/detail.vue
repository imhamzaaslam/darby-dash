<template>
  <!-- Title Section -->
  <div class="header-section">
    <div class="d-flex align-center">
      <VAvatar
        icon="tabler-building-store"
        size="36"
        class="me-2"
        color="primary"
        variant="tonal"
      />
      <h3 class="text-primary">
        {{ userDetails?.company }} Marketplace
      </h3>
    </div>
    <p class="text-body-2 mt-1">
      <strong>Active Project Type:</strong> {{ service?.service_type }}
    </p>
  </div>
  
  <!-- Main Service Section -->
  <VRow class="align-stretch mt-3">
    <!-- Image Section -->
    <VCol
      cols="12"
      md="3"
    >
      <VCard class="rounded-lg">
        <VImg
          :src="service?.image ? getImageUrl(service?.image?.path) : placeholderImg"
          class="related-image"
          height="200"
          cover
          gradient="to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.3)"
        />
        <VCardText class="pa-4">
          <strong class="text-h6 text-primary font-weight-bold mb-2 d-block">Server Stats</strong>
          <VDivider
            color="primary"
            class="mb-3"
          />
          <div class="stats-text d-flex flex-column align-items-start">
            <div class="d-flex align-items-center w-100 pb-2 border-bottom">
              <VIcon
                class="text-primary me-2"
                icon="tabler-circle-check"
              />
              <strong>Uptime:</strong> <span class="ms-2">99.9%</span>
            </div>
            <div class="d-flex align-items-center w-100 pb-2 border-bottom">
              <VIcon
                class="text-primary me-2"
                icon="tabler-circle-check"
              />
              <strong>Requests:</strong> <span class="ms-2">1200/hr</span>
            </div>
            <div class="d-flex align-items-center w-100 pb-2 border-bottom">
              <VIcon
                class="text-primary me-2"
                icon="tabler-circle-check"
              />
              <strong>Response Time:</strong> <span class="ms-2">250ms</span>
            </div>
            <div class="mt-3 text-center text-primary text-h6 w-100">
              $10/month
            </div>
            <div class="d-flex mt-4 justify-center w-100">
              <VBtn
                size="small"
                variant="elevated"
                rounded="pill"
                color="primary"
              >
                Add to Project
              </VBtn>
            </div>
          </div>
        </VCardText>
      </VCard>
    </VCol>
  
    <!-- Title and Description Section -->
    <VCol
      cols="12"
      md="9"
    >
      <VCard
        class="rounded-lg pa-6"
        style="height: 440px;"
      >
        <VCardTitle class="text-h5 font-weight-bold text-primary">
          {{ service?.title || "Service Title" }}
        </VCardTitle>
        <VCardText>
          <p
            class="text-body-1 text-secondary"
            v-html="service?.description || ''"
          />
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
  
  <!-- Related Services Section -->
  <div class="related-section">
    <h4 class="text-h5 font-weight-bold text-primary">
      Related Services
    </h4>
    <VRow
      class="mt-4"
      dense
    >
      <VCol v-if="getRelatedServices ? getRelatedServices.length === 0 : 0">
        <p class="text-h6 text-center">
          No related services found.
        </p>
      </VCol>
    </VRow>
    <VRow
      class="mt-4"
      dense
    >
      <VCol
        v-for="related in getRelatedServices"
        :key="related.id"
        cols="12"
        sm="12"
        md="3"
      >
        <VCard
          class="elevation-3 hover-card rounded-sm overflow-hidden"
          style="height: 335px;"
        >
          <!-- Service Image -->
          <VImg
            :src="related?.image ? getImageUrl(related?.image?.path) : placeholderImg"
            class="related-image"
            height="200"
            cover
            gradient="to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.3)"
          >
            <div class="text-white px-3 text-h5 font-weight-bold related-title-overlay">
              {{ related.title || "Related Service Title" }}
            </div>
          </VImg>

          <!-- Card Content -->
          <VCardText class="pa-4">
            <p 
              class="text-body-2 text-high-emphasis mb-0 text-align-between" 
              v-html="truncateDescription(related.description, 80)" 
            />
          </VCardText>

          <!-- Card Actions -->
          <VCardActions class="justify-center pb-4">
            <VBtn
              color="primary"
              variant="elevated"
              rounded="pill"
              size="small"
              :to="{ name: 'marketplace-service-detail', params: { id: related.uuid } }"
              target="_blank"
            >
              Learn More
            </VBtn>
          </VCardActions>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>
  
<script setup lang="js">
import { computed, watch, onBeforeMount } from "vue"
import placeholderImg from '@images/pages/servicePlaceholder.png'
import { useRoute } from "vue-router"
import { useUserSettingStore } from "@/store/user_settings"
import { useUserStore } from "@/store/users"
import { VDivider } from "vuetify/lib/components/index.mjs"
  
const userSettingStore = useUserSettingStore()
const userStore = useUserStore()
const $route = useRoute()
  
const serviceUuid = $route.params.id
  
const service = computed(() => userSettingStore.getProjectService)
  
const getRelatedServices = computed(() =>
  userSettingStore.getProjectServicesByType.filter(
    service => service.uuid !== serviceUuid,
  ),
)
  
const fetchService = async () => {
  try {
    await userSettingStore.showService(serviceUuid)
  } catch (error) {
    console.error("Error fetching service:", error)
  }
}
  
const fetchRelatedServices = async () => {
  try {
    await userSettingStore.getServicesByType(service?.value?.project_type_uuid)
  } catch (error) {
    console.error("Error fetching related services:", error)
  }
}
  
const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL
  
  return `${baseUrl}storage/${path}`
}

const truncateDescription = (description, length) => {
  return description.length > length
    ? description.slice(0, length) + '...'
    : description
}

const userDetails = computed(() => {
  return userStore.getUser
})
  
onBeforeMount(async () => {
  await fetchService()
  await fetchRelatedServices()
})
</script>
  
  <style scoped>
 .related-image { 
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
}
  
  .header-section {
    margin-bottom: 20px;
  }
  
  .related-section {
    margin-top: 40px;
  }
  
  .stats-text {
    font-size: 14px;
    line-height: 1.5;
  }
  .related-title-overlay {
    position: absolute;
    bottom: 8px;
    left: 16px;
    right: 16px;
    z-index: 1;
    text-align: center;
  }
  </style>
  
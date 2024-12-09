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
    <p class="text-body-1 text-muted mt-1">
      Explore a wide range of services offered by {{ userDetails?.company }} to meet all your needs.
    </p>
  </div>
  
  <!-- Related Services Section -->
  <div class="related-section">
    <VRow
      v-if="getServices ? getServices.length === 0 : 0"
      class="mt-4"
      dense
    >
      <VCol>
        <p class="text-h6 text-center">
          No services found.
        </p>
      </VCol>
    </VRow>
    <VRow
      v-else
      class=""
      dense
    >
      <VCol
        v-for="service in getServices"
        :key="service.id"
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
            :src="service.image ? getImageUrl(service?.image?.path) : placeholderImg"
            class="related-image"
            height="200"
            cover
            gradient="to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.3)"
          >
            <div class="text-white px-3 text-h5 font-weight-bold related-title-overlay">
              {{ service.title || "Related Service Title" }}
            </div>
          </VImg>

          <!-- Card Content -->
          <VCardText class="pa-4">
            <p 
              class="text-body-2 text-high-emphasis mb-0 text-align-between" 
              v-html="truncateDescription(service.description, 80)" 
            />
          </VCardText>

          <!-- Card Actions -->
          <VCardActions class="justify-center pb-4">
            <VBtn
              color="primary"
              variant="elevated"
              rounded="pill"
              size="small"
              :to="{ name: 'marketplace-service-detail', params: { id: service.uuid } }"
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
import { useUserSettingStore } from "@/store/user_settings"
import { useUserStore } from "@/store/users"
  
const userSettingStore = useUserSettingStore()
const userStore = useUserStore()
  
const getServices = computed(() =>
  userSettingStore.getProjectServicesWithoutPagination,
)
  
const fetchServices = async () => {
  try {
    await userSettingStore.getServicesWithoutPagination()
  } catch (error) {
    console.error("Error fetching services:", error)
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
  await fetchServices()
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
  
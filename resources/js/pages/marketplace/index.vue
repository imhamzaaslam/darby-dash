<template>
  <!-- Title Section -->
  <div class="header-section">
    <div class="d-flex align-center">
      <VAvatar
        icon="tabler-world"
        size="36"
        class="me-2"
        color="primary"
        variant="tonal"
      />
      <!-- <VAvatar
        v-if="project?.project_logo"
        size="36"
        class="me-2"
      >
        <VImg :src="project?.project_logo" /> 
      </VAvatar>
      <VAvatar
        v-else
        icon="tabler-building-store"
        size="36"
        class="me-2"
        color="primary"
        variant="tonal"
      /> -->
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
      v-if="getServices.length === 0"
      dense
    >
      <VCol
        cols="12"
        class="d-flex flex-column align-center justify-center text-center" 
      >
        <span v-html="emptyServices" />
        <span class="">No services found.</span>
      </VCol>
    </VRow>
    
    <VRow
      v-else
      class="mt-4"
      dense
    >
      <VCol
        v-for="service in getServices"
        :key="service.id"
        cols="12"
        sm="12"
        md="3"
        class="pe-3 pb-3"
      >
        <VCard
          class="elevation-3 hover-card rounded-sm overflow-hidden"
          style="height: 280px;"
          @click.stop="showServiceDetails(service)"
        >
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

          <VCardActions class="justify-center pt-6">
            <VBtn
              color="primary"
              variant="elevated"
              rounded="pill"
              size="small"
              @click.stop="showServiceDetails(service)"
            >
              Read More
            </VBtn>
          </VCardActions>
        </VCard>
      </VCol>
    </VRow>
  </div>

  <!-- Overlay for Service Details -->
  <VDialog
    v-model="isOverlayVisible"
    max-width="800px"
    persistent
  >
    <DialogCloseBtn @click="cancelServiceDetails" />
    <VCard
      class="overflow-hidden"
      style="height: 300px !important"
    >
      <VRow>
        <VCol
          cols="12"
          md="6"
        >
          <VImg
            :src="selectedService?.image ? getImageUrl(selectedService?.image?.path) : placeholderImg"
            height="100%"
            width="100%"
            cover
          />
        </VCol>
        <VCol
          cols="12"
          md="6"
        >
          <VCardText>
            <h3 class="text-primary">
              {{ selectedService?.title }}
            </h3>
            <p 
              class="text-body-2 text-high-emphasis mt-2 mb-0 text-align-between" 
              v-html="truncateDescription(selectedService?.description, 300)" 
            />
            <VBtn
              color="primary"
              variant="elevated"
              rounded="pill"
              size="small"
              @click="goToServiceDetail(selectedService.uuid)"
            >
              Learn more <VIcon
                icon="tabler-arrow-right"
                class="ms-2"
              />
            </VBtn>
          </VCardText>
        </VCol>
      </VRow>
    </VCard>
  </VDialog>
</template>

<script setup lang="js">
import { computed, ref, watch, onBeforeMount } from "vue"
import emptyServices from '@images/darby/projects_list.svg?raw'
import placeholderImg from '@images/pages/servicePlaceholder.png'
import { useUserSettingStore } from "@/store/user_settings"
import { useUserStore } from "@/store/users"
import { useProjectStore } from "../../store/projects"
import { useRouter } from 'vue-router'

const userSettingStore = useUserSettingStore()
const userStore = useUserStore()
const projectStore = useProjectStore()
const router = useRouter()

const getServices = computed(() => userSettingStore.getProjectServicesWithoutPagination)
const isOverlayVisible = ref(false)
const selectedService = ref(null)

const projectId = computed(() => router.params.id)

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
  return description?.length > length ? description.slice(0, length) + '...' : description
}

const showServiceDetails = service => {
  selectedService.value = service
  isOverlayVisible.value = true
}

const cancelServiceDetails = () => {
  isOverlayVisible.value = false
}

const userDetails = computed(() => userStore.getUser)

const goToServiceDetail = uuid => {
  isOverlayVisible.value = false
  router.push({ name: 'marketplace-service-detail', params: { id: uuid } })
} 

const project = computed(() =>{
  return projectStore.getProject
})

const fetchProject = async () => {
  try {
    await projectStore.show(projectId.value)
    isLoading.value = true
  } catch (error) {
    toast.error('Error fetching project:', error)
  }
  finally {
    isLoading.value = false
  }
}

onBeforeMount(async () => {
  await fetchServices()
  await fetchProject()
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

.related-title-overlay {
  position: absolute;
  bottom: 8px;
  left: 16px;
  right: 16px;
  z-index: 1;
  text-align: center;
}
</style>

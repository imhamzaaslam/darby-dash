<template>
  <div>
    <VRow class="pb-0">
      <VCol
        cols="12"
        md="9"
        class="d-flex pb-0"
      >
        <div>
          <div class="d-flex align-center">
            <VAvatar
              icon="tabler-settings"
              size="36"
              class="me-2"
              color="primary"
              variant="tonal"
            />
            <h3 class="text-primary">
              Manage Services
            </h3>
          </div>
          <p class="text-body-1 text-muted mt-1">
            Efficiently manage and organize the services offered by {{ userDetails?.company }} to enhance operations and deliver exceptional results.
          </p>
        </div>
      </VCol>
      <VCol
        cols="12"
        md="3"
      >
        <div class="d-flex flex-row align-center justify-end">
          <VBtnToggle
            v-model="viewType"
            class="d-toggle"
            rounded="0"
          >
            <VIcon
              icon="tabler-list"
              class="me-1"
              :class="{ 'bg-primary': viewType === 'list' }"
              @click="viewType = 'list'"
            />
            <VIcon
              icon="tabler-layout-grid"
              :class="{ 'bg-primary': viewType === 'grid' }"
              @click="viewType = 'grid'"
            />
          </VBtnToggle>
          <VBtn
            icon
            color="td-hover"
            class="ms-2"
            size="small"
            rounded="pills"
            @click.prevent
          >
            <VIcon icon="tabler-dots" />
            <VMenu activator="parent">
              <VList>
                <VListItem
                  value="add-service"
                  @click="isAddServiceDrawerOpen = !isAddServiceDrawerOpen"
                >
                  Add Service
                </VListItem>
                <VListItem
                  value="sort-service"
                  @click="isSortServiceModalOpen = true"
                >
                  Manage Services
                </VListItem>
              </VList>
            </VMenu>
          </VBtn>
        </div>
      </VCol>
    </VRow>
    <!-- Skeleton Loader -->
    <div v-if="getLoadStatus == 1">
      <VRow
        v-if="viewType === 'list'"
        class="mb-4"
      >
        <ListViewSkeleton />
      </VRow>
      <VRow
        v-else
        class="mb-4"
      >
        <GridViewSkeleton />
      </VRow>
    </div>

    <div v-else>
      <VRow v-if="getServices.length === 0">
        <VCol
          cols="12"
          class="d-flex flex-column align-center justify-center text-center" 
        >
          <span v-html="emptyServices" />
          <span class="">No services found.</span>
        </VCol>
      </VRow>
      <VRow v-else-if="viewType === 'list'">
        <VCol
          v-for="service in getServices"
          :key="service.id"
          cols="12"
        >
          <VCard
            class="d-flex ps-4 py-1 list-side-border"
            @click.stop="editService(service)"
          >
            <VCol cols="5">
              <div class="d-flex align-center gap-x-3">
                <VAvatar
                  v-if="service.image"
                  rounded
                  size="50"
                  class="me-3"
                  :image="getImageUrl(service.image.path)"
                />
                <VAvatar
                  v-else
                  rounded
                  size="50"
                  class="me-3"
                  :image="placeholderImg"
                />
                <div>
                  <h6 class="text-h6 text-no-wrap">
                    <span class="d-block">{{ service.title }}</span>
                  </h6>
                </div>
              </div>
            </VCol>
            <VCol cols="2">
              <div class="d-flex flex-column ms-3">
                <span class="d-block font-weight-bold text-high-emphasis text-sm text-truncate">Type</span>
                <small>{{ service.service_type }}</small>
              </div>
            </VCol>
            <VCol cols="2">
              <div class="d-flex flex-column ms-3">
                <span class="d-block font-weight-bold text-high-emphasis text-sm text-truncate">Status</span>
                <div class="d-flex flex-row flex-wrap">
                  <VTooltip location="top">
                    <template #activator="{ props }">
                      <VChip
                        v-bind="props"
                        size="small"
                        :color="service.status ? 'primary' : 'error'"
                      >
                        {{ service.status ? 'Active' : 'Inactive' }}
                      </VChip>
                    </template>
                    <span>{{ service.status ? 'Active' : 'Inactive' }}</span>
                  </VTooltip>
                </div>
              </div>
            </VCol>

            <VCol cols="2">
              <div class="d-flex flex-column ms-3">
                <span class="d-block font-weight-bold text-high-emphasis text-sm text-truncate text-center">Created At</span>
                <small class="text-center">{{ formatDate(service.created_at) }}</small>
              </div>
            </VCol>

            <VCol
              cols="1"
              class="d-flex justify-end"
            >
              <IconBtn @click.prevent>
                <VIcon icon="tabler-dots" />
                <VMenu activator="parent">
                  <VList>
                    <VListItem
                      value="edit"
                      @click="editService(service)"
                    >
                      Edit
                    </VListItem>
                    <VListItem
                      value="delete"
                      @click="deleteService(service)"
                    >
                      Delete
                    </VListItem>
                  </VList>
                </VMenu>
              </IconBtn>
            </VCol>
          </VCard>
        </VCol>
      </VRow>
      <VRow v-else>
        <VCol
          v-for="service in getServices"
          :key="service.id"
          cols="12"
          md="4"
        >
          <VCard @click.stop="editService(service)">
            <VCardTitle>
              <!-- Action Icon for Edit/Delete -->
              <div class="d-flex justify-space-between align-center">
                <!-- Status Chip with Tooltip -->
                <VTooltip location="top">
                  <template #activator="{ props }">
                    <VChip
                      v-bind="props"
                      size="small"
                      :color="service.status ? 'primary' : 'error'"
                      class="me-3"
                    >
                      {{ service.status ? 'Active' : 'Inactive' }}
                    </VChip>
                  </template>
                  <span>{{ service.status ? 'Service is active' : 'Service is inactive' }}</span>
                </VTooltip>

                <IconBtn @click.prevent>
                  <VIcon icon="tabler-dots-vertical" />
                  <VMenu activator="parent">
                    <VList>
                      <VListItem
                        value="edit"
                        @click="editService(service)"
                      >
                        Edit
                      </VListItem>
                      <VListItem
                        value="delete"
                        @click="deleteService(service)"
                      >
                        Delete
                      </VListItem>
                    </VList>
                  </VMenu>
                </IconBtn>
              </div>
            </VCardTitle>
            <VCardText class="ps-4">
              <!-- Avatar and Service Info -->
              <div class="d-flex align-center">
                <VAvatar
                  v-if="service.image"
                  rounded
                  size="80"
                  class="me-5"
                  :image="getImageUrl(service.image.path)"
                />
                <VAvatar
                  v-else
                  rounded
                  size="80"
                  class="me-5"
                  :image="placeholderImg"
                />
                <div class="d-flex flex-column">
                  <h6 class="text-h6 text-no-wrap">
                    {{ service.title }}
                  </h6>
                  <small class="text-muted">
                    <span class="font-weight-bold text-black">Type:</span> {{ service.service_type }}
                  </small>
                  <small class="text-muted">
                    <span class="font-weight-bold text-black">Created At:</span> {{ formatDate(service.created_at) }}
                  </small>
                </div>
              </div>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>
    </div>

    <TablePagination
      v-if="getLoadStatus !== 1 && getServices.length > 0"
      v-model:page="options.page"
      :items-per-page="options.itemsPerPage"
      :total-items="totalServices"
      class="custom-pagination"
      @update:page="handlePageChange"
    />
  </div>

  <AddServiceDrawer
    v-model:is-add-service-drawer-open="isAddServiceDrawerOpen"
    :fetch-services="fetchServices"
    :get-project-types="getProjectTypes"
    :get-load-status="getLoadStatus"
  />

  <EditServiceDrawer
    v-model:is-edit-service-drawer-open="isEditServiceDrawerOpen"
    :fetch-services="fetchServices"
    :edit-service-details="editServiceDetails"
    :get-project-types="getProjectTypes"
    :get-load-status="getLoadStatus"
  />
  <SortServiceModal
    v-model:is-sort-service-modal-open="isSortServiceModalOpen"
    :get-services-without-pagination="getServicesWithoutPagination"
    :fetch-services-without-pagination="fetchServicesWithoutPagination"
    :fetch-services="fetchServices"
    :get-load-status="getLoadStatus"
  />
</template>

<script setup>
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'
import placeholderImg from '@images/pages/servicePlaceholder.png'
import emptyServices from '@images/darby/projects_list.svg?raw'
import ListViewSkeleton from '@/pages/projects/web-designs/_partials/list-view-skeleton.vue'
import GridViewSkeleton from '@/pages/teams/_partials/grid-view-skeleton.vue'
import AddServiceDrawer from '@/pages/settings/services/_partials/add-service-drawer.vue'
import EditServiceDrawer from '@/pages/settings/services/_partials/update-service-drawer.vue'
import SortServiceModal from '@/pages/settings/services/_partials/sort-service-modal.vue'
import { computed, onBeforeMount, onMounted, onUnmounted, watch, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useUserSettingStore } from '@/store/user_settings'
import { useUserStore } from '@/store/users'
import { useProjectTypeStore } from "@/store/project_types"
import moment from 'moment'

useHead({ title: `${layoutConfig.app.title} | Manage Services` })

const isMobile = () => {
  return window.innerWidth <= 768 || window.innerWidth <= 926
}

const handleResize = () => {
  viewType.value = isMobile() ? 'grid' : 'list'
}

onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})
onBeforeMount(async () => {
  const searchParams = new URLSearchParams(window.location.search)
  const savedViewType = searchParams.get('view')
  if (savedViewType && ['list', 'grid'].includes(savedViewType)) {
    viewType.value = savedViewType
  }
  await fetchServices()
  await fetchServicesWithoutPagination()
  totalRecords.value = totalServices.value
})

const toast = useToast()
const userSettingStore = useUserSettingStore()
const userStore = useUserStore()
const projectTypeStore = useProjectTypeStore()
const router = useRouter()

const totalRecords = ref(0)
const isLoading = ref(false)
const isEditServiceDrawerOpen = ref(false)
const isAddServiceDrawerOpen = ref(false)
const isSortServiceModalOpen = ref(false)
const editServiceDetails = ref({})
const search = ref('')
const viewType = ref('list')
const options = ref({ page: 1, itemsPerPage: 10, orderBy: '', order: '' })

const formatDate = date => moment(date).format('MM/DD/YYYY')

const fetchServices = async () => {
  try {
    isLoading.value = true
    await userSettingStore.getAllServices(options.value.page, options.value.itemsPerPage, search.value)
  } catch (error) {
    toast.error('Error fetching services:', error)
  } finally {
    isLoading.value = false
  }
}

const fetchServicesWithoutPagination = async () => {
  try {
    await userSettingStore.getServicesWithoutPagination()
  } catch (error) {
    toast.error('Error fetching services:', error)
  }
}

const editService = service => {
  editServiceDetails.value = { ...service }
  isEditServiceDrawerOpen.value = true
}

const deleteService = async service => {
  try {
    const confirmDelete = await Swal.fire({
      title: `Are you certain about deleting ${service.title} service?`,
      text: `Once it’s gone, it’s gone.`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "rgba(var(--v-theme-primary))",
      cancelButtonColor: "#808390",
      confirmButtonText: "Yes, delete it!",
      didOpen: () => {
        document.querySelector('.swal2-confirm').blur()

        const title = document.querySelector('.swal2-title')
        if (title) {
          title.style.fontSize = '18px'
        }

        // Apply custom styles to text
        const text = document.querySelector('.swal2-html-container')
        if (text) {
          text.style.marginTop = '8px'
        }
      },
    })

    if (confirmDelete.isConfirmed) {

      const res = await userSettingStore.deleteService(service.uuid)
      const errors = getErrors.value
      if(errors)
      {
        toast.error('Something went wrong.')
      }
      else{
        isLoading.value = true
        toast.success('Service deleted successfully', { timeout: 1000 })
        await fetchServices()
        isLoading.value = false
      }
    }
  } catch (error) {
    toast.error('Failed to delete service:', error.message || error)
  }
}

const handlePageChange = async page => {
  options.value.page = page
  await fetchServices()
}

const getServices = computed(() => {
  return userSettingStore.getProjectServices
})

const getErrors = computed(() => {
  return userSettingStore.getErrors
})

const getServicesWithoutPagination = computed(() => {
  return userSettingStore.getProjectServicesWithoutPagination
})

const getLoadStatus = computed(() => {
  return userSettingStore.getLoadStatus
})

const totalServices = computed(() => {
  return userSettingStore.totalServicesCount
})

const getImageUrl = path => {
  const baseUrl = import.meta.env.VITE_APP_URL

  return `${baseUrl}storage/${path}`
}

const getProjectTypes = computed(() => {
  return projectTypeStore.getProjectTypes
})

const userDetails = computed(() => {
  return userStore.getUser
})

watch([viewType], ([newViewType]) => {
  router.push({
    query: {
      view: newViewType,
    },
  })
})
</script>

  <style scoped>
  .d-toggle{
      border: unset !important;
      padding: 0 !important;
      align-items: unset !important;
      block-size: unset !important;
  }

  .custom-pagination :deep(hr) {
    display: none !important;
  }
  </style>

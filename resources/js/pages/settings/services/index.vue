<template>
  <div>
    <VRow class="mt-0">
      <VCol
        cols="6"
        class="ps-4"
      >
        <h3>Manage Services</h3>
      </VCol>
      <VCol cols="6">
        <div class="d-flex justify-end mb-3">
          <VBtn
            icon
            color="td-hover"
            class="ms-3"
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
      <VRow class="mb-4">
        <ListViewSkeleton />
      </VRow>
    </div>

    <div v-else>
      <VRow v-if="getServices.length === 0">
        <VCol cols="12">
          <VCard class="px-3 py-3 text-center">
            <span>No Services found</span>
          </VCard>
        </VCol>
      </VRow>
      <VRow>
        <VCol
          v-for="service in getServices"
          :key="service.id"
          cols="12"
        >
          <VCard
            class="d-flex ps-4 py-1 list-side-border"
            @click="editService(service)"
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
import Swal from 'sweetalert2'
import placeholderImg from '@images/pages/servicePlaceholder.png'
import ListViewSkeleton from '@/pages/projects/web-designs/_partials/list-view-skeleton.vue'
import AddServiceDrawer from '@/pages/settings/services/_partials/add-service-drawer.vue'
import EditServiceDrawer from '@/pages/settings/services/_partials/update-service-drawer.vue'
import SortServiceModal from '@/pages/settings/services/_partials/sort-service-modal.vue'
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useUserSettingStore } from '@/store/user_settings'
import { useProjectTypeStore } from "@/store/project_types"
import moment from 'moment'

useHead({ title: `${layoutConfig.app.title} | Manage Services` })
onBeforeMount(async () => {
  await fetchServices()
  await fetchServicesWithoutPagination()
  totalRecords.value = totalServices.value
})

const toast = useToast()
const userSettingStore = useUserSettingStore()
const projectTypeStore = useProjectTypeStore()

const totalRecords = ref(0)
const isLoading = ref(false)
const isEditServiceDrawerOpen = ref(false)
const isAddServiceDrawerOpen = ref(false)
const isSortServiceModalOpen = ref(false)
const editServiceDetails = ref({})
const search = ref('')
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
      confirmButtonColor: "#a12592",
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

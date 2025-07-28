<template>
  <div>
    <VRow class="mb-3">
      <VCol
        cols="12"
        md="9"
        class="d-flex pb-0"
      >
        <div>
          <div class="d-flex align-center">
            <VAvatar
              icon="tabler-align-box-bottom-center"
              size="36"
              class="me-2"
              color="primary"
              variant="tonal"
            />
            <h3 class="text-primary">
              Manage Companies
            </h3>
          </div>
          <p class="text-body-1 text-muted mt-1">
            Oversee and manage company operations and projects with ease, ensuring streamlined workflows and effective decision-making.
          </p>
        </div>
      </VCol>
      <VCol
        cols="12"
        md="3"
        class="pb-0"
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
          <VIcon
            icon="tabler-filter"
            class="bg-primary ms-2"
            @click="isFilterDrawerOpen = !isFilterDrawerOpen"
          />
          <VBtn
            icon
            color="td-hover"
            class="ma-2 custom-btn-style"
            size="small"
            rounded="pills"
            @click.prevent
          >
            <VIcon icon="tabler-dots" />
            <VMenu activator="parent">
              <VList>
                <VListItem
                  value="add-company"
                  :to="{ name: 'add-company-details' }"
                >
                  Add Company
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
      <VRow v-if="getCompanies.length === 0">
        <VCol cols="12">
          <VCard class="px-3 py-3 text-center">
            <span>No companies found</span>
          </VCard>
        </VCol>
      </VRow>
      <VRow v-else-if="viewType === 'list'">
        <VCol
          v-for="company in getCompanies"
          :key="company.id"
          cols="12"
        >
          <RouterLink :to="{ name: 'manage-company-settings', params: { id: company.uuid } }">
            <VCard class="d-flex ps-4 py-1 list-side-border">
              <VCol cols="4">
                <div class="d-flex align-center gap-x-3">
                  <VAvatar
                    :size="34"
                    :image="sketch"
                  />
                  <div>
                    <h6 class="text-h6 text-no-wrap">
                      <span class="d-block">{{ company.name }}
                        <VTooltip location="top">
                          <template #activator="{ props }">
                            <VBtn
                              v-bind="props"
                              :href="formattedUrl(company.url)"
                              target="_blank"
                              rel="noopener noreferrer"
                              icon="tabler-pin-invoke"
                              variant="text"
                              color="primary"
                              class="custom-btn-style"
                              @click.stop=""
                            />
                          </template>
                          <span>Open URL</span>
                        </VTooltip>
                      </span> 
                    </h6>
                  </div>
                </div>
              </VCol>
              <VCol cols="3">
                <div class="d-flex align-center justify-space-between">
                  <div class="d-flex align-center">
                    <VBadge
                      dot
                      location="top end"
                      offset-x="1"
                      offset-y="1"
                      :color="company?.admin?.is_online ? 'success' : 'warning'"
                    >
                      <VAvatar
                        size="32"
                        :class="company?.admin?.avatar ? '' : 'text-white bg-primary'"
                        :variant="!company?.admin?.avatar ? 'tonal' : ''"
                      >
                        <span class="text-sm">
                          {{ avatarText(company?.admin?.name_first + ' ' + company?.admin?.name_last) }}
                        </span>
                      </VAvatar>
                    </VBadge>
                    <div class="d-flex flex-column ms-3">
                      <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">
                        {{ company?.admin?.name_first }} {{ company?.admin?.name_last }}
                      </span>
                      <small class="mt-0 text-xs">
                        {{ company?.admin?.email }}
                      </small>
                    </div>
                  </div>
                </div>
              </VCol>
              <VCol
                cols="2"
                class="ms-2"
              >
                <VIcon
                  color="primary"
                  icon="tabler-phone"
                />
                <span class="ms-2">
                  {{ company?.admin?.info.phone }}
                </span>
              </VCol>
              <VCol cols="2">
                <div class="d-flex flex-column ms-3">
                  <span class="d-block font-weight-bold text-high-emphasis text-sm text-truncate">Created At</span>
                  <small>{{ formatDate(company.created_at) }}</small>
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
                        :to="{ name: 'manage-company-settings', params: { id: company.uuid } }"
                      >
                        Edit
                      </VListItem>
                    </VList>
                  </VMenu>
                </IconBtn>
              </VCol>
            </VCard>
          </RouterLink>
        </VCol>
      </VRow>

      <!-- Grid View -->
      <VRow v-else>
        <VCol
          v-for="company in getCompanies"
          :key="company.id"
          cols="12"
          md="4"
        >
          <RouterLink :to="{ name: 'manage-company-settings', params: { id: company.uuid } }">
            <VCard
              class="py-2 px-4"
              style="position: relative; overflow: visible;"
            >
              <VCardTitle>
                <VRow>
                  <VCol cols="12">
                    <!-- Top Section: Company Name and Status -->
                    <div class="d-flex align-center justify-space-between gap-x-3">
                      <div class="d-flex align-center gap-x-3">
                        <VAvatar
                          :size="34"
                          :image="sketch"
                        />
                        <div>
                          <h6 class="text-h6 text-no-wrap">
                            {{ company.name }} 
                            <VTooltip location="top">
                              <template #activator="{ props }">
                                <VBtn
                                  v-bind="props"
                                  :href="formattedUrl(company.url)"
                                  target="_blank"
                                  rel="noopener noreferrer"
                                  icon="tabler-pin-invoke"
                                  variant="text"
                                  color="primary"
                                  class="custom-btn-style"
                                  @click.stop=""
                                />
                              </template>
                              <span>Open URL</span>
                            </VTooltip>
                          </h6>
                        </div>
                      </div>
                      <VChip
                        :color="company.is_active ? 'primary' : 'error'"
                        variant="outlined"
                        size="small"
                      >
                        {{ company.is_active ? 'Active' : 'Inactive' }}
                      </VChip>
                    </div>

                    <!-- Divider -->
                    <VDivider
                      color="primary"
                      class="mt-2 mb-3"
                    />

                    <!-- Admin Details -->
                    <div class="d-flex align-center justify-space-between">
                      <div class="d-flex align-center">
                        <VBadge
                          dot
                          location="top end"
                          offset-x="1"
                          offset-y="1"
                          :color="company?.admin?.is_online ? 'success' : 'warning'"
                        >
                          <VAvatar
                            size="32"
                            :class="company?.admin?.avatar ? '' : 'text-white bg-primary'"
                            :variant="!company?.admin?.avatar ? 'tonal' : ''"
                          >
                            <span class="text-sm">
                              {{ avatarText(company?.admin?.name_first + ' ' + company?.admin?.name_last) }}
                            </span>
                          </VAvatar>
                        </VBadge>
                        <div class="d-flex flex-column ms-3">
                          <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">
                            {{ company?.admin?.name_first }} {{ company?.admin?.name_last }}
                          </span>
                          <small class="mt-0 text-xs">
                            Admin
                          </small>
                        </div>
                      </div>
                      <VBtn
                        class="ms-auto custom-btn-style"
                        color="primary"
                        size="x-small"
                        variant="elevated"
                        :to="{ name: 'manage-company-settings', params: { id: company.uuid } }"
                      >
                        View Details
                      </VBtn>
                    </div>
                    <div class="d-flex mt-5 align-center justify-space-between">
                      <small class="text-xs">
                        <strong><VIcon
                          color="primary"
                          icon="tabler-mail"
                        /></strong> <span class="text-sm ms-1 text-high-emphasis">{{ company?.admin?.email }}</span>
                      </small>
                    </div>
                    <div class="d-flex align-center mt-2 justify-space-between">
                      <small class="text-xs">
                        <strong><VIcon
                          color="primary"
                          icon="tabler-phone"
                        /></strong> <span class="text-sm ms-1 text-high-emphasis">{{ company?.admin?.info.phone }}</span>
                      </small>
                      <small class="text-xs">
                        <strong>Created:</strong> <span class="text-xs text-secondary">{{ formatDate(company.created_at) }}</span>
                      </small>
                    </div>
                  </VCol>
                </VRow>
              </VCardTitle>
            </VCard>
          </RouterLink>
        </VCol>
      </VRow>
    </div>

    <TablePagination
      v-if="getLoadStatus !== 1 && getCompanies.length > 0"
      v-model:page="options.page"
      :items-per-page="options.itemsPerPage"
      :total-items="totalCompanies"
      class="custom-pagination"
      @update:page="handlePageChange"
    />
  </div>
  <FilterDrawer
    v-model:is-filter-drawer-open="isFilterDrawerOpen"
    :apply-filters="applyFilters"
    :get-load-status="getLoadStatus"
  />
</template>

<script setup>
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import Swal from 'sweetalert2'
import sketch from '@images/icons/project-icons/sketch.png'
import FilterDrawer from '@/pages/super-admin/companies/_partials/filter-companies-drawer.vue'
import ListViewSkeleton from '@/pages/super-admin/companies/_partials/list-view-skeleton.vue'
import GridViewSkeleton from '@/pages/super-admin/companies/_partials/grid-view-skeleton.vue'
import { computed, onBeforeMount, onMounted, onUnmounted, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useCompanyStore } from "@/store/companies"
import { useAuthStore } from '@/store/auth'
import { useRoute } from 'vue-router'
import moment from 'moment'
import { VListItemAction } from 'vuetify/lib/components/index.mjs'

useHead({ title: `${layoutConfig.app.title} | Manage Companies` })
onBeforeMount(async () => {
  await fetchCompanies()
  totalRecords.value = totalCompanies.value
})

const toast = useToast()
const companyStore = useCompanyStore()
const authStore = useAuthStore()
const route = useRoute()

const totalRecords = ref(0)
const viewType = ref('grid')
const isFilterDrawerOpen = ref(false)
const isLoading = ref(false)
const search = ref('')
const options = ref({ page: 1, itemsPerPage: 10, orderBy: '', order: '' })

const formatDate = date => moment(date).format('MM/DD/YYYY')

const isMobile = () => {
  return window.innerWidth <= 768 || window.innerWidth <= 926
}

const handleResize = () => {
  viewType.value = isMobile() ?? 'grid'
}

onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})

const formattedUrl = url => {
  if (!/^https?:\/\//i.test(url)) {
    return 'http://' + url
  }
  
  return url
}

const fetchCompanies = async () => {
  try {
    isLoading.value = true
    await companyStore.getAll(options.value.page, options.value.itemsPerPage, search.value)
  } catch (error) {
    toast.error('Error fetching companies:', error)
  } finally {
    isLoading.value = false
  }
}

const applyFilters = async (searchQuery = '') => {
  search.value = searchQuery
  await fetchCompanies()
}

const handlePageChange = async page => {
  options.value.page = page
  await fetchCompanies()
}

const onFilter = async value => {
  options.value.page = 1
  await fetchCompanies()
}

const getCompanies = computed(() => {
  return companyStore.getCompanies
})

const getLoadStatus = computed(() => {
  return companyStore.getLoadStatus
})

const totalCompanies = computed(() => {
  return companyStore.companiesCount
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

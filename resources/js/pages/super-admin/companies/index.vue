<template>
  <div>
    <!-- Toggle -->
    <VRow class="mb-0">
      <VCol
        cols="12"
        md="10"
        class="d-flex pb-0"
      >
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
      </VCol>
      <VCol
        cols="12"
        md="2"
        class="pb-0"
      >
        <div class="d-flex justify-end">
          <VBtn
            prepend-icon="tabler-plus"
            @click="isAddCompanyDrawerOpen = !isAddCompanyDrawerOpen"
          >
            New Company
          </VBtn>
        </div>
      </VCol>
    </VRow>
    <VRow class="mt-0 pt-0">
      <VCol
        cols="12"
        class="pt-0 ps-4"
      >
        <h3>Manage Company</h3>
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
          <VCard class="d-flex ps-4 py-1 list-side-border">
            <VCol cols="8">
              <div class="d-flex align-center gap-x-3">
                <VAvatar
                  :size="34"
                  :image="sketch"
                />
                <div>
                  <h6 class="text-h6 text-no-wrap">
                    <span class="d-block">{{ company.name }}</span>
                  </h6>
                  <VChip
                    color="primary"
                    size="x-small"
                  >
                    <span class="text-high-emphasis text-xs">
                      {{ company?.name_first + ' ' + company?.name_last }} (Admin)
                    </span>
                  </VChip>
                </div>
              </div>
            </VCol>
            <VCol cols="3">
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
                      @click="editCompany(company)"
                    >
                      Edit
                    </VListItem>
                    <VListItem
                      value="delete"
                      @click="deleteCompany(company)"
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

      <!-- Grid View -->
      <VRow v-else>
        <VCol
          v-for="company in getCompanies"
          :key="company.id"
          cols="12"
          md="4"
        >
          <VCard
            class="pt-2"
            style="position: relative; overflow: visible;"
          >
            <VCardTitle>
              <VRow>
                <VCol cols="10">
                  <div class="d-flex align-center gap-x-3">
                    <VAvatar
                      :size="34"
                      :image="sketch"
                    />
                    <div>
                      <h6 class="text-h6 text-no-wrap">
                        {{ company.name }}
                      </h6>
                      <VChip
                        color="primary"
                        size="x-small"
                      >
                        <span class="text-high-emphasis text-xs">
                          {{ company?.name_first + ' ' + company?.name_last }} (Admin)
                        </span>
                      </VChip>
                    </div>
                  </div>
                </VCol>
                <VCol cols="2">
                  <IconBtn @click.prevent>
                    <VIcon icon="tabler-dots-vertical" />
                    <VMenu activator="parent">
                      <VList>
                        <VListItem
                          value="edit"
                          @click="editCompany(company)"
                        >
                          Edit
                        </VListItem>
                        <VListItem
                          value="delete"
                          @click="deleteCompany(company)"
                        >
                          Delete
                        </VListItem>
                      </VList>
                    </VMenu>
                  </IconBtn>
                </VCol>
              </VRow>
            </VCardTitle>
          </VCard>
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
  <AddCompanyDrawer
    v-model:is-drawer-open="isAddCompanyDrawerOpen"
    :fetch-companies="fetchCompanies"
    :get-errors="getErrors"
    :get-status-code="getStatusCode"
    :get-load-status="getLoadStatus"
  />
  <EditCompanyDrawer
    v-model:is-edit-drawer-open="isEditCompanyDrawerOpen"
    :fetch-companies="fetchCompanies"
    :get-errors="getErrors"
    :get-status-code="getStatusCode"
    :edit-company-details="editCompanyDetails"
    :get-load-status="getLoadStatus"
  />
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
import AddCompanyDrawer from '@/pages/super-admin/companies/_partials/add-company-drawer.vue'
import EditCompanyDrawer from '@/pages/super-admin/companies/_partials/update-company-drawer.vue'
import FilterDrawer from '@/pages/super-admin/companies/_partials/filter-companies-drawer.vue'
import ListViewSkeleton from '@/pages/super-admin/companies/_partials/list-view-skeleton.vue'
import GridViewSkeleton from '@/pages/super-admin/companies/_partials/grid-view-skeleton.vue'
import { computed, onBeforeMount, onMounted, onUnmounted, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useCompanyStore } from "@/store/companies"
import { useAuthStore } from '@/store/auth'
import { useRoute } from 'vue-router'
import moment from 'moment'

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
const viewType = ref('list')
const isAddCompanyDrawerOpen = ref(false)
const isEditCompanyDrawerOpen = ref(false)
const isFilterDrawerOpen = ref(false)
const isLoading = ref(false)
const editCompanyDetails = ref({})
const search = ref('')
const options = ref({ page: 1, itemsPerPage: 10, orderBy: '', order: '' })

const formatDate = date => moment(date).format('MM/DD/YYYY')

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

const editCompany = company => {
  editCompanyDetails.value = { ...company }
  isEditCompanyDrawerOpen.value = true
}

const deleteCompany= async company => {
  try {
    const confirmDelete = await Swal.fire({
      title: "Are you certain about deleting this company?",
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

      const res = await companyStore.delete(company.uuid)
      const errors = getErrors.value
      if(errors)
      {
        toast.error('Something went wrong.')
      }
      else{
        isLoading.value = true
        toast.success('Company deleted successfully', { timeout: 1000 })
        await fetchCompanies()
        isLoading.value = false
      }
    }
  } catch (error) {
    toast.error('Failed to delete company:', error.message || error)
  }
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

const getErrors = computed(() => {
  return companyStore.getErrors
})

const getLoadStatus = computed(() => {
  return companyStore.getLoadStatus
})

const getStatusCode = computed(() => {
  return companyStore.getStatusCode
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

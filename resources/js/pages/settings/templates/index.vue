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
              Manage Templates
            </h3>
          </div>
          <p class="text-body-1 text-muted mt-1">
            Seamlessly manage and organize project templates for {{ userDetails?.company }} to streamline your processes and improve efficiency.
          </p>
        </div>
      </VCol>
      <VCol
        cols="12"
        md="3"
      >
        <div class="d-flex justify-end">
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
      <VRow v-if="getTemplates.length === 0">
        <VCol
        cols="12"
        class="d-flex flex-column align-center justify-center text-center" 
      >
        <span v-html="emptyTemplates" />
        <span class="">No templates found.</span>
      </VCol>
      </VRow>
      <VRow v-else-if="viewType === 'list'">
        <VCol
          v-for="template in getTemplates"
          :key="template.id"
          cols="12"
        >
          
          <VCard class="d-flex ps-4 py-1 list-side-border" @click.stop="goToTemplateDetail(template?.uuid)">
            <VCol cols="3">
              <div class="d-flex align-center gap-x-3">
                <VIcon
                  color="primary"
                  :size="28"
                  icon="tabler-template"
                />
                <div>
                  <h6 class="text-h6 text-no-wrap">
                    <span class="d-block">{{ template.template_name }}</span>
                  </h6>
                </div>
              </div>
            </VCol>
            <VCol cols="2">
              <div class="d-flex flex-column ms-3">
                <span class="d-block font-weight-bold text-high-emphasis text-sm text-truncate">Template Type</span>
                <small>Web Designs</small>
              </div>
            </VCol>
            <VCol cols="4">
              <div class="d-flex flex-column ms-3">
                <span class="d-block font-weight-bold text-high-emphasis text-sm text-truncate">Lists</span>
                <div class="d-flex flex-row flex-wrap">
                  <span
                    v-for="(list, index) in template.lists"
                    :key="index"
                    class="me-1 mb-1"
                  >
                    <VTooltip location="top">
                      <template #activator="{ props }">
                        <VChip
                          v-bind="props"
                          size="small"
                          color="primary"
                        >
                          {{ list.name }} ({{ list.tasks_count }})
                        </VChip>
                      </template>
                      <span>{{ list.tasks_count }} tasks</span>
                    </VTooltip>
                  </span>
                </div>
              </div>
            </VCol>

            <VCol cols="2">
              <div class="d-flex flex-column ms-3">
                <span class="d-block font-weight-bold text-high-emphasis text-sm text-truncate text-center">Created At</span>
                <small class="text-center">{{ formatDate(template.created_at) }}</small>
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
                      :to="{ name: 'manage-templates', params: { id: template.uuid } }"
                    >
                      Edit
                    </VListItem>
                    <VListItem
                      value="delete"
                      @click="deleteTemplate(template)"
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
          v-for="template in getTemplates"
          :key="template.id"
          cols="12"
          md="4"
        >
          <VCard class="mb-4 elevation-3" @click.stop="goToTemplateDetail(template?.uuid)">
            <VCardTitle class="pb-0">
              <div class="d-flex justify-space-between align-center">
                <!-- Template Name with Icon -->
                <div class="d-flex align-center">
                  <VIcon
                    color="primary"
                    :size="28"
                    icon="tabler-template"
                    class="me-3"
                  />
                  <h6 class="mb-0 text-h6">
                    {{ template.template_name }}
                  </h6>
                </div>

                <!-- Options Menu -->
                <IconBtn @click.prevent>
                  <VIcon icon="tabler-dots-vertical" />
                  <VMenu activator="parent">
                    <VList>
                      <VListItem
                        value="edit"
                        :to="{ name: 'manage-templates', params: { id: template.uuid } }"
                      >
                        Edit
                      </VListItem>
                      <VListItem
                        value="delete"
                        @click="deleteTemplate(template)"
                      >
                        Delete
                      </VListItem>
                    </VList>
                  </VMenu>
                </IconBtn>
              </div>
            </VCardTitle>

            <VCardText class="px-4 pt-2 pb-4">
              <!-- Template Info -->
              <div class="d-flex justify-space-between mb-3">
                <div class="d-flex flex-column">
                  <small class="text-high-emphasis">
                    <span class="font-weight-bold">Type:</span> Web Designs
                  </small>
                  <small class="text-high-emphasis">
                    <span class="font-weight-bold">Created At:</span> {{ formatDate(template.created_at) }}
                  </small>
                </div>
              </div>

              <!-- Lists and Chips -->
              <div class="d-flex flex-wrap gap-1">
                <span
                  v-for="(list, index) in template.lists"
                  :key="index"
                >
                  <VTooltip location="top">
                    <template #activator="{ props }">
                      <VChip
                        v-bind="props"
                        size="small"
                        color="primary"
                      >
                        {{ list.name }} ({{ list.tasks_count }})
                      </VChip>
                    </template>
                    <span>{{ list.tasks_count }} tasks</span>
                  </VTooltip>
                </span>
              </div>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>
    </div>

    <TablePagination
      v-if="getLoadStatus !== 1 && getTemplates.length > 0"
      v-model:page="options.page"
      :items-per-page="options.itemsPerPage"
      :total-items="totalTemplates"
      class="custom-pagination"
      @update:page="handlePageChange"
    />
  </div>
</template>

<script setup>
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'
import emptyTemplates from '@images/darby/projects_list.svg?raw'
import ListViewSkeleton from '@/pages/projects/web-designs/_partials/list-view-skeleton.vue'
import GridViewSkeleton from '@/pages/teams/_partials/grid-view-skeleton.vue'
import { computed, onBeforeMount, onMounted, onUnmounted, ref, watch } from 'vue'
import { useToast } from "vue-toastification"
import { useTemplateStore } from '@/store/templates'
import { useUserStore } from '@/store/users'
import moment from 'moment'

useHead({ title: `${layoutConfig.app.title} | Manage Templates` })

const toast = useToast()
const templateStore = useTemplateStore()
const userStore = useUserStore()
const router = useRouter()

const totalRecords = ref(0)
const isLoading = ref(false)
const search = ref('')
const viewType = ref('list')
const options = ref({ page: 1, itemsPerPage: 10, orderBy: '', order: '' })

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
  await fetchTemplates()
  totalRecords.value = totalTemplates.value
})

const formatDate = date => moment(date).format('MM/DD/YYYY')

const fetchTemplates = async () => {
  try {
    isLoading.value = true
    await templateStore.getAllWithPagination(options.value.page, options.value.itemsPerPage, search.value)
  } catch (error) {
    toast.error('Error fetching templates:', error)
  } finally {
    isLoading.value = false
  }
}

const editTemplate = template => {
  editTemplateDetails.value = { ...template }
  isEditTemplateDrawerOpen.value = true
}

const deleteTemplate = async template => {
  try {
    const confirmDelete = await Swal.fire({
      title: `Are you certain about deleting ${template.template_name} template?`,
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

      const res = await templateStore.delete(template.uuid)
      const errors = getErrors.value
      if(errors)
      {
        toast.error('Something went wrong.')
      }
      else{
        isLoading.value = true
        toast.success('Template deleted successfully', { timeout: 1000 })
        await fetchTemplates()
        isLoading.value = false
      }
    }
  } catch (error) {
    toast.error('Failed to delete template:', error.message || error)
  }
}

const handlePageChange = async page => {
  options.value.page = page
  await fetchTemplates()
}

const getTemplates = computed(() => {
  return templateStore.getTemplates
})

const getErrors = computed(() => {
  return templateStore.getErrors
})

const getLoadStatus = computed(() => {
  return templateStore.getLoadStatus
})

const totalTemplates = computed(() => {
  return templateStore.templatesCount
})

const userDetails = computed(() => {
  return userStore.getUser
})

const goToTemplateDetail = async uuid => {
  router.push({ name: 'manage-templates', params: { id: uuid } })
} 

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

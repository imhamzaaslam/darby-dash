<template>
  <div>
    <VRow class="mt-0 pt-0">
      <VCol
        cols="12"
        class="pt-0 ps-4"
      >
        <h3>Manage Templates</h3>
      </VCol>
    </VRow>

    <!-- Skeleton Loader -->
    <div v-if="getLoadStatus == 1">
      <VRow class="mb-4">
        <ListViewSkeleton />
      </VRow>
    </div>

    <div v-else>
      <VRow v-if="getTemplates.length === 0">
        <VCol cols="12">
          <VCard class="px-3 py-3 text-center">
            <span>No Templates found</span>
          </VCard>
        </VCol>
      </VRow>
      <VRow>
        <VCol
          v-for="template in getTemplates"
          :key="template.id"
          cols="12"
        >
          <VCard class="d-flex ps-4 py-1 list-side-border">
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
                          {{ list.list_name }} ({{ list.tasks_count }})
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
                    <!--
                      <VListItem
                      value="edit"
                      @click="editTemplate(template)"
                      >
                      Edit
                      </VListItem>
                    -->
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
import Swal from 'sweetalert2'
import ListViewSkeleton from '@/pages/projects/web-designs/_partials/list-view-skeleton.vue'
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useTemplateStore } from '@/store/templates'
import moment from 'moment'

useHead({ title: `${layoutConfig.app.title} | Manage Templates` })
onBeforeMount(async () => {
  await fetchTemplates()
  totalRecords.value = totalTemplates.value
})

const toast = useToast()
const templateStore = useTemplateStore()

const totalRecords = ref(0)
const isLoading = ref(false)
const search = ref('')
const options = ref({ page: 1, itemsPerPage: 10, orderBy: '', order: '' })

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

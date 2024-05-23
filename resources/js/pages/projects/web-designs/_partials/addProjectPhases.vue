<template>
  <VContainer>
    <VRow>
      <VCol
        cols="12"
        md="6"
        class="d-flex"
      />
      <VCol
        cols="12"
        md="6"
      >
        <div class="float-right">
          <VBtn
            v-if="!showAddPhaseField"
            color="primary"
            size="small"
            @click="activateQuickAdd"
          >
            <VIcon icon="tabler-plus" />
            Add Phase
          </VBtn>
        </div>
      </VCol>
    </VRow>
    <VRow>
      <VCol cols="12">
        <div v-if="getProjectPhases.length > 0">
          <VCard
            v-for="(phase, index) in getProjectPhases"
            :key="index"
            class="px-2 py-1 mt-2"
            @click="startEditing(phase)"
          >
            <VRow>
              <VCol
                cols="6"
                class="mt-2"
              >
                <VIcon
                  class="tabler-brand-asana"
                  color="primary"
                />
                <span class="ms-2">{{ phase.name.length > 50 ? phase.name.substring(0, 50) + '...' : phase.name }}</span>
              </VCol>
              <VCol
                cols="5"
                class="mt-2"
              >
                <div class="float-right">
                  <VChip
                    color="primary"
                    size="small"
                    class="ms-2"
                  >
                    {{ formatDate(phase.created_at) }}
                    <VTooltip
                      activator="parent"
                      location="top"
                    >
                      <span>Created on {{ formatDate(phase.created_at) }}</span>
                    </VTooltip>
                  </VChip>
                </div>
              </VCol>
              <VCol cols="1">
                <!-- Actions Menu -->
                <IconBtn class="ms-2">
                  <VIcon icon="tabler-dots-vertical" />
                  <VMenu activator="parent">
                    <VList>
                      <VListItem @click="startEditing(phase)">
                        Edit
                      </VListItem>
                      <VListItem @click="deletePhase(phase)">
                        Delete
                      </VListItem>
                    </VList>
                  </VMenu>
                </IconBtn>
              </VCol>
            </VRow>
          </VCard>
        </div>
        <div
          v-else
          class="text-center"
        >
          <div
            v-if="!showAddPhaseField"
            class="mt-12"
            v-html="NoPhaseInList"
          />
          <span v-if="!showAddPhaseField">No phases added yet.</span>
        </div>
        <VBtn
          v-if="!showAddPhaseField && getProjectPhases.length > 0"
          color="primary"
          variant="plain"
          size="small"
          @click="activateQuickAdd"
        >
          <VIcon icon="tabler-plus" />
          Add Phase
        </VBtn>
        <VCard
          v-if="showAddPhaseField"
          class="px-2 py-2 mt-2"
        >
          <VRow>
            <VCol cols="8">
              <!-- Drag Icon -->
              <div class="d-flex align-center">
                <VIcon
                  class="tabler-brand-asana me-2"
                  color="primary"
                />
                <VTextField
                  ref="quickPhaseInput"
                  v-model="quickPhaseName"
                  style="margin-top: -7px;"
                  variant="plain"
                  hide-details
                  dense
                  @keydown.enter="addQuickPhase"
                />
              </div>
            </VCol>
            <VCol cols="4">
              <div class="float-right">
                <VIcon
                  class="tabler-circle-check me-1"
                  color="primary"
                  @click="addQuickPhase"
                >
                  <VTooltip
                    activator="parent"
                    location="top"
                  >
                    <span>Save</span>
                  </VTooltip>
                </VIcon>
                <VIcon
                  class="tabler-x"
                  color="primary"
                  @click="cancelQuickPhase"
                >
                  <VTooltip
                    activator="parent"
                    location="top"
                  >
                    <span>Cancel</span>
                  </VTooltip>
                </VIcon>
              </div>
            </VCol>
          </VRow>
        </VCard>
      </VCol>
    </VRow>
  </VContainer>
  <EditPhaseDrawer
    v-model:is-edit-phase-drawer-open="isEditPhaseDrawerOpen"
    :fetch-project-phases="fetchProjectPhases"
    :editing-phase="editingPhase"
    :get-load-status="getLoadStatus"
  />
</template>

<script setup="js">
import moment from 'moment'
import Swal from 'sweetalert2'
import NoPhaseInList from '@images/darby/tasks_list.svg?raw'
import EditPhaseDrawer from '@/pages/projects/web-designs/_partials/update-project-phase-drawer.vue'
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { usePhaseStore } from "../../../../store/lists"
import { useRouter } from 'vue-router'

const toast = useToast()
const phaseStore = usePhaseStore()
const router = useRouter()

const phaseName = ref('')
const editingPhase = ref({})
const isEditPhaseDrawerOpen = ref(false)
const showAddPhaseField = ref(false)
const quickPhaseName = ref('')
const quickPhaseInput = ref(null)

const isLoading = ref(false)

const projectId = computed(() => router.currentRoute.value.params.id)

const formatDate = date => moment(date).format('MMM DD, YYYY')

onBeforeMount(async () => {
  await fetchProjectPhases()
})

const fetchProjectPhases = async () => {
  try {
    isLoading.value = true
    await phaseStore.getAll(projectId.value)
  } catch (error) {
    toast.error('Error fetching project phases:', error)
  }
  finally {
    isLoading.value = false
  }
}

function activateQuickAdd() {
  showAddPhaseField.value = true
  nextTick(() => {
    quickPhaseInput.value.focus()
  })
}

async function addQuickPhase() {
  if (quickPhaseName.value.trim() === '') {
    toast.error('Phase name cannot be empty.')

    return
  }

  try {
    activateQuickAdd()

    const newPhaseDetails = {
      name: quickPhaseName.value.trim(),
      project_uuid: projectId.value,
    }

    await phaseStore.create(newPhaseDetails)
    quickPhaseName.value = ''
    toast.success('Phase added successfully', { timeout: 1000 })
    fetchProjectPhases()
  } catch (error) {
    toast.error('Failed to add phase:', error)
  }
}

function cancelQuickPhase() {
  showAddPhaseField.value = false
  quickPhaseName.value = ''
}

function startEditing(phase) {
  editingPhase.value = { ...phase }
  isEditPhaseDrawerOpen.value = true
}

async function deletePhase(phase) {
  try {
    const confirmDelete = await Swal.fire({
      title: "Are you sure?",
      text: `Do you want to delete ${phase.name}?`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
    })

    if (confirmDelete.isConfirmed) {
      const phaseWithProjectId = { ...phase, project_uuid: projectId.value }
      const res = await phaseStore.delete(phaseWithProjectId)

      isLoading.value = true
      toast.success('Phase deleted successfully', { timeout: 1000 })
      await fetchProjectPhases()
      isLoading.value = false
    }
  } catch (error) {
    toast.error('Failed to delete phase:', error.message || error)
  }
}

const getProjectPhases = computed(() => phaseStore.getProjectPhases)

const getLoadStatus = computed(() => {
  return phaseStore.getLoadStatus
})
</script>

<style scoped>
.d-toggle{
    border: unset !important;
    padding: 0 !important;
    align-items: unset !important;
    block-size: unset !important;
}
.task-row {
    border-bottom: 1px solid #ccc;
}
</style>

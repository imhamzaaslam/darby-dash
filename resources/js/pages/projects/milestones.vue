<template>
  <div>
    <!-- Toggle -->
    <VRow>
      <VCol
        cols="12"
        md="7"
        class="d-flex"
      >
        <!--
          <VBtnToggle
          v-model="viewType"
          class="d-toggle align-center-important"
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
          <VIcon
          icon="tabler-filter"
          class="bg-primary ms-1"
          />
        -->
        <div class="d-flex justify-center align-center">
          <VAvatar
            :size="30"
            class="me-1"
            :image="sketch"
          />
          <h3 class="text-primary">
            {{ project?.title }}
          </h3>
        </div>
        <!-- </VBtnToggle> -->
      </VCol>
      <VCol
        cols="12"
        md="5"
      >
        <div class="d-flex justify-end">
          <VBtn
            prepend-icon="tabler-plus"
            @click="isAddMileStoneDialogueOpen = !isAddMileStoneDialogueOpen"
          >
            Add Milestone
          </VBtn>
        </div>
      </VCol>
    </VRow>
    <VRow class="mt-5 mb-2 pt-0 pb-0">
      <VCol
        cols="12"
        class="pt-0 ps-4 pb-0"
      >
        <h3>
          Manage Milestones
        </h3>
      </VCol>
    </VRow>

    <div
      v-if="mileStoneLoadStatus !== 1 && getProjectMileStone.length === 0"
      class="text-center"
    >
      <div
        class="mt-12"
        v-html="NoTaskInList"
      />
      <span>
        No Milestones Added Yet
      </span>
    </div>

    <div v-else>
      <!-- Skeleton Loader -->
      <div v-if="getProjectLoadStatus == 1 || mileStoneLoadStatus === 1">
        <VRow
          v-if="viewType === 'list'"
          class="mb-4"
        >
          <TeamListSkeleton />
        </VRow>

        <!-- Grid View Skeleton Loader -->
        <VRow v-else>
          <TeamGridSkeleton />
        </VRow>
      </div>

      <div v-else>
        <VRow
          v-if="viewType === 'list'"
          class="mb-4"
        >
          <VCol
            v-for="(data, index) in getProjectMileStone"
            :key="index"
            cols="12"
          >
            <VCard class="d-flex py-1">
              <VCol
                cols="3"
                class="mt-2"
              >
                <div class="d-flex align-center">
                  <VIcon
                    icon="tabler-playstation-square"
                    color="primary"
                  />
                  <div>
                    <h6 class="ms-1 text-h6 text-no-wrap">
                      <span class="d-block">{{ data.name }}</span>
                    </h6>
                  </div>
                </div>
              </VCol>
              <VCol
                cols="3"
                class="mt-2"
              >
                <div v-if="data.lists ? data.lists.length>0 : 0 ">
                  <span
                    v-for="(list, listIndex) in data.lists"
                    :key="listIndex"
                  >
                    <VChip
                      class="me-1"
                      color="primary"
                      size="small"
                      @click="() => $router.push(`/projects/${projectUuid}/tasks/add`)"
                    >
                      {{ list.name }}
                    </VChip>
                  </span>
                </div>
                <small v-else>No list added yet.</small>
              </VCol>
              <VCol
                cols="2"
                class="mt-1"
              >
                <div class="d-flex align-center gap-3">
                  <div class="flex-grow-1 mt-1">
                    <VProgressLinear
                      :height="6"
                      :model-value="data.mileStoneProgress.progress"
                      color="primary"
                      rounded
                    />
                  </div>
                  <div class="text-body-1 text-high-emphasis mt-2">
                    {{ data.mileStoneProgress.progress }}%
                  </div>
                </div>
              </VCol>
              <VCol
                cols="3 text-center"
                class="mt-2"
              >
                <VChip
                  variant="outlined"
                  :color="data.status === 'done' ? 'success' : 'warning'"
                  size="small"
                >
                  <span>{{ capitalizeFirstLetter(data.mileStoneProgress.status) }}</span>
                </VChip>
              </VCol>
              <VCol
                cols="1"
                class="ms-8"
              >
                <IconBtn @click.prevent>
                  <VIcon icon="tabler-dots" />
                  <VMenu activator="parent">
                    <VList>
                      <VList>
                        <VListItem
                          value="edit"
                          @click="editMileStone(data)"
                        >
                          Edit
                        </VListItem>
                        <VListItem
                          value="delete"
                          @click="deleteMileStone(data)"
                        >
                          Delete
                        </VListItem>
                      </VList>
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
            v-for="(data, index) in getProjectMileStone"
            :key="index"
            cols="12"
            md="4"
          >
            <VCard>
              <div class="image-container">
                <VImg :src="Page2" />
              </div>
              <VCardText class="position-relative">
                <!-- User Avatar -->
                <VAvatar
                  size="75"
                  class="avatar-center"
                  color="primary"
                >
                  <span>{{ avatarText('Mile Stones') }}</span>
                </VAvatar>

                <VRow class="mt-5">
                  <VCol cols="8">
                    <div class="d-flex align-center mt-1 ms-1">
                      <div class="d-flex flex-column ms-3">
                        <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ data.name }}</span>

                        <span class="text-sm text-truncate mb-0">
                          Lists ({{ data.lists.length }})
                        </span>
                        <VChip
                          variant="outlined"
                          :color="data.status === 'done' ? 'success' : 'warning'"
                          size="small"
                          class="mt-3 position-absolute user_role"
                        >
                          <span>{{ capitalizeFirstLetter(data.mileStoneProgress.status) }}</span>
                        </VChip>
                      </div>
                    </div>
                  </VCol>
                </VRow>
              </VCardText>
            </VCard>
          </VCol>
        </VRow>
      </div>
    </div>
    <VDialog
      v-model="isAddMileStoneDialogueOpen"
      persistent
      class="v-dialog-sm"
    >
      <!-- Dialog close btn -->
      <DialogCloseBtn @click="isAddMileStoneDialogueOpen = !isAddMileStoneDialogueOpen" />

      <!-- Dialog Content -->
      <VCard title="Add Milestone">
        <VForm
          ref="addMileStoneForm"
          @submit.prevent="submitAddMileStoneForm"
        >
          <VCardText>
            <VTextField
              v-model="mileStoneForm.name"
              label="Name*"
              placeholder="Enter MileStone Name"
              clearable
              :rules="[requiredValidator]"
            />
          </VCardText>
          <VCardText>
            <AppAutocomplete
              v-model="mileStoneForm.projectListIds"
              :items="projectListsForDropDown"
              item-title="name"
              item-value="id"
              label="Add Project List"
              placeholder="Select Project List"
              multiple
              clearable
              clear-icon="tabler-x"
            />
          </VCardText>

          <VCardText class="d-flex justify-end gap-3 flex-wrap">
            <VBtn
              type="submit"
              :disabled="getProjectMileStone === 1"
              @click="addMileStoneForm?.validate()"
            >
              <span v-if="getProjectMileStone === 1">
                <VProgressCircular
                  :size="16"
                  width="3"
                  indeterminate
                />
                Loading...
              </span>
              <span v-else>
                Save
              </span>
            </VBtn>
            <VBtn
              color="secondary"
              @click="isAddMileStoneDialogueOpen = false"
            >
              Cancel
            </VBtn>
          </VCardText>
        </VForm>
      </VCard>
    </VDialog>
    <!-- // edit dialog -->
    <VDialog
      v-model="isEditMileStoneDialogueOpen"
      persistent
      class="v-dialog-sm"
    >
      <!-- Dialog close btn -->
      <DialogCloseBtn @click="isEditMileStoneDialogueOpen = !isEditMileStoneDialogueOpen" />

      <!-- Dialog Content -->
      <VCard title="Edit MileStone">
        <VForm
          ref="editMileStoneForm"
          @submit.prevent="submitEditMileStoneForm"
        >
          <VCardText>
            <VTextField
              v-model="editMileStoneFormData.name"
              label="Name*"
              placeholder="Enter MileStone Name"
              clearable
              :rules="[requiredValidator]"
            />
          </VCardText>
          <VCardText>
            <AppAutocomplete
              v-model="editMileStoneFormData.projectListIds"
              :items="editProjectList"
              item-title="name"
              item-value="id"
              label="Add Project List"
              placeholder="Select Project List"
              multiple
              clearable
              clear-icon="tabler-x"
            />
          </VCardText>

          <VCardText class="d-flex justify-end gap-3 flex-wrap">
            <VBtn
              type="submit"
              :disabled="getProjectMileStone === 1"
              @click="editMileStoneForm?.validate()"
            >
              <span v-if="getProjectMileStone === 1">
                <VProgressCircular
                  :size="16"
                  width="3"
                  indeterminate
                />
                Loading...
              </span>
              <span v-else>
                Save
              </span>
            </VBtn>
            <VBtn
              color="secondary"
              @click="isEditMileStoneDialogueOpen = false"
            >
              Cancel
            </VBtn>
          </VCardText>
        </VForm>
      </VCard>
    </VDialog>
  </div>
</template>

<script setup lang="js">
import TeamListSkeleton from '@/pages/projects/_partials/team-list-skeleton.vue'
import TeamGridSkeleton from '@/pages/projects/_partials/team-grid-skeleton.vue'
import Page2 from '../../../images/pages/2.png'
import NoTaskInList from '@images/darby/tasks_list.svg?raw'
import { useProjectStore } from "../../store/projects"
import { useMileStoneStore } from "@/store/milestones"
import { useProjectListStore } from "@/store/project_lists"
import { useRoute } from 'vue-router'
import { useToast } from "vue-toastification"
import sketch from '@images/icons/project-icons/sketch.png'

const mileStoneStore = useMileStoneStore()
const projectStore = useProjectStore()
const projectListStore = useProjectListStore()
const $route = useRoute()
const toast = useToast()
const projectUuid = $route.params.id
const viewType = ref('list')
const addMileStoneForm = ref()
const isAddMileStoneDialogueOpen = ref(false)
const editMileStoneForm = ref()
const isEditMileStoneDialogueOpen = ref(false)
const editProjectList = ref()

const mileStoneForm = ref({
  name: '',
  projectListIds: [],
})

const editMileStoneFormData = ref({
  uuid: '',
  name: '',
  projectListIds: [],
})

onBeforeMount(async () => {
  await fetchProject()
  await getMileStones()
  await getListsWithoutMileStones()
})

const resetForm = async () => {
  mileStoneForm.value = {
    name: '',
    projectListIds: [],
  }
  await getMileStones()
  await getListsWithoutMileStones()
}

const resetEditForm = async () => {
  editMileStoneFormData.value = {
    uuid: '',
    name: '',
    projectListIds: [],
  }
  await getMileStones()
  await getListsWithoutMileStones()
}

const submitAddMileStoneForm = async () => {
  addMileStoneForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        const payload = {
          name: mileStoneForm.value.name,
          project_list_ids: mileStoneForm.value.projectListIds,
        }

        await mileStoneStore.create(projectUuid, payload)
        toast.success('MileStone added successfully')
        isAddMileStoneDialogueOpen.value = false
        resetForm()
      } catch (error) {
        console.error('Error adding member:', error)
        toast.error('Failed to add member:', error.message || error)
      }
    }
  })
}

const deleteMileStone = async data => {
  try {
    await mileStoneStore.delete(data.uuid)
    toast.success('MileStone deleted successfully')
    await getMileStones()
    await getListsWithoutMileStones()
  } catch (error) {
    console.error('Error deleting member:', error)
    toast.error('Failed to delete member:', error.message || error)
  }
}

const fetchProject = async () => {
  await projectStore.show(projectUuid)
}

const getMileStones = async () => {
  await mileStoneStore.getAll(projectUuid)
}

const getListsWithoutMileStones = async () => {
  await projectListStore.getWithoutMileStone(projectUuid)
}

const editMileStone = async data => {
  const comibineList = data.lists.map(list => ({ id: list.id, name: list.name }))

  const projectListFromDropdown = projectListStore.getProjectListsForDropDown

  const newList = [
    ...projectListFromDropdown,
    ...comibineList,
  ]

  editMileStoneFormData.value = {
    uuid: data.uuid,
    name: data.name,
    projectListIds: data.lists.map(list => ({ id: list.id, name: list.name })),
  }

  editProjectList.value = newList
  isEditMileStoneDialogueOpen.value = true
}

const submitEditMileStoneForm = async () => {
  editMileStoneForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        const payload = {
          name: editMileStoneFormData.value.name,
          project_list_ids: editMileStoneFormData.value.projectListIds,
        }

        await mileStoneStore.update(editMileStoneFormData.value.uuid, payload)
        toast.success('MileStone updated successfully')
        isEditMileStoneDialogueOpen.value = false
        resetEditForm()
      } catch (error) {
        console.error('Error adding member:', error)
        toast.error('Failed to add member:', error.message || error)
      }
    }
  })
}

const mileStoneLoadStatus = computed(() => {
  return mileStoneStore.getLoadStatus
})

const getProjectLoadStatus = computed(() => {
  return projectStore.getLoadStatus
})

const project = computed(() =>{
  return projectStore.getProject
})

const getProjectMileStone = computed(() => {
  return mileStoneStore.getMilestones
})

const projectListsForDropDown = computed(() => {
  return projectListStore.getProjectListsForDropDown
})

const capitalizeFirstLetter = string => {
  return string.charAt(0).toUpperCase() + string.slice(1)
}
</script>

<style scoped>
.d-toggle{
  border: unset !important;
  padding: 0 !important;
  align-items: unset !important;
  block-size: unset !important;
}

.avatar-center {
  position: absolute;
  border: 3px solid rgb(var(--v-theme-surface));
  inset-block-start: -2rem;
  inset-inline-start: 1rem;
}

.image-container {
  position: relative;
}

.dots-icon {
  position: absolute;
  top: 8px;
  right: 8px;
}

.user_role{
  right: 10px;
}

.align-center-important {
  align-items: center !important;
}
</style>

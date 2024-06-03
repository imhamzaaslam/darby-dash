<template>
  <div>
    <!-- Toggle -->
    <VRow>
      <VCol
        cols="12"
        md="7"
        class="d-flex"
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
      </VCol>
      <VCol
        cols="12"
        md="5"
      >
        <div class="d-flex justify-end mb-5">
          <VBtn
            prepend-icon="tabler-plus"
            @click="isAddMemberDialogueOpen = !isAddMemberDialogueOpen"
          >
            New Member
          </VBtn>
        </div>
      </VCol>
    </VRow>

    <!-- Skeleton Loader -->
    <div v-if="isMembersLoading == 1">
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
          v-for="member in getUsersByProjects"
          :key="member.id"
          cols="12"
        >
          <VCard class="d-flex ps-4 py-1">
            <VCol cols="4">
              <div class="d-flex align-center">
                <VAvatar
                  size="36"
                  :class="member.avatar ? '' : 'text-white bg-primary'"
                  :variant="!member.avatar ? 'tonal' : ''"
                >
                  <span>{{ avatarText(member.name_first + ' ' + member.name_last) }}</span>
                </VAvatar>
                <div class="d-flex flex-column ms-3">
                  <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ member.name_first }} {{ member.name_last }}</span>
                  <small class="mt-0 text-xs">
                    {{ roleStore.capitalizeFirstLetter(member.role) }}
                  </small>
                </div>
              </div>
            </VCol>
            <VCol cols="7">
              <span class="">
                {{ member.email }}
              </span>
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
                        value="delete"
                        @click="deleteMember(project)"
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
          v-for="member in getUsersByProjects"
          :key="member.id"
          cols="12"
          md="4"
        >
          <VCard>
            <div class="image-container">
              <VImg :src="Page2" />
              <IconBtn
                class="dots-icon"
                @click.prevent
              >
                <VIcon
                  color="white"
                  icon="tabler-dots-vertical"
                />
                <VMenu activator="parent">
                  <VList>
                    <VList>
                      <VListItem
                        value="delete"
                        @click="deleteProject(project)"
                      >
                        Delete
                      </VListItem>
                    </VList>
                  </VList>
                </VMenu>
              </IconBtn>
            </div>
            <VCardText class="position-relative d-flex align-center">
              <VAvatar
                size="80"
                :class="member.avatar ? '' : 'text-white bg-primary'"
                :variant="!member.avatar ? 'tonal' : ''"
                class="avatar-center"
              >
                <span>{{ avatarText(member.name_first + ' ' + member.name_last) }}</span>
              </VAvatar>
              <div class="d-flex flex-column ms-3">
                <span class="font-weight-medium text-high-emphasis text-sm text-truncate side-flick-name">{{ member.name_first }} {{ member.name_last }}</span>
                <small class="mt-0 text-xs side-flick-role">
                  {{ roleStore.capitalizeFirstLetter(member.role) }}
                </small>
                <div class="d-flex align-center mt-8">
                  <VIcon
                    color="primary"
                    icon="tabler-mail"
                  />
                  <span class="ms-1 text-sm">{{ member.email }}</span>
                </div>
              </div>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>
    </div>
  </div>
  <VDialog
    v-model="isAddMemberDialogueOpen"
    persistent
    class="v-dialog-sm"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="isAddMemberDialogueOpen = !isAddMemberDialogueOpen" />

    <!-- Dialog Content -->
    <VCard title="Add Member">
      <VForm
        ref="addMemberForm"
        @submit.prevent="submitAddMemberForm"
      >
        <VCardText>
          <AppAutocomplete
            v-model="project.member_ids"
            :items="getMembers"
            item-title="name"
            item-value="id"
            label="Members*"
            placeholder="Select Members"
            multiple
            clearable
            clear-icon="tabler-x"
          />
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            @click="isAddMemberDialogueOpen = false"
          >
            Cancel
          </VBtn>
          <VBtn
            type="submit"
            @click="addMemberForm?.validate()"
          >
            Add
          </VBtn>
        </VCardText>
      </VForm>
    </VCard>
  </VDialog>
</template>

<script setup>
import Swal from 'sweetalert2'
import TeamListSkeleton from '@/pages/projects/_partials/team-list-skeleton.vue'
import TeamGridSkeleton from '@/pages/projects/_partials/team-grid-skeleton.vue'
import Page2 from '../../../images/pages/2.png'
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useProjectStore } from "../../store/projects"
import { useUserStore } from "../../store/users"
import { useRoleStore } from "../../store/roles"
import { useRoute } from 'vue-router'

const toast = useToast()
const projectStore = useProjectStore()
const userStore = useUserStore()
const roleStore = useRoleStore()
const router = useRoute()

const viewType = ref('list')
const addMemberForm = ref()
const isAddMemberDialogueOpen = ref(false)
const isLoading = ref(false)
const isMembersLoading = ref(false)

const projectId = computed(() => router.params.id)

onBeforeMount(async () => {
  await fetchProject()
  await fetchMembers()
})

async function submitAddMemberForm() {
  addMemberForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {

        const payload = {
          id: project.id,
          uuid: project.uuid,
          member_ids: project.member_ids,
        }

        await projectStore.update(payload)

        isAddMemberDialogueOpen.value = false
        toast.success('Member added successfully', { timeout: 1000 })
        await fetchMembers()
        await fetchProject()
      } catch (error) {
        console.error('Error adding member:', error)
        toast.error('Failed to add member:', error.message || error)
      }
    }
  })
}

const deleteMember = async member => {
  try {
    const confirmDelete = await Swal.fire({
      title: "Are you sure?",
      text: `Do you want to delete ?`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
    })

    if (confirmDelete.isConfirmed) {
      await userStore.delete(member.uuid)
      if(getErrors.value)
      {
        toast.error('Something went wrong. Please try again later.')
      }
      else{
        isLoading.value = true
        toast.success('Member deleted successfully', { timeout: 1000 })
        await fetchMembers()
        isLoading.value = false
      }
    }
  } catch (error) {
    toast.error('Failed to delete member:', error.message || error)
  }
}

const fetchMembers = async () => {
  try {
    if(getLoadStatus.value === 1) return

    await userStore.getAll()
    isLoading.value = true
  } catch (error) {
    toast.error('Error fetching members:', error)
  }
  finally {
    isLoading.value = false
  }
}

const fetchProject = async () => {
  try {
    if(getLoadStatus.value === 1) return
    await projectStore.show(projectId.value)
    isLoading.value = true
  } catch (error) {
    toast.error('Error fetching project:', error)
  }
  finally {
    isLoading.value = false
  }
}

const getMembers = computed(() => {
  return userStore.getMembersList
})

const project = computed(() =>{
  return projectStore.getProject
})

const getUsersByProjects = computed(() => {
  return userStore.getUsersByProjects
})

const getErrors = computed(() => {
  return userStore.getErrors
})

const getLoadStatus = computed(() => {
  return userStore.getLoadStatus
})
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
.side-flick-name{
    position: absolute;
    left: 27%;
    top: 5%;
}
.side-flick-role{
    position: absolute;
    left: 27%;
}
</style>

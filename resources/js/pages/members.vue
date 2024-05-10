<template>
  <VRow>
    <VCol
      cols="12"
      md="6"
    />
    <VCol
      cols="12"
      md="6"
      class="mb-3"
    >
      <VDialog
        v-model="isDialogVisible"
        persistent
        class="v-dialog-sm"
      >
        <!-- Dialog Activator -->
        <template #activator="{ props }">
          <VBtn
            color="primary"
            size="small"
            rounded="pill"
            class="float-right"
            v-bind="props"
          >
            <VIcon
              start
              icon="tabler-plus"
            />
            New Member
          </VBtn>
        </template>

        <!-- Dialog close btn -->
        <DialogCloseBtn @click="isDialogVisible = !isDialogVisible" />

        <!-- Dialog Content -->
        <VCard title="Add Member Details">
          <VForm
            ref="addMemberForm"
            @submit.prevent="submitAddMemberForm"
          >
            <VCardText>
              <VRow>
                <!-- First Name -->
                <VCol cols="6">
                  <AppTextField
                    v-model="newMemberDetails.name_first"
                    :rules="[requiredValidator]"
                    label="First Name"
                  />
                </VCol>

                <!-- Last Name -->
                <VCol cols="6">
                  <AppTextField
                    v-model="newMemberDetails.name_last"
                    :rules="[requiredValidator]"
                    label="Last Name"
                  />
                </VCol>

                <!-- Email -->
                <VCol cols="6">
                  <AppTextField
                    v-model="newMemberDetails.email"
                    :rules="[requiredValidator, emailValidator]"
                    label="Email"
                  />
                </VCol>

                <!-- Phone -->
                <VCol cols="6">
                  <AppTextField
                    v-model="newMemberDetails.phone"
                    type="number"
                    :rules="[requiredValidator]"
                    label="Phone"
                  />
                </VCol>

                <!-- Role -->
                <VCol cols="6">
                  <AppSelect
                    v-model="newMemberDetails.role"
                    label="Select Role"
                    placeholder="Select Role"
                    :rules="[requiredValidator]"
                    :items="getRoles"
                    item-title="name"
                    item-value="name"
                  />
                </VCol>

                <!-- Password -->
                <VCol cols="6">
                  <AppTextField
                    v-model="newMemberDetails.password"
                    :rules="[requiredValidator]"
                    label="Password"
                    :type="isPasswordVisible ? 'text' : 'password'"
                    :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                    @click:append-inner="isPasswordVisible = !isPasswordVisible"
                  />
                </VCol>

                <!-- Confirm Password -->
                <VCol cols="6">
                  <AppTextField
                    v-model="newMemberDetails.confirmPassword"
                    :type="isConfirmPasswordVisible ? 'text' : 'password'"
                    :rules="[requiredValidator, confirmedValidator(newMemberDetails.confirmPassword, newMemberDetails.password)]"
                    label="Confirm Password"
                    :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                    @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                  />
                </VCol>

                <!-- State -->
                <VCol cols="6">
                  <AppSelect
                    v-model="newMemberDetails.state"
                    label="Select Status"
                    placeholder="Select Status"
                    :rules="[requiredValidator]"
                    :items="[ { title: 'Active', value: 'active' }, { title: 'Inactive', value: 'inactive' } ]"
                    item-title="title"
                    item-value="value"
                  />
                </VCol>
              </VRow>
            </VCardText>

            <VCardText class="d-flex justify-end gap-3 flex-wrap">
              <VBtn
                color="secondary"
                @click="isDialogVisible = false"
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
    </VCol>
    <VDialog
      v-model="isEditDialogVisible"
      persistent
      class="v-dialog-sm"
    >
      <!-- Dialog close btn -->
      <DialogCloseBtn @click="isEditDialogVisible = !isEditDialogVisible" />

      <!-- Dialog Content -->
      <VCard title="Edit Member Details">
        <VForm
          ref="editMemberForm"
          @submit.prevent="submitEditMemberForm"
        >
          <VCardText>
            <VRow>
              <!-- First Name -->
              <VCol cols="6">
                <AppTextField
                  v-model="editMemberDetails.name_first"
                  :rules="[requiredValidator]"
                  label="First Name"
                />
              </VCol>

              <!-- Last Name -->
              <VCol cols="6">
                <AppTextField
                  v-model="editMemberDetails.name_last"
                  :rules="[requiredValidator]"
                  label="Last Name"
                />
              </VCol>

              <!-- Email -->
              <VCol cols="6">
                <AppTextField
                  v-model="editMemberDetails.email"
                  :rules="[requiredValidator, emailValidator]"
                  label="Email"
                />
              </VCol>

              <!-- Phone -->
              <VCol cols="6">
                <AppTextField
                  v-model="editMemberDetails.phone"
                  :rules="[requiredValidator]"
                  label="Phone"
                />
              </VCol>

              <!-- Role -->
              <VCol cols="6">
                <AppSelect
                  v-model="editMemberDetails.roles"
                  label="Select Role"
                  placeholder="Select Role"
                  :rules="[requiredValidator]"
                  :items="getRoles"
                  item-title="name"
                  item-value="name"
                />
              </VCol>

              <!-- State -->
              <VCol cols="6">
                <AppSelect
                  v-model="editMemberDetails.state"
                  label="Select Status"
                  placeholder="Select Status"
                  :rules="[requiredValidator]"
                  :items="[ { title: 'Active', value: 'active' }, { title: 'Inactive', value: 'inactive' } ]"
                  item-title="title"
                  item-value="value"
                />
              </VCol>
            </VRow>
          </VCardText>

          <VCardText class="d-flex justify-end gap-3 flex-wrap">
            <VBtn
              color="secondary"
              @click="isEditDialogVisible = false"
            >
              Cancel
            </VBtn>
            <VBtn
              type="submit"
              @click="editMemberForm?.validate()"
            >
              Save
            </VBtn>
          </VCardText>
        </VForm>
      </VCard>
    </VDialog>
  </VRow>
  <VCard>
    <VCardText class="d-flex justify-space-between align-center flex-wrap gap-4">
      <h5 class="text-h5">
        Manage Members
      </h5>
      <div style="inline-size: 272px;">
        <!--
          <AppTextField
          v-model="search"
          placeholder="Search Member"
          />
        -->
      </div>
    </VCardText>

    <VDivider />
    <VDataTable
      v-model:page="options.page"
      :headers="headers"
      :items-per-page="options.itemsPerPage"
      :items="getUsers"
      item-value="name"
      hide-default-footer
      :search="search"
      class="text-no-wrap"
      density="compact"
    >
      <template #item.name="{ item }">
        <div class="d-flex align-center">
          <VAvatar
            size="36"
            :color="item.avatar ? '' : generateRandomColor()"
            :class="item.avatar ? '' : 'v-avatar-light-bg info--text'"
            :variant="!item.avatar ? 'tonal' : generateRandomColor()"
          >
            <span>{{ avatarText(item.name_first + ' ' + item.name_last) }}</span>
          </VAvatar>
          <div class="d-flex flex-column ms-3">
            <span class="d-block font-weight-medium text-high-emphasis text-sm text-truncate">{{ item.name_first }} {{ item.name_last }}</span>
            <small class="mt-0">
              {{ roleStore.capitalizeFirstLetter(item.roles[0]) }}
            </small>
          </div>
        </div>
      </template>
      <template #item.email="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.email }}</span>
        </div>
      </template>
      <template #item.state="{ item }">
        <div class="d-flex gap-1">
          <VChip
            v-if="item.state == 'active'"
            color="success"
            variant="outlined"
            size="small"
          >
            Active
          </VChip>
          <VChip
            v-else
            color="error"
            variant="outlined"
            size="small"
          >
            Inactive
          </VChip>
        </div>
      </template>
      <template #item.created_at="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ formatDate(item) }}</span>
        </div>
      </template>

      <template #item.actions="{ item }">
        <div class="d-flex">
          <IconBtn @click="editMember(item)">
            <VIcon
              icon="tabler-edit"
              color="info"
            />
          </IconBtn>
          <IconBtn @click="deleteMember(item)">
            <VIcon
              icon="tabler-trash"
              color="error"
            />
          </IconBtn>
        </div>
      </template>
      <template #bottom>
        <VCardText class="pt-2">
          <div
            v-if="isLoading"
            class="text-center"
          >
            <VProgressCircular
              :size="30"
              width="3"
              indeterminate
              color="primary"
            />
          </div>
        </VCardText>
        <TablePagination
          v-model:page="options.page"
          :items-per-page="options.itemsPerPage"
          :total-items="getUsers.length"
        />
      </template>
    </VDataTable>
  </VCard>
</template>

<script setup>
import moment from 'moment'
import Swal from 'sweetalert2'
import { computed, onMounted, ref } from 'vue'
import { useToast } from "vue-toastification"
import { useRoleStore } from "../store/roles"
import { useUserStore } from "../store/users"

const toast = useToast()
const roleStore = useRoleStore()
const userStore = useUserStore()
const addMemberForm = ref()
const editMemberForm = ref()
const isDialogVisible = ref(false)
const isEditDialogVisible = ref(false)
const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)

const newMemberDetails = ref({
  name_first: '',
  name_last: '',
  email: '',
  role: '',
  phone: '',
  password: '',
  confirmPassword: '',
  state: '',
})

const editMemberDetails = ref({})

const isLoading = ref(false)
const search = ref('')
const options = ref({ page: 1, itemsPerPage: 5, sortBy: [''], sortDesc: [false] })
const formatDate = date => moment(date).format('Do MMMM YYYY')

const headers = [
  {
    title: 'Name',
    key: 'name',
    sortable: false,
  },
  {
    title: 'Email',
    key: 'email',
    sortable: false,
  },
  {
    title: 'Status',
    key: 'state',
    sortable: false,
    width: '15%',
  },
  {
    title: 'Created At',
    key: 'created_at',
    sortable: true,
    width: '20%',
  },
  {
    title: 'Actions',
    key: 'actions',
    sortable: false,
    width: '5%',
  },
]

onMounted(async () => {
  await fetchRoles()
  await fetchMembers()
})

const fetchMembers = async () => {
  try {
    isLoading.value = true
    await userStore.getAll(options.value.page, options.value.itemsPerPage)
  } catch (error) {
    console.error('Error fetching members:', error)
    toast.error('Error fetching members:', error)
  }
  finally {
    isLoading.value = false
  }
}

async function submitAddMemberForm() {
  addMemberForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        isDialogVisible.value = false
        isLoading.value = true

        const res = await userStore.create(newMemberDetails.value)

        toast.success('Member added successfully', { timeout: 1000 })
        await fetchMembers()
      } catch (error) {
        console.error('Error adding member:', error)
        toast.error('Failed to add member:', error)
      }
    }
  })
}

const editMember = member => {
  editMemberDetails.value = { ...member }
  isEditDialogVisible.value = true
}

async function submitEditMemberForm() {
  editMemberForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {

        isEditDialogVisible.value = false
        isLoading.value = true

        const res = await userStore.update(editMemberDetails.value)

        toast.success('Member updated successfully', { timeout: 1000 })
        await fetchMembers()
      } catch (error) {
        console.error('Error updating member:', error)
        toast.error('Failed to update member:', error.message || error)
      }
    }
  })
}

const deleteMember = async member => {
  try {
    const confirmDelete = await Swal.fire({
      title: "Are you sure?",
      text: `Do you want to delete ${member.name_first} ${member.name_last}?`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
    })

    if (confirmDelete.isConfirmed) {
      isLoading.value = true

      const res = await userStore.delete(member.uuid)

      toast.success('Member deleted successfully', { timeout: 1000 })
      await fetchMembers()
    }
  } catch (error) {
    console.error('Error deleting member:', error)
    toast.error('Failed to delete member:', error.message || error)
  }
}

const fetchRoles = async () => {
  try {
    isLoading.value = true

    await roleStore.getAll()
  } catch (error) {
    console.error('Error fetching roles:', error)
  }
  finally {
    isLoading.value = false
  }
}

const getRoles = computed(() => {
  return roleStore.getRoles
})

const getUsers = computed(() => {
  return userStore.getUsers
})

const generateRandomColor= () => {
  let color = '#' + Math.floor(Math.random()*16777215).toString(16)
  let rgb = parseInt(color.substring(1), 16)
  let r = (rgb >> 16) & 0xff
  let g = (rgb >>  8) & 0xff
  let b = (rgb >>  0) & 0xff
  let brightness = (r * 299 + g * 587 + b * 114) / 1000
  if (brightness > 128) {
    let ratio = 128 / brightness
    r = Math.floor(r * ratio)
    g = Math.floor(g * ratio)
    b = Math.floor(b * ratio)
    color = '#' + (r << 16 | g << 8 | b).toString(16)
  }

  return color
}
</script>


<style scoped>
.table-wrapper {
    inline-size: auto;
    overflow-x: auto;
}
</style>

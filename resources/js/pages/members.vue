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
                    label="First Name"
                    :rules="[requiredValidator]"
                    :error-messages="addingErrors.name_first"
                  />
                </VCol>

                <!-- Last Name -->
                <VCol cols="6">
                  <AppTextField
                    v-model="newMemberDetails.name_last"
                    label="Last Name"
                    :rules="[requiredValidator]"
                    :error-messages="addingErrors.name_last"
                  />
                </VCol>

                <!-- Email -->
                <VCol cols="6">
                  <AppTextField
                    v-model="newMemberDetails.email"
                    label="Email"
                    :rules="[requiredValidator, emailValidator]"
                    :error-messages="addingErrors.email"
                  />
                </VCol>

                <!-- Phone -->
                <VCol cols="6">
                  <AppTextField
                    v-model="newMemberDetails.phone"
                    type="number"
                    label="Phone"
                    :rules="[requiredValidator]"
                    :error-messages="addingErrors.phone"
                  />
                </VCol>

                <!-- Role -->
                <VCol cols="6">
                  <AppSelect
                    v-model="newMemberDetails.role"
                    label="Select Role"
                    placeholder="Select Role"
                    :items="getRoles"
                    item-title="name"
                    item-value="name"
                    :rules="[requiredValidator]"
                    :error-messages="addingErrors.role"
                  />
                </VCol>

                <!-- Password -->
                <VCol cols="6">
                  <AppTextField
                    v-model="newMemberDetails.password"
                    label="Password"
                    :type="isPasswordVisible ? 'text' : 'password'"
                    :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                    :rules="[requiredValidator]"
                    :error-messages="addingErrors.password"
                    @click:append-inner="isPasswordVisible = !isPasswordVisible"
                  />
                </VCol>

                <!-- Confirm Password -->
                <VCol cols="6">
                  <AppTextField
                    v-model="newMemberDetails.confirmPassword"
                    :type="isConfirmPasswordVisible ? 'text' : 'password'"
                    label="Confirm Password"
                    :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                    :rules="[requiredValidator, confirmedValidator(newMemberDetails.confirmPassword, newMemberDetails.password)]"
                    :error-messages="addingErrors.confirmPassword"
                    @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                  />
                </VCol>

                <!-- State -->
                <VCol cols="6">
                  <AppSelect
                    v-model="newMemberDetails.state"
                    label="Select Status"
                    placeholder="Select Status"
                    :items="[ { title: 'Active', value: 'active' }, { title: 'Inactive', value: 'inactive' } ]"
                    item-title="title"
                    item-value="value"
                    :rules="[requiredValidator]"
                    :error-messages="addingErrors.state"
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
                :disabled="getLoadStatus === 1"
                @click="addMemberForm?.validate()"
              >
                <span v-if="getLoadStatus === 1">
                  Loading...
                  <VProgressCircular
                    :size="20"
                    width="3"
                    indeterminate
                  />
                </span>
                <span v-else>
                  Add
                </span>
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
                  label="First Name"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.name_first"
                />
              </VCol>

              <!-- Last Name -->
              <VCol cols="6">
                <AppTextField
                  v-model="editMemberDetails.name_last"
                  label="Last Name"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.name_last"
                />
              </VCol>

              <!-- Email -->
              <VCol cols="6">
                <AppTextField
                  v-model="editMemberDetails.email"
                  label="Email"
                  :rules="[requiredValidator, emailValidator]"
                  :error-messages="editErrors.email"
                />
              </VCol>

              <!-- Phone -->
              <VCol cols="6">
                <AppTextField
                  v-model="editMemberDetails.phone"
                  label="Phone"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.phone"
                />
              </VCol>

              <!-- Role -->
              <VCol cols="6">
                <AppSelect
                  v-model="editMemberDetails.role"
                  label="Select Role"
                  placeholder="Select Role"
                  :items="getRoles"
                  item-title="name"
                  item-value="name"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.role"
                />
              </VCol>

              <!-- State -->
              <VCol cols="6">
                <AppSelect
                  v-model="editMemberDetails.state"
                  label="Select Status"
                  placeholder="Select Status"
                  :items="[ { title: 'Active', value: 'active' }, { title: 'Inactive', value: 'inactive' } ]"
                  item-title="title"
                  item-value="value"
                  :rules="[requiredValidator]"
                  :error-messages="editErrors.state"
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
              :disabled="getLoadStatus === 1"
              @click="editMemberForm?.validate()"
            >
              <span v-if="getLoadStatus === 1">
                Loading...
                <VProgressCircular
                  :size="20"
                  width="3"
                  indeterminate
                />
              </span>
              <span v-else>
                Save
              </span>
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
          :total-items="totalRecords"
          @update:page="handlePageChange"
        />
      </template>
    </VDataTable>
  </VCard>
</template>

<script setup>
import moment from 'moment'
import Swal from 'sweetalert2'
import { computed, onBeforeMount, ref } from 'vue'
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
const totalRecords = ref(0)

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
const options = ref({ page: 1, itemsPerPage: 10, sortBy: [''], sortDesc: [false] })
const formatDate = date => moment(date).format('MMM DD, YYYY')

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

onBeforeMount(async () => {
  await fetchRoles()
  await fetchMembers()
  totalRecords.value = totalUsers.value
})

const addingErrors = ref({
  name_first: '',
  name_last: '',
  email: '',
  role: '',
  phone: '',
  password: '',
  confirmPassword: '',
  state: '',
})

const editErrors = ref({
  name_first: '',
  name_last: '',
  email: '',
  role: '',
  phone: '',
  state: '',
})

const fetchMembers = async () => {
  try {
    await userStore.getAll(options.value.page, options.value.itemsPerPage)
    isLoading.value = true
  } catch (error) {
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
        resetForm('addingErrors')
        await userStore.create(newMemberDetails.value)
        if(getErrors.value)
        {
          showError('addingForm')
        }
        else{
          isDialogVisible.value = false
          isLoading.value = true
          toast.success('Member added successfully', { timeout: 1000 })
          await fetchMembers()
          resetForm('addingForm')
          isLoading.value = false
        }
      } catch (error) {
        toast.error('Failed to add member:', error)
      }
    }
  })
}

const editMember = member => {
  const { roles, ...rest } = member

  editMemberDetails.value = { ...rest, role: roles[0] }
  isEditDialogVisible.value = true
}

async function submitEditMemberForm() {
  editMemberForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        resetForm('editErrors')
        await userStore.update(editMemberDetails.value)
        if(getErrors.value)
        {
          showError('editForm')
        }
        else{
          isEditDialogVisible.value = false
          isLoading.value = true
          toast.success('Member updated successfully', { timeout: 1000 })
          await fetchMembers()
          isLoading.value = false
        }
      } catch (error) {
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

const fetchRoles = async () => {
  try {
    isLoading.value = true

    await roleStore.getAll()
  } catch (error) {
    toast.error('Failed to get roles:', error.message || error)
  }
  finally {
    isLoading.value = false
  }
}

const showError = formType => {
  if (getStatusCode.value === 500) {
    toast.error('Something went wrong. Please try again later.')
  } else {
    if (formType === 'addingForm') {
      addingErrors.value = getErrors.value
    }
    if(formType === 'editForm') {
      editErrors.value = getErrors.value
    }
  }
}

const resetForm = formType => {
  if(formType === 'addingErrors') {
    addingErrors.value = Object.fromEntries(Object.keys(addingErrors.value).map(key => [key, '']))
  }
  if(formType === 'addingErrors') {
    editErrors.value = Object.fromEntries(Object.keys(editErrors.value).map(key => [key, '']))
  }
  if(formType === 'addingForm') {
    newMemberDetails.value = Object.fromEntries(Object.keys(newMemberDetails.value).map(key => [key, '']))
  }
}

const getRoles = computed(() => {
  return roleStore.getRoles
})

const getUsers = computed(() => {
  return userStore.getUsers
})

const getErrors = computed(() => {
  return userStore.getErrors
})

const totalUsers = computed(() => {
  return userStore.usersCount
})

const getStatusCode = computed(() => {
  return userStore.getStatusCode
})

const getLoadStatus = computed(() => {
  return userStore.getLoadStatus
})

const generateRandomColor = () => '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(-50, '0')

const handlePageChange = async page => {
  options.value.page = page
  await fetchMembers()
}
</script>

<style scoped>
.table-wrapper {
    inline-size: auto;
    overflow-x: auto;
}
</style>

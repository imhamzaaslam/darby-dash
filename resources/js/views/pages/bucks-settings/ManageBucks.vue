<script setup>
import Stepper from '@/components/bucks-settings/Stepper.vue'

const numberedSteps = [
  {
    title: 'Super Admin',
    subtitle: '',
  },
  {
    title: 'Project Manager',
    subtitle: '',
  },
  {
    title: 'Staff User',
    subtitle: '',
  },
  {
    title: 'Client User',
    subtitle: '',
  },
]

const currentStep = ref(0)
const isPasswordVisible = ref(false)
const isCPasswordVisible = ref(false)

const formData = ref({
  username: '',
  email: '',
  password: '',
  cPassword: '',
  firstName: '',
  lastName: '',
  country: undefined,
  language: undefined,
  twitter: '',
  facebook: '',
  googlePlus: '',
  linkedIn: '',
})

const onSubmit = () => {
  console.log(formData.value)
}

const headers = [
  { title: 'Task', key: 'name' },
  { title: 'Bucks Amount', key: 'amount' },
  { title: 'Status', key: 'status' },
  { title: 'Assignee', key: 'assignee' },
  { title: 'Actions', key: 'actions' },
]

const tasks = ref([
  { id: 1, name: 'Task 1', amount: '$10.00', status: 'Completed', assignee: 'User A' },
  { id: 2, name: 'Task 2', amount: '$15.50', status: 'InProgress', assignee: 'User B' },
  { id: 3, name: 'Task 3', amount: '$7.20', status: 'Pending', assignee: 'User C' },
])

const getStatusClass = status => {
  switch (status) {
  case 'Completed':
    return 'text-success'
  case 'In Progress':
    return 'text-primary'
  case 'Pending':
    return 'text-warning'
  default:
    return 'text-secondary'
  }
}

const approveTask = taskId => {
  // Logic to approve task
  console.log(`Approved task with ID: ${taskId}`)
}

const rejectTask = taskId => {
  // Logic to reject task
  console.log(`Rejected task with ID: ${taskId}`)
}
</script>

<template>
  <VCard>
    <VRow>
      <VCol
        cols="12"
        md="3"
        :class="$vuetify.display.smAndDown ? 'border-b' : 'border-e'"
      >
        <VCardText>
          <!-- 👉 Stepper -->
          <Stepper
            v-model:current-step="currentStep"
            direction="vertical"
            :items="numberedSteps"
          />
        </VCardText>
      </VCol>
      <!-- 👉 stepper content -->
      <VCol
        cols="12"
        md="9"
      >
        <VCardText>
          <VForm>
            <VWindow
              v-model="currentStep"
              class="disable-tab-transition"
            >
              <VWindowItem>
                <VRow>
                  <VCol cols="12">
                    <h6 class="text-h6 font-weight-medium">
                      Super Admin
                    </h6>
                    <p class="mb-0">
                      Manage Bucks for Super Admin
                    </p>
                  </VCol>
                </VRow>
                <VRow class="d-flex align-center">
                  <VCol cols="12">
                    <VDataTable
                      :headers="headers"
                      :items="tasks"
                      item-key="id"
                      hide-default-footer
                      class="elevation-1 mb-4 pb-4"
                    >
                      <template #item.status="{ item }">
                        <span :class="getStatusClass(item.status)">
                          {{ item.status }}
                        </span>
                      </template>
                      <template #item.actions="{ item }">
                        <VBtn
                          color="success"
                          class="me-2"
                          size="small"
                          @click="approveTask(item.id)"
                        >
                          Approve
                        </VBtn>
                        <VBtn
                          color="error"
                          size="small"
                          @click="rejectTask(item.id)"
                        >
                          Reject
                        </VBtn>
                      </template>
                      <template #bottom />
                    </VDataTable>
                  </VCol>
                </VRow>
              </VWindowItem>

              <VWindowItem>
                <VRow>
                  <VCol cols="12">
                    <h6 class="text-h6 font-weight-medium">
                      Project Manager
                    </h6>
                    <p class="mb-0">
                      Manage Bucks for Project Manager
                    </p>
                  </VCol>
                </vrow>
                <VRow>
                  <VCol cols="12">
                    <VDataTable
                      :headers="headers"
                      :items="tasks"
                      item-key="id"
                      hide-default-footer
                      class="elevation-1 mb-4 pb-4"
                    >
                      <template #item.status="{ item }">
                        <span :class="getStatusClass(item.status)">
                          {{ item.status }}
                        </span>
                      </template>
                      <template #item.actions="{ item }">
                        <VBtn
                          color="success"
                          class="me-2"
                          size="small"
                          @click="approveTask(item.id)"
                        >
                          Approve
                        </VBtn>
                        <VBtn
                          color="error"
                          size="small"
                          @click="rejectTask(item.id)"
                        >
                          Reject
                        </VBtn>
                      </template>
                      <template #bottom />
                    </VDataTable>
                  </VCol>
                </VRow>
              </VWindowItem>

              <VWindowItem>
                <VRow>
                  <VCol cols="12">
                    <h6 class="text-h6 font-weight-medium">
                      Staff User
                    </h6>
                    <p class="mb-0">
                      Manage Bucks for Staff User
                    </p>
                  </VCol>
                </VRow>
                <VRow>
                  <VCol cols="12">
                    <VDataTable
                      :headers="headers"
                      :items="tasks"
                      item-key="id"
                      hide-default-footer
                      class="elevation-1 mb-4 pb-4"
                    >
                      <template #item.status="{ item }">
                        <span :class="getStatusClass(item.status)">
                          {{ item.status }}
                        </span>
                      </template>
                      <template #item.actions="{ item }">
                        <VBtn
                          color="success"
                          class="me-2"
                          size="small"
                          @click="approveTask(item.id)"
                        >
                          Approve
                        </VBtn>
                        <VBtn
                          color="error"
                          size="small"
                          @click="rejectTask(item.id)"
                        >
                          Reject
                        </VBtn>
                      </template>
                      <template #bottom />
                    </VDataTable>
                  </VCol>
                </VRow>
              </VWindowItem>

              <VWindowItem>
                <VRow>
                  <VCol cols="12">
                    <h6 class="text-h6 font-weight-medium">
                      Client User
                    </h6>
                    <p class="mb-0">
                      Manage Bucks for Client User
                    </p>
                  </VCol>
                </VRow>
                <VRow>
                  <VCol cols="12">
                    <VDataTable
                      :headers="headers"
                      :items="tasks"
                      item-key="id"
                      hide-default-footer
                      class="elevation-1 mb-4 pb-4"
                    >
                      <template #item.status="{ item }">
                        <span :class="getStatusClass(item.status)">
                          {{ item.status }}
                        </span>
                      </template>
                      <template #item.actions="{ item }">
                        <VBtn
                          color="success"
                          class="me-2"
                          size="small"
                          @click="approveTask(item.id)"
                        >
                          Approve
                        </VBtn>
                        <VBtn
                          color="error"
                          size="small"
                          @click="rejectTask(item.id)"
                        >
                          Reject
                        </VBtn>
                      </template>
                      <template #bottom />
                    </VDataTable>
                  </VCol>
                </VRow>
              </VWindowItem>
            </VWindow>
          </VForm>
        </VCardText>
      </VCol>
    </VRow>
  </VCard>
</template>

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
      <div class="d-flex justify-end mb-5">
        <VBtn
          prepend-icon="tabler-plus"
          @click="isAddPaymentDrawerOpen = !isAddPaymentDrawerOpen"
        >
          New Payment
        </VBtn>
      </div>
    </VCol>
  </VRow>
  <VCard>
    <VCardText class="d-flex justify-space-between align-center flex-wrap gap-4">
      <h3>
        Manage Payments
      </h3>
      <div style="inline-size: 272px;">
        <AppTextField
          v-model="search"
          placeholder="Search Member"
          @input="onFilter($event.target.value)"
        />
      </div>
    </VCardText>

    <VDivider />
    <VDataTable
      :headers="headers"
      :items-per-page="options.itemsPerPage"
      :items="[]"
      item-value="name"
      hide-default-footer
      class="text-no-wrap"
    >
      <template #item.type="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.type }}</span>
        </div>
      </template>
      <template #item.name="{ item }">
        <div class="d-flex align-center">
          <span class="text-sm text-truncate mb-0">{{ item.name }}</span>
        </div>
      </template>
      <template #item.email="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.email }}</span>
        </div>
      </template>
      <template #item.amount="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">${{ item.amount }}</span>
        </div>
      </template>
      <template #item.customer_id="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.customer_id }}</span>
        </div>
      </template>
      <template #item.card="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.card }}</span>
        </div>
      </template>
      <template #item.transaction_id="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.transaction_id }}</span>
        </div>
      </template>
      <template #item.created_at="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.created_at }}</span>
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
          :total-items="0"
          @update:page="handlePageChange"
        />
      </template>
    </VDataTable>
  </VCard>
  <AddPaymentDrawer 
    v-model:is-drawer-open="isAddPaymentDrawerOpen"
  />
</template>

<script setup>
import Swal from 'sweetalert2'
import AddPaymentDrawer from '@/pages/payments/_partials/add-payment-drawer.vue'
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"

const toast = useToast()

const isAddPaymentDrawerOpen = ref(false)
const isLoading = ref(false)
const search = ref('')
const options = ref({ page: 1, itemsPerPage: 10, orderBy: '', order: '' })


const headers = [
  { title: 'Type', key: 'type', sortable: false },
  { title: 'Name', key: 'name', sortable: false },
  { title: 'Email', key: 'email', sortable: false },
  { title: 'Amount', key: 'amount', sortable: false },
  { title: 'Customer ID', key: 'customer_id', sortable: false },
  { title: 'Card', key: 'card', sortable: false },
  { title: 'Transaction ID', key: 'transaction_id', sortable: false },
  { title: 'Created At', key: 'created_at', sortable: false },
]


const handlePageChange = async page => {
  options.value.page = page
  await fetchMembers()
}

const onFilter = async value => {
  options.value.page = 1
  search.value = value
  await fetchMembers()
}
</script>

<style scoped>
.table-wrapper {
    inline-size: auto;
    overflow-x: auto;
}
</style>

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
          placeholder="Search Payments"
          @input="onInput($event.target.value)"
        />
      </div>
    </VCardText>
  
    <VDivider />
    <VDataTable
      :headers="headers"
      :items-per-page="options.itemsPerPage"
      :items="getPayments"
      item-value="name"
      hide-default-footer
      class="text-no-wrap"
      :loading="isLoading"
      @update:options="updateOptions"
    >
      <template #item.type="{ item }">
        <div class="d-flex gap-1">
          <span class="text-sm text-truncate mb-0">{{ item.payment_method }}</span>
        </div>
      </template>
      <template #item.name="{ item }">
        <div class="d-flex align-center">
          <span class="text-sm text-truncate mb-0">{{ item.name_on_card }}</span>
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
          <span class="text-sm text-truncate mb-0">{{ item.card_number }}</span>
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
          :total-items="totalPayments"
          @update:page="handlePageChange"
        />
      </template>
    </VDataTable>
  </VCard>
  <AddPaymentDrawer 
    v-model:is-drawer-open="isAddPaymentDrawerOpen"
    :fetch-payments="fetchPayments"
    :get-errors="getErrors"
    :get-status-code="getStatusCode"
    :get-load-status="getLoadStatus"
  />
</template>
  
<script setup>
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import AddPaymentDrawer from '@/pages/projects/_partials/add-payment-drawer.vue'
import { computed, onBeforeMount, ref } from 'vue'
import { useToast } from "vue-toastification"
import { debounce } from 'lodash'
import { useRouter } from 'vue-router'
import { usePaymentStore } from "@/store/payments"
  
useHead({ title: `${layoutConfig.app.title} | Payments` })
  
onBeforeMount(async () => {
  await fetchPayments()
})
  
const paymentStore = usePaymentStore()
const toast = useToast()
const router = useRouter()
  
const projectUuid = computed(() => router.currentRoute.value.params.id)
const isAddPaymentDrawerOpen = ref(false)
const isLoading = ref(false)
const search = ref('')
const options = ref({ page: 1, itemsPerPage: 10, orderBy: '', order: '' })
  
const headers = [
  { title: 'Type', key: 'type', sortable: true },
  { title: 'Name', key: 'name', sortable: true },
  { title: 'Email', key: 'email', sortable: true },
  { title: 'Amount', key: 'amount', sortable: true },
  { title: 'Customer ID', key: 'customer_id', sortable: true },
  { title: 'Card', key: 'card', sortable: true },
  { title: 'Transaction ID', key: 'transaction_id', sortable: true },
  { title: 'Created At', key: 'created_at', sortable: true },
]
  
const fetchPayments = async () => {
  try {
    isLoading.value = true
    await paymentStore.getAll(projectUuid.value, options.value.page, options.value.itemsPerPage, search.value, options.value.orderBy, options.value.order)
  } catch (error) {
    toast.error(error.message)
  } finally {
    isLoading.value = false
  }
}
  
const handlePageChange = async page => {
  options.value.page = page
  await fetchPayments()
}
  
const onFilter = async value => {
  options.value.page = 1
  search.value = value
  await fetchPayments()
}
  
const debouncedFilter = debounce(onFilter, 300)
  
const onInput = value => {
  debouncedFilter(value)
}
  
const updateOptions = async updateOptions => {
  const sortKeyMap = {
    type: 'payment_method',
    name: 'name_on_card',
    email: 'email',
    amount: 'amount',
    customer_id: 'customer_id',
    card: 'card_number',
    transaction_id: 'transaction_id',
    created_at: 'created_at',
  }
  
  const sortByKey = updateOptions.sortBy[0]?.key
  if (sortByKey && sortKeyMap[sortByKey]) {
    options.value.orderBy = sortKeyMap[sortByKey]
  }
    
  options.value.order = updateOptions.sortBy[0]?.order
  await fetchPayments()
}
  
const getPayments = computed(() => {
  return paymentStore.getPayments
})
  
const getErrors = computed(() => {
  return paymentStore.getErrors
})
  
const totalPayments = computed(() => {
  return paymentStore.getPaymentsCount
})
  
const getStatusCode = computed(() => {
  return paymentStore.getStatusCode
})
  
const getLoadStatus = computed(() => {
  return paymentStore.getLoadStatus
})
</script>
  
  <style scoped>
  .table-wrapper {
      inline-size: auto;
      overflow-x: auto;
  }
  </style>
  
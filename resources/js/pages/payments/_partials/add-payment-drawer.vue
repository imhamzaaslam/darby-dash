<template>
  <VNavigationDrawer
    :model-value="props.isDrawerOpen"
    temporary
    location="end"
    width="600"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Add Payment Details"
      @cancel="$emit('update:isDrawerOpen', false)"
    />
  
    <VDivider />
    <!-- 👉 Payment Methods -->
    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VRow class="px-3">
            <!-- 👉 card type switch -->
            <VCol cols="12">
              <VRadioGroup
                v-model="selectedPaymentMethod"
                inline
              >
                <VRadio
                  v-for="paymentMethod in paymentMethods"
                  :key="paymentMethod.value"
                  :value="paymentMethod.value"
                  :label="paymentMethod.label"
                  color="primary"
                  class="me-6"
                  :error-messages="addingErrors.payment_method"
                />
              </VRadioGroup>
            </VCol>
          </VRow>
          <VForm 
            v-if="selectedPaymentMethod === 'credit-card'"
            ref="piaViaCreditCardForm"
            @submit.prevent="submitCreditCardPaymentForm"
          >
            <VRow class="px-3">
              <VCol cols="12">
                <VRow>
                  <VCol cols="3">
                    <AppTextField
                      v-model="amount"
                      prepend-inner-icon="tabler-currency-dollar"
                      label="Amount to Pay *"
                      placeholder="0.00"
                      type="number"
                      class="no-arrows"
                      :rules="[requiredValidator]"
                      :error-messages="addingErrors.amount"
                    />
                  </VCol>
                </VRow>
                <VRow>
                  <VCol cols="12">
                    <VRow>
                      <!-- 👉 Card Number -->
                      <VCol 
                        cols="12"
                        md="6"
                      >
                        <AppTextField
                          v-model="card_number"
                          label="Card Number *"
                          placeholder="1234 1234 1234 1234"
                          type="number"
                          class="no-arrows"
                          :rules="[requiredValidator]"
                          :error-messages="addingErrors.card_number"
                        />
                      </VCol>
                      <!-- 👉 Name -->
                      <VCol
                        cols="12"
                        md="6"
                      >
                        <AppTextField
                          v-model="name_on_card"
                          label="Name *"
                          placeholder="John Doe"
                          :rules="[requiredValidator]"
                          :error-messages="addingErrors.name_on_card"
                        />
                      </VCol>
                      <!-- 👉 Expiry month -->
                      <VCol
                        cols="6"
                        md="4"
                      >
                        <AppSelect
                          v-model="card_exp_month"
                          label="Expiry Month *"
                          placeholder="MM"
                          :items="months"
                          :rules="[requiredValidator]"
                          :error-messages="addingErrors.card_exp_month"
                        />
                      </VCol>

                      <!-- 👉 Expiry year -->
                      <VCol
                        cols="6"
                        md="4"
                      >
                        <AppSelect
                          v-model="card_exp_year"
                          label="Expiry Year *"
                          placeholder="YY"
                          :items="years"
                          :rules="[requiredValidator]"
                          :error-messages="addingErrors.card_exp_year"
                        />
                      </VCol>
                      <!-- 👉 Cvv code -->
                      <VCol
                        cols="6"
                        md="4"
                      >
                        <AppTextField
                          v-model="card_cvc"
                          type="number"
                          label="CVV *"
                          placeholder="123"
                          class="no-arrows"
                          :rules="[requiredValidator]"
                          :error-messages="addingErrors.card_cvc"
                        />
                      </VCol>
                    </VRow>
                  </VCol>
                </VRow>
                <VRow>
                  <VCol
                    cols="12"
                    class="d-flex flex-wrap gap-4"
                  >
                    <VBtn type="submit">
                      Save changes
                    </VBtn>
                  </VCol>
                </VRow>
              </VCol>
            </VRow>
          </VForm>
          <VForm 
            v-if="selectedPaymentMethod === 'cash'"
            ref="piaViaOtherForm"
            @submit.prevent="submitOtherPaymentForm"
          >
            <VRow class="px-3">
              <VCol cols="12">
                <VRow>
                  <VCol cols="3">
                    <AppTextField
                      v-model="amount"
                      prepend-inner-icon="tabler-currency-dollar"
                      label="Amount to Pay *"
                      placeholder="0.00"
                      type="number"
                      class="no-arrows"
                      :rules="[requiredValidator]"
                      :error-messages="addingErrors.amount"
                    />
                  </VCol>
                </VRow>
                <VRow>
                  <VCol
                    cols="12"
                    class="d-flex flex-wrap gap-4"
                  >
                    <VBtn type="submit">
                      Save changes
                    </VBtn>
                  </VCol>
                </VRow>
              </VCol>
            </VRow>
          </VForm>
          <VForm v-if="selectedPaymentMethod === 'po'">
            <VRow class="px-3">
                <VCol cols="12 text-center">
                    <h3>Do it later</h3>
                </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </PerfectScrollbar>
    </VCard>
  </VNavigationDrawer>
</template>
  
<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'
import { ref } from 'vue'
import { useToast } from "vue-toastification"
import { usePaymentStore } from "@/store/payments"
  
const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
//   getErrors: Object,
//   getStatusCode: Object,
//   getLoadStatus: Number,
})
  
const emit = defineEmits(['update:isDrawerOpen'])
const toast = useToast()
const paymentStore = usePaymentStore()

const paymentMethods = ref([
  { value: 'credit-card', label: 'Credit Card' },
  { value: 'cash', label: 'Cash' },
  { value: 'po', label: 'Purchase Order' },
])

const months = ref(['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'])
const years = ref(Array.from({ length: 20 }, (_, i) => (new Date().getFullYear() + i).toString()))

const isLoading= ref(false)
const piaViaCreditCardForm = ref(null)
const piaViaOtherForm = ref(null)
const selectedPaymentMethod = ref('credit-card')
const amount = ref('')
const card_number = ref('')
const name_on_card = ref('')
const card_exp_month = ref('07')
const card_exp_year = ref()
const card_cvc = ref()

const addingErrors = ref({
  payment_method: '',
  amount: '',
  card_number: '',
  name_on_card: '',
  card_exp_month: '',
  card_exp_year: '',
  card_cvc: '',
})

const submitCreditCardPaymentForm = async () => {
  piaViaCreditCardForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        resetErrors()

        const paymentDetails = {
          payment_method: selectedPaymentMethod.value,
          amount: amount.value,
          card_number: card_number.value,
          name_on_card: name_on_card.value,
          card_exp_month: card_exp_month.value,
          card_exp_year: card_exp_year.value,
          card_cvc: card_cvc.value,
        }

        await paymentStore.create(paymentDetails)

        if(props.getErrors)
        {
          showError()
        }
        else{
          isLoading.value = true
          emit('update:isDrawerOpen', false)
          toast.success('Payment added successfully', { timeout: 1000 })
          resetFormFields('payViaCreditCardForm')
          isLoading.value = false
        }
      } catch (error) {
        toast.error('Failed to add member:', error)
      }
    }
  })
}

const submitOtherPaymentForm = async () => {
  piaViaOtherForm.value?.validate().then(async ({ valid: isValid }) => {
    if(isValid){
      try {
        resetErrors()

        const paymentDetails = {
          payment_method: selectedPaymentMethod.value,
          amount: amount.value,
        }

        await paymentStore.create(paymentDetails)

        if(props.getErrors)
        {
          showError()
        }
        else{
          isLoading.value = true
          emit('update:isDrawerOpen', false)
          toast.success('Payment added successfully', { timeout: 1000 })
          resetFormFields('payViaOtherForm')
          isLoading.value = false
        }
      } catch (error) {
        toast.error('Failed to add member:', error)
      }
    }
  })
}

const resetErrors = () => {
  addingErrors.value = {
    payment_method: '',
    amount: '',
    card_number: '',
    name_on_card: '',
    card_exp_month: '',
    card_exp_year: '',
    card_cvc: '',
  }
}

const resetFormFields = formName => {
  formName.value?.reset()
  selectedPaymentMethod.value = 'credit-card'
  amount.value = null
  card_number.value = null
  name_on_card.value = null
  card_exp_month.value = '07'
  card_exp_year.value = null
  card_cvc.value = null
  resetErrors()
  emit('update:isDrawerOpen', false)
}

const showError = () => {
  if (props.getStatusCode === 500) {
    toast.error('Something went wrong. Please try again later.')
  } else {
    addingErrors.value = props.getErrors
  }
}
</script>
  
<style lang="scss">
.v-navigation-drawer__content {
    overflow-y: hidden !important;
}
/* Hide the up and down arrows for number inputs */
.no-arrows input::-webkit-outer-spin-button,
.no-arrows input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.no-arrows input {
  -moz-appearance: textfield;
}
</style>
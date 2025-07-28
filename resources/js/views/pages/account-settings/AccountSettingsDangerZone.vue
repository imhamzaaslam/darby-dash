<template>
  <VRow class="card-row">
    <!-- 👉 Delete Account -->
    <VCol
      cols="12"
      md="6"
    >
      <VCard
        title="Delete Account"
        class="card-height"
      >
        <VCardText>
          <p class="mt-n4 mb-6 text-sm">
            Did KeywordCaddy not work for you?<br><br>We’d love to hear why. We’re always looking for ways to get better. If you have feedback you think will be useful, please send us an email <a :href="emailLink">here</a>.
          </p>
          <div>
            <VCheckbox
              v-model="isAccountDeleted"
              :rules="validateAcccountDelete"
              label="I confirm my account delete"
            />
          </div>
          <!-- Button for Delete Account -->
          <VBtn
            :disabled="!isAccountDeleted"
            color="error"
            class="mt-3 error-btn-customer-style"
            @click="isConfirmDeleteAccountDialogOpen = true"
          >
            Delete Account
          </VBtn>
        </VCardText>
      </VCard>
    </VCol>

    <!-- 👉 Cancel Subscription -->
    <VCol
      cols="12"
      md="6"
    >
      <VCard
        title="Cancel Subscription"
        class="card-height"
      >
        <VCardText>
          <p class="mt-n4 mb-6 text-sm">
            Prefer to cancel your account instead?<br><br>We’ll be sorry to see you go. Once your account is cancelled, your information will be immediately and permanently deleted. To cancel your account please click below.
          </p>
          <div>
            <VCheckbox
              v-model="isCancelSubscription"
              :rules="validateCancelSubscription"
              label="I confirm my cancel subscription"
            />
          </div>
          <!-- Button for Delete Account -->
          <VBtn
            :disabled="!isCancelSubscription"
            color="error error-btn-customer-style"
            class="mt-3"
            @click="isConfirmCancelSubscriptionDialogOpen = true"
          >
            Cancel Subscription
          </VBtn>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
  <!-- Confirm Dialog -->
  <ConfirmDialog
    v-model:isDialogVisible="isConfirmDeleteAccountDialogOpen"
    confirmation-question="Are you sure you want to delete your account?"
    confirm-title="Deleted!"
    confirm-msg="Your account has been deleted successfully."
    cancel-title="Cancelled"
    cancel-msg="Account Deletion Cancelled!"
  />
  <!-- Confirm Dialog -->
  <ConfirmDialog
    v-model:isDialogVisible="isConfirmCancelSubscriptionDialogOpen"
    confirmation-question="Are you sure you want to cancel your subscription?"
    confirm-title="Unsubscribed!!"
    confirm-msg="Your subscription cancelled successfully."
    cancel-title="Cancelled"
    cancel-msg="Unsubscription Cancelled!"
  />
</template>

<script setup>
const emailLink = "mailto:support@keywordcaddy.com"

const isConfirmDeleteAccountDialogOpen = ref(false)
const isConfirmCancelSubscriptionDialogOpen = ref(false)
const isAccountDeleted = ref(false)
const isCancelSubscription = ref(false)
const validateAcccountDelete = [v => !!v || 'Please confirm account deletion']
const validateCancelSubscription = [v => !!v || 'Please confirm cancel subscription']
</script>

<style scoped>
.card-row {
  display: flex;
  align-items: stretch;
}

.card-height {
  height: 100%;
}
</style>

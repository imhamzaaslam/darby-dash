<template>
  <VDialog
    :model-value="props.isDialogVisible"
    :width="$vuetify.display.smAndDown ? 'auto' : 700"
    persistent
    @update:model-value="dialogVisibleUpdate"
  >
    <!-- Dialog close button -->
    <DialogCloseBtn @click="$emit('update:isDialogVisible', false)" />
  
    <VCard class="sort-dialog-card">
      <!-- Dialog Header -->
      <VCardTitle class="d-flex justify-between align-center mb-0">
        <span class="text-h5 fw-bold">
          <VIcon
            icon="tabler-users"
            class="me-2"
            color="primary"
          />
          {{ roleName }}'s List ({{ roleUsersLength }})
        </span>
      </VCardTitle>
  
      <!-- User Grid -->
      <VCardText class="scrollable-content">
        <VRow>
          <!-- Empty State -->
          <VCol
            v-if="props.usersList?.length === 0"
            cols="12"
            class="mt-7 text-center"
          >
            <VIcon
              icon="tabler-user-off"
              size="48"
              color="grey"
            />
            <p class="text-muted mt-2">
              No users found
            </p>
          </VCol>
  
          <!-- User Cards -->
          <VRow v-else>
            <VCol
              v-for="user in props.usersList"
              :key="user.id"
              cols="12"
              md="6"
              lg="4"
              class="mb-4"
            >
              <VCard class="user-card pa-4 d-flex flex-column align-center hover-elevation">
                <!-- Badge and Avatar -->
                <VBadge
                  dot
                  location="top end"
                  offset-x="1"
                  offset-y="1"
                  :color="user.is_online ? 'success' : 'warning'"
                >
                  <VAvatar
                    size="64"
                    :class="user.avatar ? '' : 'text-white bg-primary'"
                    :variant="!user.avatar ? 'tonal' : ''"
                  >
                    <span>{{ avatarText(user.name_first + ' ' + user.name_last) }}</span>
                  </VAvatar>
                </VBadge>
                <!-- User Name -->
                <h6 class="text-h6 mt-3 fw-bold">
                  {{ user.name_first }} {{ user.name_last }}
                </h6>
                <div class="text-sm text-muted">
                  {{ user.role_name }}
                </div>
  
                <!-- Contact Details -->
                <div class="contact-details mt-4 w-100">
                  <div class="d-flex align-center mb-2">
                    <VIcon
                      icon="tabler-mail"
                      color="primary"
                      size="18"
                    />
                    <span class="ms-2 text-sm text-high-emphasis">
                      {{ user.email }}
                    </span>
                  </div>
                  <div class="d-flex align-center">
                    <VIcon
                      icon="tabler-phone"
                      color="primary"
                      size="18"
                    />
                    <span class="ms-2 text-sm text-high-emphasis">
                      {{ user?.info?.phone || 'N/A' }}
                    </span>
                  </div>
                </div>
              </VCard>
            </VCol>
          </VRow>
        </VRow>
      </VCardText>
  
      <!-- Dialog Footer -->
      <VCardText class="d-flex justify-end gap-3 flex-wrap px-3 mb-0">
        <VBtn
          color="secondary"
          @click="$emit('update:isDialogVisible', false)"
        >
          Cancel
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>
</template>
  
<script setup>
import { ref, computed } from "vue"
import { useToast } from "vue-toastification"
  
const props = defineProps({
  isDialogVisible: { type: Boolean, required: true },
  usersList: { type: Object },
  roleName: {
    type: String,
    required: true,
  },
  roleUsersLength: {
    type: Number,
    required: true,
  },
})
  
const emit = defineEmits(["update:isDialogVisible"])
  
const dialogVisibleUpdate = async val => {
  emit("update:isDialogVisible", val)
}
  
const toast = useToast()
</script>
  
  <style scoped>
  .scrollable-content {
    max-height: 500px;
    overflow-y: auto;
    padding: 0 8px;
  }
  
  /* User Card Styles */
  .user-card {
    border: 1px solid rgb(var(--v-theme-primary));
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    text-align: center;
    background: linear-gradient(to bottom right, rgb(var(--v-theme-surface)), rgb(var(--v-theme-td-hover)));
  }
  
  .user-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  }
  
  .contact-details {
    text-align: left;
    width: 100%;
  }
  
  .hover-elevation {
    transition: all 0.3s ease;
  }
  </style>
  
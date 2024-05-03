<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits(['update:isDrawerOpen'])

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}

const refVForm = ref()
const title = ref()
const projectManager = ref()
const estHours = ref()
const estCost = ref()

const resetForm = () => {
  refVForm.value?.reset()
  emit('update:isDrawerOpen', false)
}
</script>

<template>
  <VNavigationDrawer
    :model-value="props.isDrawerOpen"
    temporary
    location="end"
    width="370"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Add Project Details"
      @cancel="$emit('update:isDrawerOpen', false)"
    />

    <VDivider />

    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm
            ref="refVForm"
            @submit.prevent=""
          >
            <VRow>
              <VCol cols="12">
                <AppTextField
                  v-model="title"
                  label="Title*"
                  :rules="[requiredValidator]"
                  placeholder="Title"
                />
              </VCol>

              <VCol cols="12">
                <AppTextField
                  v-model="projectManager"
                  label="Project Manager*"
                  :rules="[requiredValidator]"
                  placeholder="Project Manager"
                />
              </VCol>

              <VCol cols="12">
                <AppTextField
                  v-model="estHours"
                  label="Estimated Hours*"
                  :rules="[requiredValidator]"
                  placeholder="Estimated Hours"
                />
              </VCol>

              <VCol cols="12">
                <AppTextField
                  v-model="estBudget"
                  label="Estimated Budget*"
                  :rules="[requiredValidator]"
                  placeholder="Estimated Budget"
                />
              </VCol>

              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    color="primary"
                    class="me-4"
                  >
                    Add
                  </VBtn>
                  <VBtn
                    color="error"
                    variant="tonal"
                    @click="resetForm"
                  >
                    Discard
                  </VBtn>
                </div>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </PerfectScrollbar>
    </VCard>
  </VNavigationDrawer>
</template>

<style lang="scss">
.v-navigation-drawer__content {
  overflow-y: hidden !important;
}
</style>

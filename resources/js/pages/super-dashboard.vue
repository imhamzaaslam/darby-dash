<template>
  <h3>Dashboard</h3>
  <VRow class="mt-3">
    <VCol
      cols="12"
      md="4"
      sm="4"
    >
      <div>
        <VCard class="logistics-card-statistics cursor-pointer">
          <VCardText>
            <div class="d-flex align-center gap-x-4 mb-2">
              <VAvatar
                variant="tonal"
                color="primary"
                rounded
              >
                <VIcon
                  icon="tabler-businessplan"
                  size="28"
                />
              </VAvatar>
              <h5 class="text-h6 text-center">
                Companies: {{ totalCompanies ?? 0 }}
              </h5>
            </div>
          </VCardText>
        </VCard>
      </div>
    </VCol>
  </VRow>
</template>
  
<script setup>
import { layoutConfig } from '@layouts'
import { useHead } from '@unhead/vue'
import { useCompanyStore } from "@/store/companies"
import { useToast } from "vue-toastification"

useHead({ title: `${layoutConfig.app.title} | Super Admin Dashboard` })

const toast = useToast()
const companyStore = useCompanyStore()

onBeforeMount(async () => {
  await fetchCompanies()
})


const fetchCompanies = async () => {
  try {
    await companyStore.getAll()
  } catch (error) {
    toast.error('Error fetching companies:', error)
  }
}

const totalCompanies = computed(() => {
  return companyStore.companiesCount
}) 
</script>
  
  <style lang="scss" scoped>
  @use "@core-scss/base/mixins" as mixins;
  
  .logistics-card-statistics:hover {
    @include mixins.elevation(12);
  
    transition: all 0.1s ease-out;
  }
  .logistics-card-statistics {
    border-block-end: 2px solid rgba(var(--v-theme-primary));
  }
  .logistics-card-statistics:hover {
    border-block-end: 2px solid rgba(var(--v-theme-primary));
  }
  </style>
  
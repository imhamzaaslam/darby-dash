<template>
  <SuperDashboard v-if="authStore.isSuperAdmin" />
  <CompanyDashboard v-else />
</template>

<script setup>
import CompanyDashboard from './company-dashboard.vue'
import SuperDashboard from '@/pages/super-admin/companies/index.vue'
import { useAuthStore } from "@/store/auth"

const authStore = useAuthStore()
</script>
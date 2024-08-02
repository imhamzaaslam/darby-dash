<template>
  <div class="container d-flex justify-center align-center flex-column text-center">
    <div class="misc-wrapper">
      <ErrorHeader
        status-code="403"
        title="Unauthorized ⚠️"
        description="You are not authorized to access this page."
      />
    </div>
    <div class="button-wrapper">
      <VBtn
        to="/"
        class="mb-11"
      >
        Back to Home
      </VBtn>
    </div>
  </div>
</template>
  
<script setup>
import ErrorHeader from '@/components/ErrorHeader.vue'
</script>

<style scoped>
.container {
  height: 70vh;
}
</style>
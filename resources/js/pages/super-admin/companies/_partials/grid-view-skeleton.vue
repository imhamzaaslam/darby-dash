<template>
  <VCol
    v-for="n in 6"
    :key="n"
    cols="12"
    md="4"
  >
    <VCard class="pt-2">
      <VCardTitle>
        <VSkeletonLoader
          class="w-100"
          height="20px"
        />
      </VCardTitle>
      <VCardText class="px-3 pt-2">
        <VSkeletonLoader
          class="w-75 mb-2"
          height="10px"
        />
        <VSkeletonLoader
          class="w-50"
          height="10px"
        />
        <VSkeletonLoader
          class="w-100 mt-2"
          height="100px"
        />
      </VCardText>
    </VCard>
  </VCol>
</template>
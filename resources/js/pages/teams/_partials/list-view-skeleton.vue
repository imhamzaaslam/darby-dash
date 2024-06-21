<template>
  <VCol
    v-for="n in 3"
    :key="n"
    cols="12"
  >
    <VCard
      class="d-flex ps-4 py-1 align-center"
      style="height: 80px;"
    >
      <VCol cols="4">
        <div class="d-flex align-center">
          <VSkeletonLoader
            class="rounded-circle"
            style="width: 36px; height: 36px;"
          />
          <div class="d-flex flex-column ms-3">
            <VSkeletonLoader
              class="w-75 mb-1"
              height="10px"
            />
            <VSkeletonLoader
              class="w-50"
              height="10px"
            />
          </div>
        </div>
      </VCol>
      <VCol cols="4">
        <VSkeletonLoader
          class="w-75"
          height="10px"
        />
      </VCol>
      <VCol cols="3">
        <VSkeletonLoader
          class="w-50"
          height="10px"
        />
      </VCol>
      <VCol
        cols="1"
        class="ms-8"
      >
        <VSkeletonLoader
          class="w-25"
          height="10px"
        />
      </VCol>
    </VCard>
  </VCol>
</template>
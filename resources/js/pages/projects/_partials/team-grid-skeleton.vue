<template>
  <VCol
    v-for="n in 6"
    :key="n"
    cols="12"
    md="4"
  >
    <VCard>
      <div class="image-container">
        <VSkeletonLoader
          type="image"
          class="w-100"
          style="height: 150px;"
        />
      </div>
      <VCardText class="position-relative">
        <VAvatar
          size="75"
          class="avatar-center"
          color="primary"
        >
          <VSkeletonLoader
            class="rounded-circle"
            style="width: 75px; height: 75px;"
          />
        </VAvatar>
        <VRow class="mt-5">
          <VCol cols="8">
            <div class="d-flex align-center mt-1 ms-1">
              <div class="d-flex flex-column ms-3">
                <VSkeletonLoader
                  class="w-75 mb-1"
                  height="10px"
                />
                <VSkeletonLoader
                  class="w-50 mb-1"
                  height="10px"
                />
                <VSkeletonLoader
                  class="w-50"
                  height="10px"
                />
              </div>
            </div>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>
  </VCol>
</template>


<style scoped>
.image-container {
  position: relative;
}

.avatar-center {
  position: absolute;
  top: -30px;
  left: 10%;
  transform: translateX(-10%);
}

.mt-5 {
  margin-top: 5rem;
}
</style>
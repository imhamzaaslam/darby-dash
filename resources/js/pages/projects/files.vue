<script setup>
const files = ref([
  {
    location: 'assets/files/file-1.pdf',
    name: 'File 1',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'Apr 19, 2024',
    isHover: false,
    isFavourite: true,
  },
  {
    location: 'assets/files/file-2.pdf',
    name: 'File 2',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'Apr 20, 2024',
    isHover: false,
    isFavourite: false,
  },
  {
    location: 'assets/files/file-3.pdf',
    name: 'File 3',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'Apr 21, 2024',
    isHover: false,
    isFavourite: false,
  },
  {
    location: 'assets/files/file-4.pdf',
    name: 'File 4',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'Apr 22, 2024',
    isHover: false,
    isFavourite: false,
  },
  {
    location: 'assets/files/file-5.pdf',
    name: 'File 5',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'Apr 23, 2024',
    isHover: false,
    isFavourite: false,
  },
  {
    location: 'assets/files/file-6.pdf',
    name: 'File 6',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'Apr 24, 2024',
    isHover: false,
    isFavourite: false,
  },
  {
    location: 'assets/files/file-7.pdf',
    name: 'File 7',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'Apr 25, 2024',
    isHover: false,
    isFavourite: false,
  },
  {
    location: 'assets/files/file-8.pdf',
    name: 'File 8',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'June 5, 2024',
    isHover: false,
    isFavourite: false,
  },
  {
    location: 'assets/files/file-9.pdf',
    name: 'File 9',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'Apr 26, 2024',
    isHover: false,
    isFavourite: false,
  },
  {
    location: 'assets/files/file-10.pdf',
    name: 'File 10',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'Apr 27, 2024',
    isHover: false,
    isFavourite: false,
  },
  {
    location: 'assets/files/file-11.pdf',
    name: 'File 11',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'Apr 28, 2024',
    isHover: false,
    isFavourite: false,
  },
  {
    location: 'assets/files/file-12.pdf',
    name: 'File 12',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'Apr 29, 2024',
    isHover: false,
    isFavourite: false,
  },
  {
    location: 'assets/files/file-13.pdf',
    name: 'File 13',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'June 01, 2024',
    isHover: false,
    isFavourite: false,
  },
  {
    location: 'assets/files/file-14.pdf',
    name: 'File 14',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'June 01, 2024',
    isHover: false,
    isFavourite: false,
  },
  {
    location: 'assets/files/file-15.pdf',
    name: 'File 15',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'June 02, 2024',
    isHover: false,
    isFavourite: false,
  },
  {
    location: 'assets/files/file-16.pdf',
    name: 'File 16',
    icon: 'tabler-file',
    color: 'primary',
    uploadedAt: 'June 02, 2024',
    isHover: false,
    isFavourite: false,
  },
])

function deleteFile(index) {
  files.value.splice(index, 1)
}
function toggleFavourite(index) {
  files.value[index].isFavourite = !files.value[index].isFavourite
}
</script>

<template>
  <VRow>
    <VCol
      cols="12"
      class="d-flex justify-space-between align-center"
    >
      <h5 class="text-h5">
        Media
      </h5>
      <VBtn
        prepend-icon="tabler-upload"
        color="primary"
        size="small"
      >
        Upload File
      </VBtn>
    </VCol>
  </VRow>
  <VRow class="mt-3">
    <VCol
      v-for="(file, index) in files"
      :key="index"
      cols="12"
      md="3"
      sm="3"
    >
      <div class="position-relative">
        <VCard
          class="logistics-card-statistics cursor-pointer"
          :style="file.isHover ? { 'border-block-end': `2px solid rgba(var(--v-theme-${file.color}))` } : { 'border-block-end': `2px solid rgba(var(--v-theme-${file.color}), var(--v-disabled-opacity))` }"
          @mouseenter="file.isHover = true"
          @mouseleave="file.isHover = false"
        >
          <VCardText>
            <h5 class="text-h5 font-weight-medium mb-2 me-3">
              <p class="mb-0">
                {{ file.name }}
              </p>
              <p class="mb-0 text-xs">
                {{ file.location.substring(0, 15) }}...
              </p>
            </h5>
            <div class="d-flex justify-between align-center">
              <div>
                <p class="mb-0 text-xs">
                  {{ file.uploadedAt }}
                </p>
              </div>
              <div class="ps-14">
                <VIcon
                  icon="tabler-download"
                  color="info"
                  size="small"
                  class="me-1"
                />
                <VIcon
                  icon="tabler-eye"
                  color="info"
                  class="me-1"
                  size="small"
                />
                <VIcon
                  icon="tabler-trash"
                  color="error"
                  size="small"
                  @click="deleteFile(index)"
                />
              </div>
            </div>
          </VCardText>
        </VCard>
        <VIcon
          :icon="file.isFavourite ? 'tabler-star-filled' : 'tabler-star'"
          color="warning"
          size="small"
          class="isFavourite"
          @click="toggleFavourite(index)"
        />
      </div>
    </VCol>
  </VRow>
</template>

<style lang="scss" scoped>
@use "@core-scss/base/mixins" as mixins;

.logistics-card-statistics:hover {
  @include mixins.elevation(12);

  transition: all 0.1s ease-out;
}
.isFavourite{
    position: absolute !important;
    top: 5px;
    right: 8px;
}
</style>

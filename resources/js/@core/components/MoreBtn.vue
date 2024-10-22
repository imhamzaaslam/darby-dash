<script setup>
import { useRouter } from 'vue-router'
import { VMenu, VList, VListItem, VIcon } from 'vuetify/components'

const props = defineProps({
  menuList: {
    type: Array,
    required: false,
  },
  itemProps: {
    type: Boolean,
    required: false,
  },
})

const router = useRouter()

const navigate = to => {
  if (to) {
    router.push(to)
  }
}
</script>

<template>
  <IconBtn color="disabled">
    <VIcon icon="tabler-dots-vertical" />

    <VMenu
      v-if="props.menuList && props.menuList.length"
      activator="parent"
    >
      <VList>
        <VListItem
          v-for="(item, index) in props.menuList"
          :key="index"
          :item-props="props.itemProps"
          @click="navigate(item.to)"
        >
          {{ item.title }}
        </VListItem>
      </VList>
    </VMenu>
  </IconBtn>
</template>

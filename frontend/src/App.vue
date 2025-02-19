<script setup lang="ts">
import Toaster from '@/components/ui/toast/Toaster.vue'
const route = useRoute()

useHead({
  title: () => route.meta.title || 'Parcel Delivery Tracking',
  meta: [
    {
      property: 'og:title',
      content: () => route.meta.title,
    },
    {
      name: 'twitter:title',
      content: () => route.meta.title,
    },
  ],
})
</script>
<template>
  <Toaster />
  <router-view />
</template>

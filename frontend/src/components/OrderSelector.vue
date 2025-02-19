<script setup lang="ts">
import { useOrdersStore } from '@/stores/orders'
import { ref, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { type Order } from '@/composables/useOrders'
import { CommandDialog, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command'
import { Search } from 'lucide-vue-next'

const open = ref<boolean>(false)
const ordersStore = useOrdersStore()
const { selectedOrder, orders } = storeToRefs(ordersStore)

onMounted(() => {
  ordersStore.fetchOrders()
})

function handleOpenChange() {
  open.value = !open.value
}

function handleOrderClick(order: Order) {
  ordersStore.selectOrder(order)
  handleOpenChange()
}
</script>

<template>
  <Button
      :class="!selectedOrder ? 'animate-pulse' : ''"
      size="icon"
      variant="outline"
      @click="open = true"
    >
    <Search class="w-4 h-4" />
  </Button>
  <CommandDialog :open="open" @update:open="handleOpenChange">
    <CommandInput placeholder="Search order by reference" />
    <CommandList>
      <CommandEmpty>No results found.</CommandEmpty>
      <CommandGroup heading="Orders">
        <CommandItem class="cursor-pointer" v-for="order in orders" :key="order.id" @click="handleOrderClick(order)" :value="order">
          {{ order.reference }}
          <CommandShortcut>{{ order.status.name }}</CommandShortcut>
        </CommandItem>
      </CommandGroup>
    </CommandList>
  </CommandDialog>
</template>

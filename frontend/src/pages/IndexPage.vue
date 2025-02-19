<script setup lang="ts">
import { useOrdersStore } from '@/stores/orders'
import { storeToRefs } from 'pinia'
import { Skeleton } from '@/components/ui/skeleton'
import { RefreshCcw } from 'lucide-vue-next'

const ordersStore = useOrdersStore()
const { selectedOrder, statusHistory, isLoading, lastStatus } = storeToRefs(ordersStore)

</script>
<template>
  <div class="relative min-h-screen bg-background text-foreground flex">
    <div class="relative min-h-full container mx-auto p-4 flex flex-col gap-4">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Parcel Delivery Tracking</h1>
        <div class="flex gap-2">
          <DevToolbar />
          <OrderSelector />
          <ThemeSwitcher />
          <Button @click="ordersStore.fetchOrders()" variant="outline" size="icon" :disabled="isLoading">
            <RefreshCcw v-if="isLoading" class="animate-spin" />
            <RefreshCcw v-else />
          </Button>
        </div>
      </div>

      <Transition
        mode="out-in"
        enter-active-class="transition-all duration-300 ease-out"
        leave-active-class="transition-all duration-300 ease-in"
        enter-from-class="opacity-0 transform scale-95"
        leave-to-class="opacity-0 transform scale-95"
      >
        <div class="flex flex-col gap-4 relative w-full py-12 justify-center items-center" :key="selectedOrder ? 'order' : 'skeleton'">
          <h2 v-if="selectedOrder" class="text-3xl font-semibold">
            {{ selectedOrder.reference }}
          </h2>
          <Skeleton v-else class="w-1/2 h-12" />
        </div>
      </Transition>


      <Transition
        mode="out-in"
        enter-active-class="transition-all duration-300 ease-out"
        leave-active-class="transition-all duration-300 ease-in"
        enter-from-class="opacity-0 transform -translate-y-4"
        leave-to-class="opacity-0 transform translate-y-4"
      >
        <div class="p-4 mb-4 w-full" :key="selectedOrder ? 'stepper' : 'skeleton'">
          <OrderStatusStepper v-if="selectedOrder" :step="ordersStore.lastStatus" />
          <Skeleton v-else class="w-full h-[140px]" />
        </div>
      </Transition>

      <Transition
        mode="out-in"
        enter-active-class="transition-all duration-300 ease-out"
        leave-active-class="transition-all duration-300 ease-in"
        enter-from-class="opacity-0 transform translate-y-4"
        leave-to-class="opacity-0 transform -translate-y-4"
      >
        <div v-if="selectedOrder && selectedOrder.package || !selectedOrder" class="flex flex-col gap-4 relative w-full py-12 justify-center items-center grow h-full" :key="selectedOrder ? 'stepper' : 'skeleton'">
          <div class="relative flex justify-center items-center h-full w-full" v-if="selectedOrder">
            <ParcelMap class="rounded-lg w-full h-full overflow-hidden shadow-lg" />
          </div>
          <Skeleton v-else class="w-full h-full" />
        </div>
      </Transition>
    </div>
  </div>
</template>

<style scoped>
.v-enter-active,
.v-leave-active {
  transition-property: opacity, transform;
  transition-timing-function: ease-in-out;
}
</style>

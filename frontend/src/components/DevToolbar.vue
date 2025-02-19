<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { ArrowUpDown, Plus, MapPin, InfoIcon } from 'lucide-vue-next'
import { useOrdersStore } from '@/stores/orders'
import { storeToRefs } from 'pinia'
import { useToast } from '@/components/ui/toast/use-toast'

const ordersStore = useOrdersStore()
const { selectedOrder } = storeToRefs(ordersStore)
const { toast } = useToast()

function generateRandomOrderReference() {
  return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
}

const actions = [
  {
    label: 'Hover to show row data',
    icon: InfoIcon,
    tooltip: () => JSON.stringify(selectedOrder.value, null, 2),
    disabled: () => !selectedOrder.value,
  },
  {
    label: 'Change status of current order',
    icon: ArrowUpDown,
    tooltip: () => 'Change the status of the current order',
    choices: [
      {
        label: 'Created',
        value: 'CREATED',
      },
      {
        label: 'Payment',
        value: 'WAITING_FOR_PAYMENT',
      },
      {
        label: 'Shipped',
        value: 'SHIPPED',
      },
      {
        label: 'In Transit',
        value: 'IN_TRANSIT',
      },
      {
        label: 'Delivered',
        value: 'DELIVERED',
      },
    ],
    onClick: (e: MouseEvent, value: string) => {
      ordersStore.updateOrderStatus(value)
      toast({
        title: 'Order status updated',
        description: `Order status updated to ${value}`,
      })
    },
    disabled: () => !selectedOrder.value,
  },
  {
    label: 'Simulate tracking',
    icon: MapPin,
    tooltip: () => 'Simulate tracking',
    onClick: async (e: MouseEvent) => {
      e.preventDefault()
      if (!selectedOrder.value) return
      await ordersStore.simulatePackageLocation(selectedOrder.value.package.trackingNumber)
      toast({
        title: 'Package location updated',
        description: `Package ${selectedOrder.value.package.trackingNumber} location simulated`,
      })
    },
    disabled: () => !selectedOrder.value,
  },
  {
    label: 'Add a new order',
    icon: Plus,
    tooltip: () => 'Add a new order',
    onClick: async (e: MouseEvent) => {
      e.preventDefault()
      try {
        await ordersStore.createOrder({ reference: generateRandomOrderReference() })
      } catch (error) {
        toast({
          title: 'Error creating order',
          description: error instanceof Error ? error.message : 'An unknown error occurred',
          variant: 'destructive',
        })
      }
    },
  },
]
</script>

<template>
  <div class="flex justify-between items-center">
    <div class="container mx-auto flex items-center justify-between">
      <div class="flex items-center gap-2">
        <TooltipProvider>
          <Tooltip v-for="action in actions" :key="action.label">
            <TooltipTrigger as-child>
              <DropdownMenu v-if="action.choices && action.choices.length > 0">
                <DropdownMenuTrigger as-child>
                  <Button size="icon" variant="outline" :disabled="action.disabled?.()">
                    <component :is="action.icon" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                  <DropdownMenuItem v-for="choice in action.choices" :key="choice.value" @click="action?.onClick($event, choice.value)">
                    {{ choice.label }}
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
              <Button v-else size="icon" variant="outline" @click="action.onClick" :disabled="action.disabled?.()">
                <component :is="action.icon" />
              </Button>
            </TooltipTrigger>
            <TooltipContent>
              <pre class="text-xs max-h-[400px] overflow-auto">{{ action.tooltip() }}</pre>
            </TooltipContent>
          </Tooltip>
        </TooltipProvider>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { StatusHistory } from '@/stores/orders'
import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper'
import { Box, Check, CreditCard, Truck } from 'lucide-vue-next'

const props = defineProps<{
    step: StatusHistory | undefined
}>()

const steps = [
  {
    step: 1,
    title: 'Created',
    description: 'Order created',
    icon: Check,
    status: 'CREATED',
  },
  {
    step: 2,
    title: 'Payment',
    description: 'Payment received',
    icon: CreditCard,
    status: 'WAITING_FOR_PAYMENT',
  },
  {
    step: 3,
    title: 'Shipped',
    description: 'Shipped, waiting for transport',
    icon: Box,
    status: 'SHIPPED',
  },
  {
    step: 4,
    title: 'In transit',
    description: 'In transit',
    icon: Truck,
    status: 'IN_TRANSIT',
  },
  {
    step: 5,
    title: 'Delivered',
    description: 'Delivered',
    icon: Check,
    status: 'DELIVERED',
  }
]

const stepIndex = ref(0)
watch(props, (newProps) => {
  const { step } = newProps
  if (step === undefined) {
    stepIndex.value = 1
    return
  }
  const index = steps.find(s => s.status === step?.status)?.step ?? 1
  stepIndex.value = index
})

</script>

<template>
  <div class="flex flex-col gap-4 w-full">
    <Stepper v-model="stepIndex">
      <StepperItem
        v-for="step in steps"
        :key="step.step"
        v-slot="{ state }"
        class="relative flex w-full flex-col items-center justify-center"
        :step="step.step"
      >
        <StepperSeparator
          v-if="step.step !== steps[steps.length - 1].step"
          class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-primary"
        />

        <StepperTrigger as-child>
          <Button
            :variant="state === 'completed' || state === 'active' ? 'default' : 'outline'"
            size="icon"
            class="z-10 rounded-full shrink-0"
            :class="[state === 'active' && 'ring-2 ring-ring ring-offset-2 ring-offset-background']"
            :disabled="state !== 'completed'"
          >
            <component :is="step.icon" class="w-4 h-4" />
          </Button>
        </StepperTrigger>

        <div class="mt-5 flex flex-col items-center text-center">
          <StepperTitle
            :class="[state === 'active' && 'text-primary']"
            class="text-sm font-semibold transition lg:text-base"
          >
            {{ step.title }}
          </StepperTitle>
          <StepperDescription
            :class="[state === 'active' && 'text-primary']"
            class="sr-only text-xs text-muted-foreground transition md:not-sr-only lg:text-sm"
          >
            {{ step.description }}
          </StepperDescription>
        </div>
      </StepperItem>
    </Stepper>
  </div>
</template>

<script setup lang="ts">
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'
import { MoreHorizontal } from 'lucide-vue-next'

defineProps<{
  payment: {
    id: string
    amount: number
    status: string
    email: string
  }
}>()

const emit = defineEmits(['expand'])
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="h-8 w-8 p-0">
        <span class="sr-only">Open menu</span>
        <MoreHorizontal class="h-4 w-4" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuItem @click="$emit('expand')">
        View Details
      </DropdownMenuItem>
      <DropdownMenuItem>Edit</DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>

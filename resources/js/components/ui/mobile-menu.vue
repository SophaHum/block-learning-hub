<script setup>
import { Button } from '@/components/ui/button'
import { X } from 'lucide-vue-next'

defineProps({
  isOpen: Boolean,
  categories: Array,
  subNavItems: Array
})

defineEmits(['close'])
</script>

<template>
  <div v-if="isOpen"
       class="fixed inset-0 bg-background/80 backdrop-blur-sm z-50 lg:hidden"
       @click="$emit('close')">
    <div class="fixed inset-y-0 right-0 w-full max-w-xs bg-background p-6 shadow-lg"
         @click.stop>
      <div class="flex items-center justify-between mb-8">
        <h2 class="text-lg font-semibold">Menu</h2>
        <Button variant="ghost" size="icon" @click="$emit('close')">
          <X class="h-5 w-5" />
        </Button>
      </div>

      <!-- Mobile Navigation -->
      <nav class="space-y-6">
        <div class="space-y-3">
          <div class="font-medium">Categories</div>
          <a v-for="item in categories"
             :key="item.title"
             href="#"
             class="block px-3 py-2 rounded-md hover:bg-accent">
            <span class="mr-2">{{ item.icon }}</span>
            {{ item.title }}
          </a>
        </div>

        <div class="space-y-3">
          <div class="font-medium">Quick Links</div>
          <a v-for="item in subNavItems"
             :key="item.title"
             :href="item.href"
             class="block px-3 py-2 rounded-md hover:bg-accent">
            {{ item.title }}
          </a>
        </div>
      </nav>
    </div>
  </div>
</template>

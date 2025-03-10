<script setup>
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { ChevronDown, ChevronRight, Menu } from 'lucide-vue-next'

const isCollapsed = ref(false)
const expandedSections = ref(new Set())

const menuItems = [
  {
    title: 'Getting Started',
    icon: '🚀',
    items: [
      { name: 'Introduction', path: '/intro' },
      { name: 'Installation', path: '/install' },
      { name: 'Quick Start', path: '/quick-start' }
    ]
  },
  {
    title: 'Frontend Development',
    icon: '🎨',
    items: [
      { name: 'Vue.js Basics', path: '/vue' },
      { name: 'Components', path: '/components' },
      { name: 'State Management', path: '/state' }
    ]
  },
  {
    title: 'Backend Development',
    items: ['Laravel', 'Node.js', 'Django', 'Spring']
  },
  {
    title: 'Database',
    items: ['MySQL', 'PostgreSQL', 'MongoDB', 'Redis']
  }
]

const toggleSection = (title) => {
  if (expandedSections.value.has(title)) {
    expandedSections.value.delete(title)
  } else {
    expandedSections.value.add(title)
  }
}

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value
}
</script>

<template>
  <aside :class="[
    'transition-all duration-300 fixed left-0 top-16 lg:top-20 h-[calc(100vh-4rem)] lg:h-[calc(100vh-5rem)] bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60',
    isCollapsed ? 'w-20' : 'w-72',
    $attrs.class
  ]">
    <div class="flex items-center justify-between p-6">
      <span v-if="!isCollapsed" class="font-semibold">Navigation</span>
      <Button variant="ghost" size="icon" @click="toggleSidebar">
        <Menu class="h-4 w-4" />
      </Button>
    </div>

    <nav class="space-y-2 p-4">
      <div v-for="section in menuItems" :key="section.title" class="pb-3">
        <button
          @click="toggleSection(section.title)"
          class="flex items-center w-full px-4 py-3 hover:bg-accent rounded-md gap-3"
        >
          <span class="text-lg">{{ section.icon }}</span>
          <span v-if="!isCollapsed" class="flex-1 text-base font-medium">{{ section.title }}</span>
          <component
            :is="expandedSections.has(section.title) ? ChevronDown : ChevronRight"
            v-if="!isCollapsed"
            class="h-4 w-4"
          />
        </button>

        <div v-if="!isCollapsed && expandedSections.has(section.title)"
             class="ml-4 space-y-2 mt-2">
          <Button
            v-for="item in section.items"
            :key="item.name"
            variant="ghost"
            class="w-full justify-start text-base pl-8 py-2 h-auto"
          >
            {{ item.name }}
          </Button>
        </div>
      </div>
    </nav>
  </aside>
</template>

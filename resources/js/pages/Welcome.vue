<template>
  <div class="min-h-screen bg-background">
    <!-- Main Navbar -->
    <NavigationMenu class="bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 fixed w-full z-10">
      <div class="container mx-auto px-4 lg:px-6 flex h-16 lg:h-20 items-center justify-between">
        <NavigationMenuList class="hidden lg:flex gap-8">
          <NavigationMenuItem>
            <a href="/" class="text-xl lg:text-2xl font-bold tracking-tight hover:text-primary transition-colors">DevBlog</a>
          </NavigationMenuItem>
          <NavigationMenuItem v-for="item in categories" :key="item.title">
            <a href="#" class="text-base font-medium text-muted-foreground transition-colors hover:text-primary px-3 py-2 rounded-md hover:bg-accent">
              {{ item.title }}
            </a>
          </NavigationMenuItem>
        </NavigationMenuList>

        <!-- Mobile Logo -->
        <a href="/" class="lg:hidden text-xl font-bold">DevBlog</a>

        <!-- Mobile Menu Button -->
        <Button variant="ghost" size="icon" class="lg:hidden" @click="isMobileMenuOpen = true">
          <Menu class="h-5 w-5" />
        </Button>
      </div>
    </NavigationMenu>

    <!-- Mobile Menu -->
    <MobileMenu
      :is-open="isMobileMenuOpen"
      :categories="categories"
      :sub-nav-items="subNavItems"
      @close="isMobileMenuOpen = false"
    />

    <!-- Page Header with Breadcrumb -->
    <div class="fixed top-16 lg:top-20 left-0 lg:left-72 right-0 h-14 lg:h-16 bg-background/95 backdrop-blur z-10">
      <div class="px-4 lg:px-8 h-full flex items-center justify-between gap-4 lg:gap-6">
        <Breadcrumb :items="breadcrumbItems" class="hidden sm:flex" />

        <div class="flex items-center gap-2 lg:gap-4">
          <Button variant="ghost" size="sm" class="hidden sm:inline-flex">Share</Button>
          <Button variant="ghost" size="sm" class="hidden sm:inline-flex">Print</Button>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <Sidebar class="hidden lg:block" />

    <!-- Main Content -->
    <div class="lg:ml-72 pt-28 lg:pt-36">
      <!-- Hero Section -->
      <div class="pb-12">
        <div class="container">
          <div class="text-center space-y-4">
            <h1 class="text-4xl font-bold tracking-tight lg:text-5xl">
              Learn. Build. Master.
            </h1>
            <p class="mx-auto max-w-[700px] text-lg text-muted-foreground">
              Explore the latest in programming, framework updates, and networking tutorials.
            </p>
            <div class="flex justify-center gap-4">
              <Button variant="default">Get Started</Button>
              <Button variant="outline">Browse Tutorials</Button>
            </div>
          </div>
        </div>
      </div>

      <!-- Featured Posts Grid -->
      <div class="container py-8 lg:py-12">
        <h2 class="text-2xl font-bold mb-8">Latest Posts</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
          <Card v-for="post in posts" :key="post.title">
            <CardHeader>
              <CardTitle>{{ post.title }}</CardTitle>
              <CardDescription>{{ post.category }}</CardDescription>
            </CardHeader>
            <CardContent>
              <p>{{ post.description }}</p>
            </CardContent>
            <CardFooter class="flex justify-between">
              <span class="text-sm text-muted-foreground">{{ post.readTime }} read</span>
              <Button variant="ghost">Read more →</Button>
            </CardFooter>
          </Card>
        </div>
      </div>

      <!-- Categories Section -->
      <div class="bg-muted py-8 lg:py-12">
        <div class="container">
          <h2 class="text-2xl font-bold mb-8">Browse by Category</h2>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <Card v-for="category in categories" :key="category.title" class="hover:bg-muted/50 transition-colors cursor-pointer">
              <CardHeader>
                <CardTitle class="flex items-center gap-2">
                  <span>{{ category.icon }}</span>
                  {{ category.title }}
                </CardTitle>
                <CardDescription>{{ category.description }}</CardDescription>
              </CardHeader>
            </Card>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card'
import { NavigationMenu, NavigationMenuItem, NavigationMenuList } from '@/components/ui/navigation-menu'
import Sidebar from '@/components/ui/sidebar.vue'
import {Breadcrumb} from '@/components/ui/breadcrumb'
import { Menu } from 'lucide-vue-next'
import MobileMenu from '@/components/ui/mobile-menu.vue'

const categories = ref([
  { title: 'Tutorials', description: 'Step-by-step learning guides', icon: '📚' },
  { title: 'Framework Updates', description: 'Latest changes and releases', icon: '🔄' },
  { title: 'Networking', description: 'Infrastructure and protocols', icon: '🌐' },
  { title: 'Best Practices', description: 'Coding standards and tips', icon: '✨' }
])

const posts = ref([
  {
    title: 'Understanding Laravel 12\'s New Features',
    category: 'Framework Updates',
    description: 'Explore the latest updates and improvements in Laravel 12...',
    readTime: '5 min'
  },
  // Add more posts here
])

const subNavItems = ref([
  { title: 'Popular', href: '#' },
  { title: 'Latest', href: '#' },
  { title: 'Trending', href: '#' },
  { title: 'Most Viewed', href: '#' }
])

const breadcrumbItems = ref([
  { label: 'Home', href: '/' },
  { label: 'Tutorials', href: '/tutorials' },
  { label: 'Vue.js Basics', href: '#' }
])

const isMobileMenuOpen = ref(false)
</script>

<!-- <style scoped>
.container {
  @apply max-w-7xl mx-auto px-4;
}
</style> -->

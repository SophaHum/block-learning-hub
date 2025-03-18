<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Pencil, Trash2, ArrowLeft, Calendar, User } from 'lucide-vue-next';
import { formatDate } from '@/utils';

interface User {
  id: number;
  name: string;
}

interface Post {
  id: number;
  title: string;
  excerpt: string;
  featured_image: string | null;
  published_at: string;
  user: User;
}

interface Category {
  id: number;
  name: string;
  description: string | null;
}

interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

interface Pagination<T> {
  data: T[];
  from: number;
  to: number;
  total: number;
  links: PaginationLink[];
  current_page: number;
  last_page: number;
}

interface Props {
  category: Category;
  posts: Pagination<Post>;
  can: {
    edit_categories: boolean;
    delete_categories: boolean;
  };
}

const props = defineProps<Props>();
</script>

<template>
  <AppLayout>
    <Head :title="props.category.name" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Category Header -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-start justify-between">
              <div>
                <div class="flex items-center mb-4">
                  <Link :href="route('categories.index')" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                    <ArrowLeft class="w-5 h-5" />
                  </Link>
                  <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ props.category.name }}</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-300">{{ props.category.description }}</p>
              </div>

              <div class="flex space-x-2">
                <Link
                  v-if="props.can.edit_categories"
                  :href="route('categories.edit', { id: props.category.id })"
                  class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring ring-indigo-300 disabled:opacity-25 transition"
                >
                  <Pencil class="w-4 h-4 mr-1" />
                  Edit
                </Link>

                <Link
                  v-if="props.can.delete_categories"
                  :href="route('categories.destroy', { id: props.category.id })"
                  method="delete"
                  as="button"
                  class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring ring-red-300 disabled:opacity-25 transition"
                  @click.prevent="router.delete(route('categories.destroy', { id: props.category.id }))"
                >
                  <Trash2 class="w-4 h-4 mr-1" />
                  Delete
                </Link>
              </div>
            </div>

            <div class="mt-4">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                {{ props.posts.total }} Posts
              </span>
            </div>
          </div>
        </div>

        <!-- Posts in this category -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Posts in this category</h2>

            <div v-if="props.posts.data.length === 0" class="text-center py-10">
              <p class="text-gray-500 dark:text-gray-400">No posts found in this category</p>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
              <div
                v-for="post in props.posts.data"
                :key="post.id"
                class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition"
              >
                <div v-if="post.featured_image" class="h-40 overflow-hidden">
                  <img :src="post.featured_image" :alt="post.title" class="w-full h-full object-cover" />
                </div>
                <div class="p-4">
                  <Link
                    :href="route('posts.show', { id: post.id })"
                    class="text-lg font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400"
                  >
                    {{ post.title }}
                  </Link>

                  <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                    {{ post.excerpt }}
                  </p>

                  <div class="mt-4 flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center">
                      <User class="w-4 h-4 mr-1" />
                      {{ post.user.name }}
                    </div>
                    <div class="flex items-center">
                      <Calendar class="w-4 h-4 mr-1" />
                      {{ formatDate(post.published_at) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="props.posts.data.length > 0" class="mt-8 flex items-center justify-between">
              <div class="text-sm text-gray-700 dark:text-gray-300">
                Showing {{ props.posts.from }} to {{ props.posts.to }} of {{ props.posts.total }} posts
              </div>
              <div class="flex-1 flex justify-end">
                <div class="flex space-x-2">
                  <Link
                    v-for="(link, key) in props.posts.links"
                    :key="key"
                    :href="link.url ?? ''"
                    :class="{
                      'px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200': true,
                      'opacity-50 cursor-default': !link.url,
                      'bg-blue-500 text-white hover:bg-blue-600': link.active
                    }"
                  >
                    {{ link.label }}
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

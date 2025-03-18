<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Save, X } from 'lucide-vue-next';

const props = defineProps({
  category: {
    type: Object as () => {
      id: number;
      name: string;
      description?: string;
    },
    required: true
  },
  can: Object,
});

const form = useForm({
  name: props.category.name,
  description: props.category.description || '',
});

const submit = () => {
  form.put(route('categories.update', { id: props.category.id }));
};
</script>

<template>
  <AppLayout>
    <Head title="Edit Category" />

    <div class="py-12">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="mb-8">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Edit Category</h1>
              <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Update category details
              </p>
            </div>

            <form @submit.prevent="submit">
              <div class="grid gap-6">
                <!-- Name -->
                <div class="space-y-2">
                  <label for="name" class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    Category Name
                  </label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white sm:text-sm"
                    placeholder="Enter category name"
                    required
                  />
                  <div v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                  <label for="description" class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    Description
                  </label>
                  <textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white sm:text-sm"
                    placeholder="Enter category description (optional)"
                  ></textarea>
                  <div v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</div>
                </div>

                <div class="flex justify-end space-x-3">
                  <a
                    :href="route('categories.show', { id: category.id })"
                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition"
                  >
                    <X class="w-4 h-4 mr-2" />
                    Cancel
                  </a>

                  <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition"
                    :disabled="form.processing"
                  >
                    <Save class="w-4 h-4 mr-2" />
                    Update Category
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

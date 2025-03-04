<template>
    <div>
      <h1>Create Post</h1>
      <form @submit.prevent="submit">
        <input v-model="form.title" placeholder="Title" />
        <select v-model="form.category_id">
          <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
        </select>
        <TiptapEditor v-model="form.content" />
        <button type="submit">Save</button>
      </form>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref } from 'vue';
  import { useForm } from '@inertiajs/vue3';
  import TiptapEditor from '@/Components/TiptapEditor.vue';
  
  const props = defineProps<{
    categories: Array<{ id: number; name: string }>;
  }>();
  
  const form = useForm({
    title: '',
    content: '',
    category_id: null,
  });
  
  const submit = () => {
    form.post('/posts');
  };
  </script>
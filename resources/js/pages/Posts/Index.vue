<template>
    <div>
      <h1>Posts</h1>
      <table>
        <thead>
          <tr>
            <th>Title</th>
           
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="post in posts" :key="post.id">
            <td>{{ post.title }}</td>
            <!-- <td>{{ post.category.name }}</td> -->
            <td>
              <button>Edit</button>
              <button>Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script setup lang="ts">
  import { defineProps } from 'vue';
  
  defineProps<{
    posts: Array<{
      id: number;
      title: string;
    //   category: { name: string };
    }>;
  }>();
  </script>
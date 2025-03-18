<template>
  <div class="container mx-auto p-6">
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-3xl font-bold">{{ post.title }}</h1>
      <div class="flex gap-2">
        <Link v-if="can.edit_posts" :href="route('posts.edit', { id: post.id })" class="btn btn-outline">
          Edit Post
        </Link>
        <Link :href="route('posts.index')" class="btn btn-outline">
          Back to Posts
        </Link>
      </div>
    </div>

    <Card>
      <CardHeader>
        <CardTitle>{{ post.title }}</CardTitle>
        <CardDescription>
          <div class="flex items-center gap-4">
            <span>Published in: {{ post.category?.name }}</span>
            <span v-if="post.published_at" class="text-muted-foreground">
              Published on: {{ formatDate(post.published_at) }}
            </span>
            <span v-else class="text-orange-500">Draft</span>
          </div>
        </CardDescription>
      </CardHeader>
      <CardContent>
        <Alert class="mb-6">
          <AlertTitle>Excerpt</AlertTitle>
          <AlertDescription>
            {{ post.excerpt }}
          </AlertDescription>
        </Alert>

        <!-- Content Display -->
        <div class="mt-6 prose prose-slate max-w-none" v-html="post.content"></div>
      </CardContent>
      <CardFooter class="flex justify-between">
        <div class="flex items-center">
          <Avatar v-if="post.user">
            <AvatarImage :src="post.user.avatar || ''" />
            <AvatarFallback>{{ getUserInitials(post.user.name) }}</AvatarFallback>
          </Avatar>
          <span class="ml-2 text-sm text-muted-foreground">
            By {{ post.user?.name || 'Unknown Author' }}
          </span>
        </div>
        <div v-if="can.delete_posts">
          <Button variant="destructive" @click="confirmDelete">
            Delete Post
          </Button>
        </div>
      </CardFooter>
    </Card>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:open="showDeleteDialog">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Are you sure you want to delete this post?</DialogTitle>
          <DialogDescription>
            This action cannot be undone. This will permanently delete this post and remove the data from the server.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" @click="showDeleteDialog = false">Cancel</Button>
          <Button variant="destructive" :disabled="form.processing" @click="deletePost">
            <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
            Delete Post
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import  {format}  from 'date-fns';
import { Loader2 } from 'lucide-vue-next';
import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent,
  CardFooter
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle
} from '@/components/ui/dialog';
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar';
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert';

const props = defineProps<{
  post: {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    content: string;
    category_id: number;
    category?: { id: number; name: string };
    user?: { id: number; name: string; avatar?: string };
    published_at?: string;
    created_at: string;
  };
  can: {
    edit_posts: boolean;
    delete_posts: boolean;
  };
}>();

const showDeleteDialog = ref(false);
const form = useForm({});

// Format date for display
const formatDate = (dateString: string) => {
  try {
    return format(new Date(dateString), 'MMMM d, yyyy');
  } catch {
    return dateString;
  }
};

// Get user initials for avatar fallback
const getUserInitials = (name: string) => {
  return name
    .split(' ')
    .map(part => part[0])
    .join('')
    .toUpperCase()
    .substring(0, 2);
};

const confirmDelete = () => {
  showDeleteDialog.value = true;
};

const deletePost = () => {
  form.delete(route('posts.destroy', { id: props.post.id.toString() }), {
    onSuccess: () => {
      showDeleteDialog.value = false;
    },
  });
};
</script>

<style>
/* Additional styles for rich text content */
.prose pre {
  background-color: #0d1117;
  color: #e6edf3;
  padding: 1rem;
  border-radius: 0.5rem;
  overflow: auto;
}

.prose table {
  border-collapse: collapse;
  width: 100%;
  margin: 1rem 0;
}

.prose table td,
.prose table th {
  border: 1px solid #e2e8f0;
  padding: 0.5rem;
}

.prose table th {
  background-color: #f8fafc;
  font-weight: 600;
}

.prose blockquote {
  border-left: 4px solid #e2e8f0;
  padding-left: 1rem;
  color: #64748b;
  font-style: italic;
}

.prose ul[data-type="taskList"] {
  list-style-type: none;
  padding-left: 0;
}

.prose ul[data-type="taskList"] li {
  display: flex;
  align-items: flex-start;
  margin-bottom: 0.5em;
}

.prose ul[data-type="taskList"] li input[type="checkbox"] {
  margin-right: 0.5em;
}
</style>

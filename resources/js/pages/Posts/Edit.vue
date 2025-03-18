<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch, computed, onMounted, nextTick } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import type { ErrorBag, Errors, FormDataConvertible } from '@inertiajs/core';
import Editor from 'primevue/editor';
import { useForm as useVeeForm } from 'vee-validate';
import { z } from 'zod';
import { toFormValidator } from '@vee-validate/zod';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Form,
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage
} from '@/components/ui/form';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { ImagePlus, Trash } from 'lucide-vue-next';
import DeleteConfirmationDialog from '@/components/DeleteConfirmationDialog.vue';

// Define the Category interface
interface Category {
    id: number;
    name: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: '/posts',
    },
    {
        title: 'Edit',
        href: '#',
    },
];

// Setup props for categories and post data
const props = defineProps<{
    post: {
        id: number;
        title: string;
        slug: string;
        excerpt: string;
        content: string;
        featured_image: string | null;
        category_id: number;
        is_published: boolean;
        published_at: string | null;
    };
    categories: Category[];
}>();

// Form schema with Zod - Using optional strings for nullable fields
const formSchema = z.object({
    title: z.string().min(1, 'Title is required'),
    excerpt: z.string().min(1, 'Excerpt is required'),
    category_id: z.string().min(1, 'Category is required'),
    featured_image: z.any().optional(),
    is_published: z.boolean().default(false),
    published_at: z.string().optional().nullable()
});

// Type definition for form data
interface PostFormData {
    title: string;
    excerpt: string;
    content: string;
    category_id: string;
    featured_image: File | null;
    is_published: boolean;
    published_at: string | null;
    [key: string]: FormDataConvertible;
}

// Define the editor options type
interface EditorOptions {
    modules: {
        toolbar: (string | { [key: string]: any })[][];
    };
}

// Improved content handling
interface SanitizeHtml {
    (html: string): string;
}

const sanitizeHtml: SanitizeHtml = (html) => {
    // Basic sanitization to ensure HTML is properly formatted for the editor
    return html ? html.trim() : '';
};

// For debugging purposes - log the content received from props
console.log('Post content from props:', {
  content: props.post.content,
  contentType: typeof props.post.content,
  contentLength: props.post.content?.length,
  hasHtmlTags: props.post.content?.includes('<'),
});

// For production/development mode check
const isDev = ref(process.env.NODE_ENV !== 'production');

// For content editor - initialize with sanitized content
const editorContent = ref(sanitizeHtml(props.post.content));
const contentError = ref<string | null>(null);

// For image upload preview
const imagePreview = ref<string | null>(props.post.featured_image);
const fileInputRef = ref<HTMLInputElement | null>(null);

// Use Inertia's form handling with proper type definitions and initialize with post data
const form = useForm<PostFormData>({
    title: props.post.title,
    excerpt: props.post.excerpt,
    content: props.post.content, // Initialize content
    category_id: String(props.post.category_id),
    featured_image: null,
    is_published: props.post.is_published,
    published_at: props.post.published_at
});

// Initialize the editor with a better approach to load content
onMounted(async () => {
  console.log('onMounted - Initial state:', {
    content: props.post.content,
    editorContent: editorContent.value,
    formContent: form.content
  });

  // Wait for next tick to ensure the component is fully rendered
  await nextTick();

  // First approach: Force-set the content value
  editorContent.value = props.post.content;
  form.content = props.post.content;

  // Second approach: Use a different technique with longer timeout
  setTimeout(() => {
    // Try to get a reference to the editor element
    const editorElement = document.querySelector('.p-editor-content .ql-editor');
    if (editorElement) {
      // Directly set the HTML content
      editorElement.innerHTML = props.post.content || '';

      // Force sync form content with editor content
      form.content = props.post.content || '';
      editorContent.value = props.post.content || '';

      console.log('Content set directly to editor DOM');
    } else {
      console.warn('Editor element not found');
    }
  }, 1000); // Longer timeout (1 second)

  // Add a backup approach - in case the editor is slow to load
  setTimeout(() => {
    // Final check and force update if needed
    if (!editorContent.value && props.post.content) {
      console.log('Backup content loading triggered');

      // Try once more with the editor
      const editorEl = document.querySelector('.p-editor-content .ql-editor');
      if (editorEl) {
        editorEl.innerHTML = props.post.content;
      }

      // Force the form and editor content values
      editorContent.value = props.post.content;
      form.content = props.post.content;
    }
  }, 2000); // Much longer timeout as last resort
});

// PrimeVue Editor specific configuration
const editorOptions = {
  modules: {
    toolbar: [
      ['bold', 'italic', 'underline', 'strike'],
      ['blockquote', 'code-block'],
      [{ 'header': 1 }, { 'header': 2 }],
      [{ 'list': 'ordered'}, { 'list': 'bullet' }],
      [{ 'indent': '-1'}, { 'indent': '+1' }],
      [{ 'direction': 'rtl' }],
      [{ 'size': ['small', false, 'large', 'huge'] }],
      [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
      [{ 'color': [] }, { 'background': [] }],
      [{ 'font': [] }],
      [{ 'align': [] }],
      ['clean'],
      ['link', 'image']
    ]
  }
};

// VeeValidate form for validation with matching types
const veeForm = useVeeForm<{
    title: string;
    excerpt: string;
    category_id: string;
    featured_image: File | null;
    is_published: boolean;
    published_at: string | null;
    content: string; // Add content field
}>({
    validationSchema: toFormValidator(formSchema),
    initialValues: {
        title: props.post.title,
        excerpt: props.post.excerpt,
        category_id: String(props.post.category_id),
        featured_image: null,
        is_published: props.post.is_published,
        published_at: props.post.published_at,
        content: props.post.content // Initialize content
    }
});

// Remove duplicated watchers - keep only the most effective one
watch(() => props.post, (newPost) => {
  if (newPost && newPost.content) {
    editorContent.value = newPost.content;
    form.content = newPost.content;
  }
}, { immediate: true, deep: true });

// Sync veeForm values with Inertia form
watch(() => veeForm.values, (newValues) => {
    form.title = newValues.title;
    form.excerpt = newValues.excerpt;
    form.category_id = newValues.category_id;
    form.is_published = newValues.is_published;
    form.content = newValues.content; // Sync content
    // Handle potentially undefined published_at
    form.published_at = newValues.published_at || null;
}, { deep: true });

// Sync editorContent with form.content
watch(editorContent, (newContent) => {
    form.content = newContent;
});

const handleImageChange = (event: Event) => {
    const fileInput = event.target as HTMLInputElement;
    const file = fileInput.files?.[0];

    if (file) {
        form.featured_image = file;
        veeForm.setFieldValue('featured_image', file);

        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const triggerFileInput = () => {
    fileInputRef.value?.click();
};

// Validate content separately
const validateContent = () => {
    if (!editorContent.value) {
        contentError.value = 'Content is required';
        return false;
    }
    contentError.value = null;
    return true;
};

// Computed property for published_at v-model binding
const publishedAtModel = computed({
    get() {
        // Format the date properly for datetime-local input if it exists
        if (form.published_at) {
            // Convert date string to a proper ISO format for datetime-local inputs
            const date = new Date(form.published_at);
            if (!isNaN(date.getTime())) {
                // Format: YYYY-MM-DDThh:mm
                return new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
                    .toISOString()
                    .slice(0, 16);
            }
        }
        return '';
    },
    set(value) {
        form.published_at = value || null;
    }
});

// Fix the issue with file uploads and content not being sent properly
const onSubmit = () => {
    // Check if required fields are filled
    if (!form.title || !form.excerpt || !form.category_id) {
        if (!form.title) veeForm.setFieldError('title', 'Title is required');
        if (!form.excerpt) veeForm.setFieldError('excerpt', 'Excerpt is required');
        if (!form.category_id) veeForm.setFieldError('category_id', 'Category is required');
        return;
    }

    const isContentValid = validateContent();
    if (!isContentValid) return;

    // Debug: Log form values before submission
    console.log('Form data before submission:', {
        title: form.title,
        excerpt: form.excerpt,
        content: form.content,
        category_id: form.category_id,
        featured_image: form.featured_image,
        is_published: form.is_published,
        published_at: form.published_at
    });

    // Create FormData object manually to ensure all fields are included
    const formData = new FormData();
    formData.append('title', form.title);
    formData.append('excerpt', form.excerpt);
    formData.append('content', form.content);
    formData.append('category_id', form.category_id);
    formData.append('is_published', form.is_published ? '1' : '0');

    if (form.published_at) {
        formData.append('published_at', form.published_at);
    }

    if (form.featured_image) {
        formData.append('featured_image', form.featured_image);
    }

    // Add the method spoofing parameter
    formData.append('_method', 'PUT');

    // Use Inertia's form.submit() with the manual FormData
    router.post(`/posts/${props.post.id}`, formData, {
        forceFormData: true,
        preserveScroll: true,
        headers: {
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        },
        onSuccess: () => {
            router.visit('/posts');
        },
        onError: (errors) => {
            console.error('Submission errors:', errors);
            // Debug: Log what was sent
            console.log('Request data that was sent:', Object.fromEntries(formData.entries()));

            // Map errors to form fields
            for (const key in errors) {
                if (key === 'content') {
                    contentError.value = errors[key];
                } else if (key in veeForm.values) {
                    veeForm.setFieldError(key as keyof typeof veeForm.values, errors[key]);
                }
            }
        }
    });
};

// Delete confirmation dialog ref
const deleteDialog = ref<InstanceType<typeof DeleteConfirmationDialog> | null>(null);

// Function to handle post deletion with confirmation
const deletePost = () => {
    deleteDialog.value?.showDialog();
};

// Handle confirmed deletion
const handleDeleteConfirm = () => {
    router.delete(`/posts/${props.post.id}`);
};

// Add a special method to force content update (can be triggered from a button if needed)
const forceContentUpdate = () => {
  editorContent.value = props.post.content;
  form.content = props.post.content;
  console.log('Force update triggered', {
    editorContent: editorContent.value,
    formContent: form.content
  });
};
</script>

<template>
    <Head title="Edit Post" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container py-6">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Edit Post</CardTitle>
                        <CardDescription>Update your post details</CardDescription>
                    </div>
                    <Button
                        variant="destructive"
                        @click="deletePost"
                        class="bg-red-600 hover:bg-red-700 dark:bg-red-800 dark:hover:bg-red-900 dark:text-white"
                    >
                        <Trash class="mr-2 h-4 w-4" />
                        Delete Post
                    </Button>
                </CardHeader>

                <Form @submit="onSubmit">
                    <CardContent class="space-y-6">
                        <!-- Title -->
                        <FormField
                            name="title"
                            :value="veeForm.values.title"
                            @update:value="(val: string) => { veeForm.setFieldValue('title', val); form.title = val; }"
                        >
                            <FormItem>
                                <FormLabel>Title</FormLabel>
                                <FormControl>
                                    <Input
                                        placeholder="Enter post title"
                                        v-model="form.title"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <!-- Category -->
                        <FormField
                            name="category_id"
                            :value="veeForm.values.category_id"
                            @update:value="(val: string) => { veeForm.setFieldValue('category_id', val); form.category_id = val; }"
                        >
                            <FormItem>
                                <FormLabel>Category</FormLabel>
                                <Select v-model="form.category_id">
                                    <FormControl>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select a category" />
                                        </SelectTrigger>
                                    </FormControl>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="category in categories"
                                            :key="category.id"
                                            :value="String(category.id)"
                                        >
                                            {{ category.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <!-- Excerpt -->
                        <FormField
                            name="excerpt"
                            :value="veeForm.values.excerpt"
                            @update:value="(val: string) => { veeForm.setFieldValue('excerpt', val); form.excerpt = val; }"
                        >
                            <FormItem>
                                <FormLabel>Excerpt</FormLabel>
                                <FormControl>
                                    <Textarea
                                        placeholder="Brief summary of your post (will be displayed in blog listings)"
                                        rows="3"
                                        v-model="form.excerpt"
                                    />
                                </FormControl>
                                <FormDescription>
                                    This will be displayed on the blog homepage
                                </FormDescription>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <!-- Featured Image -->
                        <FormField
                            name="featured_image"
                            :value="veeForm.values.featured_image"
                            @update:value="(val: File | null) => { veeForm.setFieldValue('featured_image', val); }"
                        >
                            <FormItem>
                                <FormLabel>Featured Image</FormLabel>
                                <FormControl>
                                    <div
                                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center cursor-pointer hover:border-primary"
                                        @click="triggerFileInput"
                                    >
                                        <div v-if="imagePreview" class="mb-4">
                                            <img :src="imagePreview" alt="Featured image" class="max-h-40 rounded-md" />
                                        </div>
                                        <ImagePlus class="h-10 w-10 text-gray-400" />
                                        <p class="mt-2 text-sm text-gray-500">Click to change the image</p>
                                        <input
                                            type="file"
                                            ref="fileInputRef"
                                            accept="image/*"
                                            class="hidden"
                                            @change="handleImageChange"
                                        />
                                    </div>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <!-- Content Editor -->
                        <div class="space-y-2">
                            <Label for="content">Content</Label>

                            <!-- Use the style attribute to ensure the component is displayed correctly -->
                            <div class="editor-wrapper" style="min-height: 320px; display: block;">
                                <Editor
                                    id="content"
                                    v-model="editorContent"
                                    editorStyle="min-height: 320px"
                                    class="border rounded-md w-full"
                                    :readonly="false"
                                    :options="editorOptions"
                                    @text-change="form.content = editorContent"
                                    @blur="validateContent"
                                />
                            </div>

                            <p v-if="contentError" class="text-sm text-destructive mt-1">
                                {{ contentError }}
                            </p>
                            <p v-if="form.errors.content" class="text-sm text-destructive mt-1">
                                {{ form.errors.content }}
                            </p>
                        </div>

                        <!-- Publish Settings -->
                        <FormField
                            name="is_published"
                            :value="veeForm.values.is_published"
                            @update:value="(val: boolean) => { veeForm.setFieldValue('is_published', val); form.is_published = val; }"
                        >
                            <div class="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    id="is_published"
                                    v-model="form.is_published"
                                    class="rounded border-gray-300 text-primary focus:ring-primary"
                                />
                                <Label for="is_published">Publish immediately</Label>
                            </div>
                        </FormField>

                        <!-- Published Date (if not publishing immediately) -->
                        <FormField
                                        v-model="publishedAtModel as string | undefined"
                            name="published_at"
                            :value="veeForm.values.published_at"
                            @update:value="(val: string | null) => { veeForm.setFieldValue('published_at', val); form.published_at = val || null; }"
                        >
                            <FormItem>
                                <FormLabel>Schedule Publication</FormLabel>
                                <FormControl>
                                    <Input
                                        type="datetime-local"
                                        v-model="publishedAtModel as string | undefined"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </CardContent>

                    <CardFooter class="flex gap-4 justify-end">
                        <Button type="button" variant="outline" @click="() => router.visit('/posts')">
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            class="w-32"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Saving...' : 'Update Post' }}
                        </Button>
                    </CardFooter>
                </Form>

                <!-- Add the DeleteConfirmationDialog component -->
                <DeleteConfirmationDialog
                    ref="deleteDialog"
                    title="Delete Post"
                    description="Are you sure you want to delete this post? This action cannot be undone and all content will be permanently removed."
                    dangerButtonText="Delete Post"
                    @confirm="handleDeleteConfirm"
                />
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage, useForm } from '@inertiajs/vue3';
import type { ErrorBag, Errors } from '@inertiajs/core';
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
import { ImagePlus } from 'lucide-vue-next';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create',
        href: '/posts/create',
    },
];

// Setup props for categories from controller
defineProps<{
    categories?: Array<{
        id: number;
        name: string;
    }>;
}>();

// Form schema with Zod - Using optional strings for nullable fields
const formSchema = z.object({
    title: z.string(),
    excerpt: z.string(),
    category_id: z.string(),
    content: z.string(), // Add content to the schema
    featured_image: z.any().optional(),
    is_published: z.boolean().default(false),
    published_at: z.string().optional().nullable()
});

// For content editor
const editorContent = ref<string>('');
const contentError = ref<string | null>(null);

// For image upload preview
const imagePreview = ref<string | null>(null);
const fileInputRef = ref<HTMLInputElement | null>(null);

// Ensure the form data is correctly initialized in the Create.vue component
const form = useForm({
    title: '',
    excerpt: '',
    category_id: '',
    content: '',
    featured_image: null as File | null,
    is_published: false as boolean,
    published_at: '' as string | null
});

// VeeValidate form for validation with matching types
const veeForm = useVeeForm({
    validationSchema: toFormValidator(formSchema),
    initialValues: {
        title: '',
        excerpt: '',
        category_id: '',
        content: '',
        featured_image: null,
        is_published: false,
        published_at: null as string | null
    }
});

// Sync veeForm values with Inertia form
watch(() => veeForm.values, (newValues) => {
    form.title = newValues.title || '';
    form.excerpt = newValues.excerpt || '';
    form.category_id = newValues.category_id || '';
    form.is_published = newValues.is_published || false;
    form.content = newValues.content || ''; // Sync content
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

// Computed property for published_at
const computedPublishedAt = computed({
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
    set(value: string) {
        form.published_at = value || null;
    }
});

// Validate content separately
const validateContent = () => {
    if (!editorContent.value) {
        contentError.value = 'Content is required';
        return false;
    }
    contentError.value = null;
    return true;
};

// Handle form submission with Inertia
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

    // Submit form using Inertia
    form.post('/posts', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            // Reset forms and states
            veeForm.resetForm();
            editorContent.value = '';
            imagePreview.value = null;
            contentError.value = null;
            router.visit('/posts');
        },
        onError: (errors: Errors) => {
            console.error('Submission errors:', errors);

            // Map Inertia errors to VeeValidate
            Object.keys(errors).forEach(key => {
                if (key === 'content') {
                    contentError.value = errors[key];
                } else if (key in veeForm.values) {
                    veeForm.setFieldError(key as keyof typeof veeForm.values, errors[key]);
                }
            });
        }
    });
};

</script>

<template>
    <Head title="Create Post" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container py-6">
            <Card>
                <CardHeader>
                    <CardTitle>Create New Post</CardTitle>
                    <CardDescription>Share your knowledge about programming, frameworks, networking, or devops</CardDescription>
                </CardHeader>

                <Form @submit="onSubmit">
                    <CardContent class="space-y-6">
                        <!-- Title -->
                        <FormField
                            name="title"
                            :value="veeForm.values.title"
                            @update:value="(val) => { veeForm.setFieldValue('title', val); form.title = val; }"
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
                            @update:value="(val) => { veeForm.setFieldValue('category_id', val); form.category_id = val; }"
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
                            @update:value="(val) => { veeForm.setFieldValue('excerpt', val); form.excerpt = val; }"
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
                            @update:value="veeForm.setFieldValue('featured_image', $event)"
                        >
                            <FormItem>
                                <FormLabel>Featured Image</FormLabel>
                                <FormControl>
                                    <div
                                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center cursor-pointer hover:border-primary"
                                        @click="triggerFileInput"
                                    >
                                        <ImagePlus class="h-10 w-10 text-gray-400" />
                                        <p class="mt-2 text-sm text-gray-500">Click to upload an image</p>
                                        <input
                                            type="file"
                                            ref="fileInputRef"
                                            accept="image/*"
                                            class="hidden"
                                            @change="handleImageChange"
                                        />
                                    </div>
                                    <div v-if="imagePreview" class="mt-2">
                                        <img :src="imagePreview" alt="Preview" class="max-h-40 rounded-md" />
                                    </div>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <!-- Content Editor -->
                        <div class="space-y-2">
                            <Label for="content">Content</Label>
                            <Editor
                                id="content"
                                v-model="editorContent"
                                editorStyle="min-height: 320px"
                                class="border rounded-md w-full"
                                :readonly="false"
                                @text-change="form.content = editorContent"
                                @blur="validateContent"
                            />
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
                            @update:value="(val) => { veeForm.setFieldValue('is_published', val); form.is_published = val; }"
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
                                        v-model="computedPublishedAt"
                            name="published_at"
                            :value="veeForm.values.published_at"
                            @update:value="(val) => { veeForm.setFieldValue('published_at', val); form.published_at = val || null; }"
                        >
                            <FormItem>
                                <FormLabel>Schedule Publication</FormLabel>
                                <FormControl>
                                    <Input
                                        type="datetime-local"
                                        v-model="computedPublishedAt"
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
                            {{ form.processing ? 'Saving...' : 'Save Post' }}
                        </Button>
                    </CardFooter>
                </Form>
            </Card>
        </div>
    </AppLayout>
</template>

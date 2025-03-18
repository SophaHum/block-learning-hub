<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle
} from '@/components/ui/card';
import { Calendar, User, Clock, Tag } from 'lucide-vue-next';

// Define props
defineProps<{
    post: {
        id: number;
        title: string;
        slug: string;
        excerpt: string;
        content: string;
        featured_image: string | null;
        category: {
            id: number;
            name: string;
        };
        user: {
            id: number;
            name: string;
        };
        published_at: string;
        created_at: string;
    };
    relatedPosts?: Array<{
        id: number;
        title: string;
        slug: string;
        excerpt: string;
        featured_image: string | null;
        category: {
            id: number;
            name: string;
        };
        published_at: string;
    }>;
}>();

// Format date for display
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Generate breadcrumbs dynamically based on post title
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Blog',
        href: '/blog',
    },
    {
        title: 'Post', // Will be replaced by post title
        href: '#',
    },
];
</script>

<template>
    <Head :title="post.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container py-8">
            <div class="grid grid-cols-12 gap-8">
                <!-- Main Content -->
                <div class="col-span-12 lg:col-span-8">
                    <!-- Post Header -->
                    <div class="mb-8">
                        <Link
                            :href="`/blog?category=${post.category.id}`"
                            class="inline-block mb-4"
                        >
                            <Badge class="px-3 py-1 text-sm">{{ post.category.name }}</Badge>
                        </Link>
                        <h1 class="text-4xl font-bold mb-4">{{ post.title }}</h1>

                        <div class="flex flex-wrap items-center gap-6 text-muted-foreground mb-6">
                            <div class="flex items-center gap-1.5">
                                <User class="w-4 h-4" />
                                <span>{{ post.user.name }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <Calendar class="w-4 h-4" />
                                <span>{{ formatDate(post.published_at) }}</span>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div v-if="post.featured_image" class="mb-8 rounded-lg overflow-hidden">
                            <img
                                :src="post.featured_image"
                                :alt="post.title"
                                class="w-full h-auto object-cover"
                            />
                        </div>
                    </div>

                    <!-- Post Content -->
                    <div class="prose prose-lg max-w-none dark:prose-invert mb-8" v-html="post.content"></div>

                    <!-- Tags or Category Links -->
                    <div class="flex items-center gap-2 py-6 border-t border-b">
                        <Tag class="w-4 h-4 text-muted-foreground" />
                        <span class="text-sm text-muted-foreground">Category:</span>
                        <Link :href="`/blog?category=${post.category.id}`">
                            <Badge variant="outline">{{ post.category.name }}</Badge>
                        </Link>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-span-12 lg:col-span-4">
                    <!-- Author Info -->
                    <Card class="mb-8">
                        <CardHeader>
                            <CardTitle>About the Author</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="flex items-center gap-4">
                                <div class="rounded-full bg-primary/10 w-12 h-12 flex items-center justify-center">
                                    <User class="w-6 h-6 text-primary" />
                                </div>
                                <div>
                                    <h3 class="font-medium">{{ post.user.name }}</h3>
                                    <p class="text-sm text-muted-foreground">Author</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Related Posts -->
                    <Card v-if="relatedPosts && relatedPosts.length">
                        <CardHeader>
                            <CardTitle>Related Posts</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div
                                v-for="relatedPost in relatedPosts"
                                :key="relatedPost.id"
                                class="group"
                            >
                                <Link :href="`/blog/${relatedPost.slug}`" class="flex gap-4 items-start">
                                    <div
                                        v-if="relatedPost.featured_image"
                                        class="flex-shrink-0 w-16 h-16 rounded-md overflow-hidden"
                                    >
                                        <img
                                            :src="relatedPost.featured_image"
                                            :alt="relatedPost.title"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                        />
                                    </div>
                                    <div class="flex-grow">
                                        <h4 class="font-medium line-clamp-2 group-hover:text-primary transition-colors">
                                            {{ relatedPost.title }}
                                        </h4>
                                        <div class="flex items-center gap-2 mt-1 text-xs text-muted-foreground">
                                            <Clock class="w-3 h-3" />
                                            <span>{{ formatDate(relatedPost.published_at) }}</span>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </CardContent>
                        <CardFooter>
                            <Link href="/blog" class="w-full">
                                <Button variant="outline" class="w-full">View All Posts</Button>
                            </Link>
                        </CardFooter>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
/* Add styles for the rendered content from the WYSIWYG editor */
.prose img {
    border-radius: 0.5rem;
}

.prose pre {
    background-color: rgb(var(--muted));
    border-radius: 0.5rem;
    padding: 1rem;
    overflow-x: auto;
}

.prose code {
    background-color: rgb(var(--muted));
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.9em;
}

.prose blockquote {
    border-left: 4px solid rgb(var(--primary));
    padding-left: 1rem;
    font-style: italic;
    color: rgb(var(--muted-foreground));
}
</style>

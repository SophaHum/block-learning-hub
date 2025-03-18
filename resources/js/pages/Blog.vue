<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button'
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { Input } from '@/components/ui/input'
import { Search } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Blog',
        href: '/blog',
    },
];

defineProps<{
    posts?: Array<{
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
    }>;
    categories?: Array<{
        id: number;
        name: string;
    }>;
}>();
</script>
<template>
    <Head title="Blog" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="grid grid-cols-12 gap-6 py-6">
            <!-- Sidebar with Categories -->
            <div class="col-span-12 md:col-span-3">
                <Card>
                    <CardHeader>
                        <CardTitle>Categories</CardTitle>
                        <CardDescription>Browse posts by category</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div v-if="categories && categories.length" class="space-y-2">
                                <Link
                                    v-for="category in categories"
                                    :key="category.id"
                                    :href="`/blog?category=${category.id}`"
                                    class="block p-2 hover:bg-muted rounded-md transition-colors"
                                >
                                    {{ category.name }}
                                </Link>
                            </div>
                            <div v-else class="text-muted-foreground">
                                No categories found
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="mt-6">
                    <CardHeader>
                        <CardTitle>Search</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="relative">
                            <Search class="absolute left-2 top-3 h-4 w-4 text-muted-foreground" />
                            <Input
                                placeholder="Search posts..."
                                class="pl-8"
                            />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Blog Posts -->
            <div class="col-span-12 md:col-span-9 space-y-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-bold">Latest Posts</h1>
                </div>

                <Separator />

                <div v-if="posts && posts.length" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <Card v-for="post in posts" :key="post.id" class="overflow-hidden flex flex-col">
                        <div v-if="post.featured_image" class="aspect-video overflow-hidden">
                            <img
                                :src="post.featured_image"
                                :alt="post.title"
                                class="h-full w-full object-cover transition-transform hover:scale-105"
                            />
                        </div>

                        <CardHeader>
                            <div class="flex items-center gap-2">
                                <Badge>{{ post.category.name }}</Badge>
                                <span class="text-xs text-muted-foreground">
                                    {{ new Date(post.published_at).toLocaleDateString() }}
                                </span>
                            </div>
                            <CardTitle class="line-clamp-2">
                                <Link :href="`/blog/${post.slug}`" class="hover:underline">
                                    {{ post.title }}
                                </Link>
                            </CardTitle>
                        </CardHeader>

                        <CardContent class="flex-grow">
                            <p class="text-muted-foreground line-clamp-3">{{ post.excerpt }}</p>
                        </CardContent>

                        <CardFooter class="flex justify-between">
                            <div class="text-sm text-muted-foreground">By {{ post.user.name }}</div>
                            <Link :href="`/blog/${post.slug}`">
                                <Button variant="outline" size="sm">Read More</Button>
                            </Link>
                        </CardFooter>
                    </Card>
                </div>

                <div v-else class="text-center py-12">
                    <h3 class="text-xl font-semibold mb-2">No posts yet</h3>
                    <p class="text-muted-foreground">
                        Check back later for articles about programming, frameworks, networking, and devops.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlogController extends Controller
{
    /**
     * Display the blog homepage with posts.
     */
    public function index(Request $request)
    {
        // Get category filter if provided
        $categoryId = $request->query('category');

        // Base query for posts that are published
        $query = Post::with(['user', 'category'])
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        // Apply category filter if specified
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Get posts with pagination
        $posts = $query->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        // Get all categories for the sidebar
        $categories = Category::all();

        return Inertia::render('Blog', [
            'posts' => $posts,
            'categories' => $categories,
            'filters' => [
                'category' => $categoryId,
            ],
        ]);
    }

    /**
     * Display a single blog post.
     */
    public function show($slug)
    {
        $post = Post::with(['user', 'category'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        // Get related posts (same category)
        $relatedPosts = Post::with(['user', 'category'])
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->limit(3)
            ->get();

        return Inertia::render('Blog/Show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}

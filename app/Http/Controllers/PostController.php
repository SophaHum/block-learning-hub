<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Post;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth')->except(['index', 'show']);
        // $this->middleware('can:create posts')->only(['create', 'store']);
        // $this->middleware('can:edit posts')->only(['edit', 'update']);
        // $this->middleware('can:delete posts')->only(['destroy']);
    }

    public function index()
    {
        // Return the posts with pagination to match the expected structure in the Vue component
        $posts = Post::with(['user', 'category'])
            ->orderBy('id', 'desc')
            ->paginate(10);
        // Log the structure to verify it has meta and links properties
        // Log::info('Posts structure:', [
        //     'has_meta' => isset($posts->meta),
        //     'has_links' => isset($posts->links),
        //     'structure' => [
        //         'current_page' => $posts->currentPage(),
        //         'per_page' => $posts->perPage(),
        //         'total' => $posts->total(),
        //     ]
        // ]);

        return Inertia::render('Posts/Index',[
            'posts' => $posts,
            // Comment out the permissions for now to simplify debugging
            // 'can' => [
            //     'create_posts' => Auth::user()->can('create posts'),
            //     'edit_posts' => Auth::user()->can('edit posts'),
            //     'delete_posts' => Auth::user()->can('delete posts'),
            // ]
        ]);
    }

    public function create()
    {
        $categories = Category::all();

        return Inertia::render('Posts/Create',[
            'categories' => $categories,
            // 'can' => [
            //     'create_posts' => Auth::user()->can('create posts'),
            // ]
        ]);
    }

    // Ensure the form data is correctly passed in the PostController
    public function store(Request $request)
    {
        try {
            Log::info('Post store method called with data:', $request->all());

            $validated = $request->validate([
                'title' => 'required',
                'excerpt' => 'required',
                'content' => 'required',
                'featured_image' => 'nullable|image|max:2048',
                'category_id' => 'required|exists:categories,id',
                'is_published' => 'boolean',
                'published_at' => 'nullable|date'
            ]);

            // Generate slug from title
            $slug = Str::slug($validated['title']);
            $baseSlug = $slug;

            // Check if slug already exists
            $count = 1;
            while (Post::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }

            // Handle featured image upload
            $imagePath = null;
            if ($request->hasFile('featured_image')) {
                $imagePath = $request->file('featured_image')->store('posts', 'public');
            }

            // Set published_at date based on is_published setting
            $publishedAt = null;
            if (isset($validated['is_published']) && $validated['is_published']) {
                $publishedAt = now();
            } elseif (isset($validated['published_at']) && $validated['published_at']) {
                $publishedAt = $validated['published_at'];
            }

            $post = Post::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'slug' => $slug,
                'excerpt' => $validated['excerpt'],
                'content' => $validated['content'],
                'featured_image' => $imagePath ? asset('storage/' . $imagePath) : null,
                'category_id' => $validated['category_id'],
                'is_published' => $validated['is_published'] ?? false,
                'published_at' => $publishedAt,
            ]);

            return redirect()->route('posts.index')->with('success', 'Post created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating post:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to create post: ' . $e->getMessage()]);
        }
    }

    public function show(Post $post)
    {
        $post->load(['user', 'category']);

        return Inertia::render('Posts/Show',[
            'post' => $post,
            'can' => [
                'edit_posts' => Auth::user()->can('edit posts'),
                'delete_posts' => Auth::user()->can('delete posts'),
            ]
        ]);
    }

    public function edit(Post $post)
    {
        // This line was fetching Post models instead of Category models
        $categories = Category::all();

        Log::info('Editing post:', [
            'post_id' => $post->id,
            'title' => $post->title,
            'content' => $post->content, // Log to verify content is being passed
            'category_id' => $post->category_id
        ]);

        return Inertia::render('Posts/Edit',[
            'post' => $post,
            'categories' => $categories,
            // 'can' => [
            //     'edit_posts' => Auth::user()->can('edit posts'),
            // ]
        ]);
    }

    // Add logging to the update method to verify data received
    public function update(Request $request, Post $post)
    {
        Log::info('Post update method called with data:', $request->all());

        // Check if we're receiving the form data properly
        if ($request->missing(['title', 'excerpt', 'content', 'category_id'])) {
            Log::error('Missing required fields in update request', [
                'received_data' => $request->all(),
                'post_id' => $post->id
            ]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date'
        ]);

        // Handle slug
        $newSlug = Str::slug($validated['title']);
        if ($post->slug !== $newSlug) {
            $baseSlug = $newSlug;
            $count = 1;
            while (Post::where('slug', $newSlug)->where('id', '!=', $post->id)->exists()) {
                $newSlug = $baseSlug . '-' . $count++;
            }
            $validated['slug'] = $newSlug;
        }

        // Handle featured image update
        if ($request->hasFile('featured_image')) {
            // Delete the old image if it exists
            if ($post->featured_image) {
                $oldPath = str_replace(asset('storage/'), '', $post->featured_image);
                Storage::disk('public')->delete($oldPath);
            }

            // Store the new image
            $imagePath = $request->file('featured_image')->store('posts', 'public');
            $validated['featured_image'] = asset('storage/' . $imagePath);
        }

        // Set published_at date based on is_published setting
        if (isset($validated['is_published'])) {
            if ($validated['is_published'] && !$post->published_at) {
                $validated['published_at'] = now();
            } elseif (!$validated['is_published']) {
                // If unpublishing, keep the scheduled date if set
                if (!isset($validated['published_at'])) {
                    $validated['published_at'] = null;
                }
            }
        }

        try {
            $post->update($validated);
            return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update post', [
                'post_id' => $post->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update post: ' . $e->getMessage()]);
        }
    }

    public function destroy(Post $post)
    {
        // Delete featured image if it exists
        if ($post->featured_image) {
            $imagePath = str_replace(asset('storage/'), '', $post->featured_image);
            Storage::disk('public')->delete($imagePath);
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class PostController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:views posts')->only(['index', 'show']);
        // $this->middleware('permission:creates posts')->only(['create', 'store']);
        // $this->middleware('permission:edits posts')->only(['edit', 'update']);
        // $this->middleware('permission:deletes posts')->only(['destroy']);
    }

    public function index()
    {
        $posts = Post::with(['user', 'category'])
        ->orderBy('id', 'desc')
        ->paginate(10);

        return Inertia::render('Posts/Index',[
            'posts' => $posts,
            'can' => [
                'create_posts' => Auth::user()->can('create posts'),
                'edit_posts' => Auth::user()->can('edit posts'),
                'delete_posts' => Auth::user()->can('delete posts'),
            ]
        ]);
    }
    public function create()
    {
        return Inertia::render('Posts/Create',[
            'can' => [
                'create_posts' => Auth::user()->can('create posts'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:1024',
            'category_id' => 'required|exists:categories,id',
            'published_at' => 'nullable|date'
        ]);

        //Generate slug
        $validated['slug'] = Str::slug($validated['title']);

        Post::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'featured_image' => $validated['featured_image'],
            'category_id' => $validated['category_id'],
            'published_at' => $validated['published_at'],
        ]);

        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }

    public function show(Post $post)
    {
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
        return Inertia::render('Posts/Edit',[
            'post' => $post,
            'can' => [
                'edit_posts' => Auth::user()->can('edit posts'),
            ]
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:1024',
            'category_id' => 'required|exists:categories,id',
            'published_at' => 'nullable|date'
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        $post->update($validated);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }
}

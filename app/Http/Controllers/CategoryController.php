<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        
    }
    public function index()
    {
        $categories = Category::withCount('posts')
        ->orderBy('id', 'desc')
        ->paginate(10);
        return Inertia::render('Categories/Index',[
            'categories' => $categories,
            'can' => [
                'create_categories' => Auth::user()->can('create categories'),
                'edit_categories' => Auth::user()->can('edit categories'),
                'delete_categories' => Auth::user()->can('delete categories'),
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Categories/Create',[
            'can' => [
                'create_categories' => Auth::user()->can('create categories'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string',
            'color' => 'required|string|max:6',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        $category->load([
            'posts' => function ($query) {
                $query->with('user')
                    ->where('published', true)
                    ->orderBy('published_at', 'desc');
            }
        ]);

        $posts = $category->posts()->where('published', true)
            ->with('user')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return Inertia::render('Categories/Show',[
            'category' => $category,
            'posts' => $posts,
            'can' => [
                'edit_categories' => Auth::user()->can('edit categories'),
                'delete_categories' => Auth::user()->can('delete categories'),
            ]
        ]);
    }
    public function edit(Category $category)
    {
        return Inertia::render('Categories/Edit',[
            'category' => $category,
            'can' => [
                'edit_categories' => Auth::user()->can('edit categories'),
            ]
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string',
            'color' => 'required|string|max:6',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}

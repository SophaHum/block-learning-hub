<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Models\Post;


class LandingController extends Controller
{
    public function __construct(){
       
    }

    public function index()
    {
        $featuredPost = Post::with(['user','category'])
        ->where('is_featured', 1)
        ->orderBy('published_at', 'desc')
        ->take(6)
        ->get();
        //Get all categories with their posts count
        $categories = Category::withCount(['posts' => function ($query) {
            $query->where('published', true);
        }])->get();
       
        return Inertia::render('Landings/Index',[
            'featured_post' => $featuredPost,
            'categories' => $categories,
            'can' => [
                'create_landings' => Auth::user()->can('create landings'),
                'edit_landings' => Auth::user()->can('edit landings'),
                'delete_landings' => Auth::user()->can('delete landings'),
            ]
        ]);
    }

    function blog(Request $request)
    {
        $categorySlug = $request->query('category');
        $query = Post::with(['user','category'])
        ->where('published', true)
        ->orderBy('published_at', 'desc');

        //Filter by category if provided
        if($categorySlug){
            $category = Category::where('slug', $categorySlug)->firstOrFail();
            $query->where('category_id', $category->id);
        }
        $post = $query->paginate(12);
        
        //Get all categories for the filter
        $categories = Category::withCount(['posts' => function ($query) {
            $query->where('published', true);
        }])->get();
        
        return Inertia::render('Landings/Blog',[
            'post' => $post,
            'categories' => $categories,
            'currentCategory' => $categorySlug
        ]);
    }

    public function post(string $slug)
    {
        $post = Post::with(['user','category'])
        ->where('slug', $slug)
        ->where('published', true)
        ->firstOrFail();

        //Get related posts the same categories
        $relatedPosts = Post::with(['user','category'])
        ->where('category_id', $post->category_id)
        ->where('slug', '!=', $post->slug)
        ->where('published', true)
        ->get();

        return Inertia::render('Landings/Post',[
            'post' => $post,
            'relatedPosts' => $relatedPosts
        ]);
    }
    
}

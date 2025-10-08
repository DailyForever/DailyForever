<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(10);
        return view('blog.index', compact('posts'));
    }

    /**
     * Display a listing of all posts for admin.
     */
    public function adminIndex()
    {
        $posts = Post::orderByDesc('id')->paginate(20);
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.posts.edit', ['post' => new Post()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'is_published' => 'nullable|boolean',
        ]);
        
        $slug = $data['slug'] ?: Str::slug($data['title']).'-'.substr(bin2hex(random_bytes(3)),0,6);
        $post = Post::create([
            'author_id' => $request->user()->id,
            'title' => $data['title'],
            'slug' => $slug,
            'excerpt' => $data['excerpt'] ?? null,
            'body' => $data['body'],
            'is_published' => (bool)($data['is_published'] ?? false),
            'published_at' => ($data['is_published'] ?? false) ? now() : null,
        ]);
        
        return redirect()->route('admin.posts.edit', $post->id);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->where('is_published', true)->firstOrFail();
        
        // Track blog view in analytics
        if (function_exists('gtag')) {
            gtag('event', 'blog_view', [
                'event_category' => 'content',
                'event_label' => $slug,
                'custom_parameter_1' => $post->title
            ]);
        }
        
        return view('blog.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'required|string|max:200',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'is_published' => 'nullable|boolean',
        ]);
        
        $post->fill($data);
        if (($data['is_published'] ?? false) && !$post->published_at) {
            $post->published_at = now();
        }
        if (!($data['is_published'] ?? false)) {
            $post->is_published = false;
        }
        $post->save();
        
        return back()->with('status', 'Saved');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index');
    }
}

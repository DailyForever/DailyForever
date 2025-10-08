@extends('layouts.app')

@section('title', ($post->id ? 'Edit Post' : 'New Post') . ' - DailyForever')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="content-card p-6 space-y-4">
        <h1 class="text-2xl font-medium">{{ $post->id ? 'Edit Post' : 'New Post' }}</h1>
        @if(session('status'))
            <div class="text-yt-success text-sm">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ $post->id ? route('admin.posts.update', $post) : route('admin.posts.store') }}" class="space-y-4">
            @csrf
            @if($post->id)
                @method('PUT')
            @endif
            <div>
                <label class="block text-sm mb-1">Title</label>
                <input name="title" value="{{ old('title', $post->title) }}" class="input-field w-full px-3 py-2" required />
            </div>
            <div>
                <label class="block text-sm mb-1">Slug</label>
                <input name="slug" value="{{ old('slug', $post->slug) }}" class="input-field w-full px-3 py-2" placeholder="auto if empty when creating" {{ $post->id ? 'required' : '' }} />
            </div>
            <div>
                <label class="block text-sm mb-1">Excerpt</label>
                <textarea name="excerpt" rows="3" class="input-field w-full px-3 py-2">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>
            <div>
                <label class="block text-sm mb-1">Body (Markdown or HTML)</label>
                <textarea name="body" rows="14" class="input-field w-full px-3 py-2" required>{{ old('body', $post->body) }}</textarea>
            </div>
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_published" value="1" {{ old('is_published', $post->is_published) ? 'checked' : '' }} /> <span>Published</span></label>
                <button class="btn-primary px-4 py-2">Save</button>
                @if($post->id)
                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn-secondary px-3 py-2">Delete</button>
                </form>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection




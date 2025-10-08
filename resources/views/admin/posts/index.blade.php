@extends('layouts.app')

@section('title', 'Manage Posts - DailyForever')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="content-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-medium" data-i18n="admin.posts.index.title" data-i18n-doc-title="admin.posts.index.doc_title">Posts</h1>
            <a href="{{ route('admin.posts.create') }}" class="btn-primary px-4 py-2 text-sm" data-i18n="admin.posts.index.new_post">New Post</a>
        </div>
        <table class="w-full text-sm">
            <thead>
            <tr class="text-left text-yt-text-secondary">
                <th class="py-2" data-i18n="admin.posts.index.th.title">Title</th>
                <th data-i18n="admin.posts.index.th.slug">Slug</th>
                <th data-i18n="admin.posts.index.th.status">Status</th>
                <th data-i18n="admin.posts.index.th.updated">Updated</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($posts as $post)
                <tr class="border-t border-yt-border">
                    <td class="py-2">{{ $post->title }}</td>
                    <td>{{ $post->slug }}</td>
                    <td>@if($post->is_published)<span data-i18n="admin.posts.index.status.published">Published</span>@else<span data-i18n="admin.posts.index.status.draft">Draft</span>@endif</td>
                    <td>{{ $post->updated_at->diffForHumans() }}</td>
                    <td class="text-right">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-link" data-i18n="common.buttons.edit">Edit</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $posts->links() }}</div>
    </div>
</div>
@endsection




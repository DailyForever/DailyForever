@extends('layouts.app')

@section('title', 'My Files - DailyForever')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="content-card p-6">
        <h1 class="text-2xl font-medium mb-4" data-i18n="files.mine.title">My Files</h1>
        @if(session('status'))
            <div class="mb-3 text-sm text-yt-success">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-3 text-sm text-yt-error">{{ $errors->first() }}</div>
        @endif
        <table class="w-full text-sm">
            <thead>
            <tr class="text-left text-yt-text-secondary">
                <th class="py-2" data-i18n="files.mine.th.filename">Filename</th>
                <th data-i18n="files.mine.th.size">Size</th>
                <th data-i18n="files.mine.th.views">Views</th>
                <th data-i18n="files.mine.th.limit">Limit</th>
                <th data-i18n="files.mine.th.private">Private</th>
                <th class="text-right" data-i18n="files.mine.th.actions">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($files as $f)
                <tr class="border-t border-yt-border">
                    <td class="py-2">{{ $f->original_filename }}</td>
                    <td>{{ number_format($f->size_bytes/1024,1) }} KB</td>
                    <td>{{ $f->views }}</td>
                    <td>{{ $f->view_limit ?? '—' }}</td>
                    <td>
                        @if($f->is_private)
                            <span data-i18n="common.misc.yes">yes</span>
                        @else
                            <span data-i18n="common.misc.no">no</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="inline-flex items-center gap-3">
                            <a class="text-link" href="{{ route('files.show', $f->identifier) }}" data-i18n="common.buttons.open_link">Open</a>
                            <form method="POST" action="{{ route('files.destroy', $f->identifier) }}" onsubmit="return (window.I18N ? confirm(I18N.t('files.mine.confirm_delete')) : confirm('Delete this file? This cannot be undone.'));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-yt-error hover:underline" data-i18n="common.buttons.delete">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $files->links() }}</div>
    </div>
</div>
@endsection




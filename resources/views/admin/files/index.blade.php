@extends('layouts.app')

@section('title', 'Manage Files - DailyForever')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="content-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-medium" data-i18n="admin.files.index.title" data-i18n-doc-title="admin.files.index.doc_title">Files</h1>
        </div>
        <table class="w-full text-sm">
            <thead>
            <tr class="text-left text-yt-text-secondary">
                <th class="py-2" data-i18n="admin.files.index.th.id">ID</th>
                <th data-i18n="admin.files.index.th.owner">Owner</th>
                <th data-i18n="admin.files.index.th.filename">Filename</th>
                <th data-i18n="admin.files.index.th.size">Size</th>
                <th data-i18n="admin.files.index.th.views">Views</th>
                <th data-i18n="admin.files.index.th.limit">Limit</th>
                <th data-i18n="admin.files.index.th.private">Private</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($files as $f)
                <tr class="border-t border-yt-border">
                    <td class="py-2">{{ $f->id }}</td>
                    <td>
                        @if($f->user_id)
                            {{ $f->user_id }}
                        @else
                            <span data-i18n="admin.files.index.guest">guest</span>
                        @endif
                    </td>
                    <td class="font-mono">{{ $f->original_filename }}</td>
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
                    <td class="space-x-2">
                        <form method="POST" action="{{ route('admin.files.viewlimit', $f) }}" class="inline">
                            @csrf
                            <input type="number" name="view_limit" min="1" class="input-field px-2 py-1 text-xs w-20" data-i18n-attr="placeholder" data-i18n-placeholder="admin.files.index.limit_placeholder" placeholder="Limit" />
                            <button class="btn-secondary px-2 py-1 text-xs" data-i18n="admin.files.index.buttons.set">Set</button>
                        </form>
                        <form method="POST" action="{{ route('admin.files.delete', $f) }}" onsubmit="return confirm(window.I18N ? I18N.t('admin.files.index.confirm_delete') : 'Delete this file?')" class="inline">
                            @csrf
                            <button class="btn-secondary px-2 py-1 text-xs" data-i18n="admin.files.index.buttons.delete">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $files->links() }}</div>
    </div>
</div>
@endsection




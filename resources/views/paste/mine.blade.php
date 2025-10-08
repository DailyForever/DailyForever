@extends('layouts.app')

@section('title', 'My Pastes - DailyForever')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="content-card">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-medium text-yt-text" data-i18n="paste.mine.title" data-i18n-doc-title="paste.mine.doc_title">My Pastes</h1>
                <p class="text-yt-text-secondary text-sm mt-1" data-i18n="paste.mine.subtitle">Manage your encrypted pastes</p>
            </div>
            <a href="{{ route('paste.create') }}" class="btn-primary px-4 py-2 text-sm inline-flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span data-i18n="paste.mine.create">Create New Paste</span>
            </a>
        </div>
        <!-- Desktop Table -->
        <div class="hidden md:block table-responsive">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-yt-text-secondary border-b border-yt-border">
                        <th class="py-3 px-2 font-medium" data-i18n="paste.mine.th.id">ID</th>
                        <th class="py-3 px-2 font-medium" data-i18n="paste.mine.th.identifier">Identifier</th>
                        <th class="py-3 px-2 font-medium" data-i18n="paste.mine.th.private">Private</th>
                        <th class="py-3 px-2 font-medium" data-i18n="paste.mine.th.views">Views</th>
                        <th class="py-3 px-2 font-medium" data-i18n="paste.mine.th.limit">Limit</th>
                        <th class="py-3 px-2 font-medium" data-i18n="paste.mine.th.created">Created</th>
                        <th class="py-3 px-2 font-medium" data-i18n="paste.mine.th.actions">Actions</th>
                    </tr>
                </thead>
            <tbody>
                @forelse($pastes as $p)
                <tr class="border-t border-yt-border hover:bg-yt-surface/50 transition-colors duration-150">
                    <td class="py-3 px-2 text-yt-text-secondary">{{ $p->id }}</td>
                    <td class="py-3 px-2 font-mono text-xs text-yt-text">{{ $p->identifier }}</td>
                    <td class="py-3 px-2">
                        @if($p->is_private)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-orange-600 bg-orange-50 dark:bg-orange-900/20 rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <span data-i18n="paste.mine.private">Private</span>
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-600 bg-green-50 dark:bg-green-900/20 rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                </svg>
                                <span data-i18n="paste.mine.public">Public</span>
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-2 text-yt-text">{{ $p->views }}</td>
                    <td class="py-3 px-2 text-yt-text">{{ $p->view_limit ?? '—' }}</td>
                    <td class="py-3 px-2 text-yt-text-secondary">{{ $p->created_at->format('Y-m-d H:i') }}</td>
                    <td class="py-2">
                        <div class="flex items-center space-x-1">
                            <a href="{{ route('paste.show', $p->identifier) }}" 
                               class="inline-flex items-center px-2 py-1 text-xs font-medium text-yt-accent bg-yt-accent/10 hover:bg-yt-accent/20 rounded-md transition-colors duration-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span data-i18n="common.buttons.view">View</span>
                            </a>
                            <a href="{{ route('paste.edit', $p->identifier) }}" 
                               class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-md transition-colors duration-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>
                            <button onclick="deletePaste('{{ $p->identifier }}')" 
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-md transition-colors duration-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span data-i18n="common.buttons.delete">Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="w-16 h-16 bg-yt-surface rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-yt-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-yt-text mb-1" data-i18n="paste.mine.no_pastes">No pastes yet</h3>
                                <p class="text-yt-text-secondary text-sm" data-i18n="paste.mine.create_first_paste">Create your first paste to get started</p>
                            </div>
                            <a href="{{ route('paste.create') }}" class="btn-primary px-4 py-2 text-sm" data-i18n="paste.mine.create_new_paste">Create New Paste</a>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Cards -->
        <div class="md:hidden space-y-4">
            @forelse($pastes as $p)
            <div class="border border-yt-border rounded-lg p-4 space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="font-mono text-xs text-yt-text mb-1">{{ $p->identifier }}</div>
                        <div class="text-xs text-yt-text-secondary">ID: {{ $p->id }}</div>
                    </div>
                    @if($p->is_private)
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-orange-600 bg-orange-50 dark:bg-orange-900/20 rounded-full">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <span data-i18n="paste.mine.private">Private</span>
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-600 bg-green-50 dark:bg-green-900/20 rounded-full">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            <span data-i18n="paste.mine.public">Public</span>
                        </span>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-yt-text-secondary" data-i18n="paste.mine.th.views">Views:</span>
                        <span class="text-yt-text ml-1">{{ $p->views }}</span>
                    </div>
                    <div>
                        <span class="text-yt-text-secondary" data-i18n="paste.mine.th.limit">Limit:</span>
                        <span class="text-yt-text ml-1">{{ $p->view_limit ?? '—' }}</span>
                    </div>
                </div>
                <div class="text-xs text-yt-text-secondary">
                    <span data-i18n="paste.mine.th.created">Created:</span> {{ $p->created_at->format('Y-m-d H:i') }}
                </div>
                <div class="flex flex-wrap gap-2 pt-2">
                    <a href="{{ route('paste.show', $p->identifier) }}" 
                       class="flex-1 inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-yt-accent bg-yt-accent/10 hover:bg-yt-accent/20 rounded-md transition-colors duration-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span data-i18n="common.buttons.view">View</span>
                    </a>
                    <a href="{{ route('paste.edit', $p->identifier) }}" 
                       class="flex-1 inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-blue-600 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-md transition-colors duration-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <button onclick="deletePaste('{{ $p->identifier }}')" 
                            class="flex-1 inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-red-600 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-md transition-colors duration-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span data-i18n="common.buttons.delete">Delete</span>
                    </button>
                </div>
            </div>
            @empty
            <div class="py-12 text-center">
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-16 h-16 bg-yt-surface rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-yt-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-yt-text mb-1" data-i18n="paste.mine.no_pastes">No pastes yet</h3>
                        <p class="text-yt-text-secondary text-sm" data-i18n="paste.mine.create_first_paste">Create your first paste to get started</p>
                    </div>
                    <a href="{{ route('paste.create') }}" class="btn-primary px-4 py-2 text-sm" data-i18n="paste.mine.create_new_paste">Create New Paste</a>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
async function deletePaste(identifier) {
    const q = (window.I18N ? I18N.t('paste.mine.confirm_delete_paste') : 'Are you sure you want to delete this paste? This action cannot be undone.');
    if (!confirm(q)) {
        return;
    }

    try {
        const response = await fetch(`/paste/${identifier}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Remove the row from the table
            const row = event.target.closest('tr');
            row.remove();
            
            // Show success message
            const successMsg = window.I18N ? I18N.t('paste.mine.delete_success') : 'Paste deleted successfully!';
            alert(successMsg);
        } else {
            const errorLabel = window.I18N ? I18N.t('paste.mine.delete_error_prefix') : 'Error deleting paste:';
            const errorDetail = data.error || (window.I18N ? I18N.t('paste.mine.delete_error_generic') : 'Unknown error');
            alert(`${errorLabel} ${errorDetail}`);
        }
    } catch (error) {
        console.error('Error:', error);
        const retryMsg = window.I18N ? I18N.t('paste.mine.delete_error_retry') : 'Error deleting paste. Please try again.';
        alert(retryMsg);
    }
}
</script>
@endsection


